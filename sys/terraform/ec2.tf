data "aws_ssm_parameter" "bottlerocket_ami" {
  name = "/aws/service/bottlerocket/aws-ecs-2/arm64/latest/image_id"
}

data "aws_ec2_managed_prefix_list" "cloudfront" {
  name = "com.amazonaws.global.cloudfront.origin-facing"
}

# Stable address for CloudFront to reach, reattached to every new instance by
# the eip-bootstrap container. Also the fixed egress IP towards GitHub/GitLab/Packagist.
resource "aws_eip" "origin" {
  domain = "vpc"
}

resource "aws_security_group" "sgec2" {
  description = "${var.service_name}-ec2"
  name        = "${var.service_name}-ec2"
  egress {
    cidr_blocks = ["0.0.0.0/0"]
    protocol    = "-1"
    from_port   = 0
    to_port     = 0
  }
  vpc_id = var.vpc_id
}

resource "aws_security_group_rule" "sgec2_ingress_cloudfront" {
  type              = "ingress"
  security_group_id = aws_security_group.sgec2.id
  prefix_list_ids   = [data.aws_ec2_managed_prefix_list.cloudfront.id]
  from_port         = 80
  protocol          = "tcp"
  to_port           = 80
}

resource "aws_iam_role" "ec2_instance" {
  name = "${var.service_name}-ec2-instance"

  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Action    = "sts:AssumeRole"
        Effect    = "Allow"
        Principal = { Service = "ec2.amazonaws.com" }
      },
    ]
  })
}

resource "aws_iam_role_policy_attachment" "ec2_instance_ecs" {
  role       = aws_iam_role.ec2_instance.name
  policy_arn = "arn:aws:iam::aws:policy/service-role/AmazonEC2ContainerServiceforEC2Role"
}

# Lets you open a shell on the Bottlerocket control container through SSM.
resource "aws_iam_role_policy_attachment" "ec2_instance_ssm" {
  role       = aws_iam_role.ec2_instance.name
  policy_arn = "arn:aws:iam::aws:policy/AmazonSSMManagedInstanceCore"
}

resource "aws_iam_role_policy" "ec2_instance_eip" {
  name = "associate-origin-eip"
  role = aws_iam_role.ec2_instance.id

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Effect = "Allow"
        Action = "ec2:AssociateAddress"
        Resource = [
          "arn:aws:ec2:${data.aws_region.current.name}:${data.aws_caller_identity.current.account_id}:elastic-ip/${aws_eip.origin.allocation_id}",
          "arn:aws:ec2:${data.aws_region.current.name}:${data.aws_caller_identity.current.account_id}:instance/*",
          "arn:aws:ec2:${data.aws_region.current.name}:${data.aws_caller_identity.current.account_id}:network-interface/*",
        ]
      },
    ]
  })
}

resource "aws_iam_instance_profile" "ec2_instance" {
  name = "${var.service_name}-ec2-instance"
  role = aws_iam_role.ec2_instance.name
}

resource "aws_launch_template" "ecs" {
  name     = "${var.service_name}-ecs"
  image_id = data.aws_ssm_parameter.bottlerocket_ami.value

  iam_instance_profile {
    arn = aws_iam_instance_profile.ec2_instance.arn
  }

  # Auto-assigned public IP gives the box internet access until the EIP is attached.
  network_interfaces {
    associate_public_ip_address = true
    security_groups             = [aws_security_group.sgec2.id]
  }

  metadata_options {
    http_endpoint               = "enabled"
    http_tokens                 = "required"
    http_put_response_hop_limit = 1
  }

  # Bottlerocket settings (TOML). Image pruning is done by the ECS agent and
  # container logs go to CloudWatch via awslogs, so the host needs no cron/logrotate.
  user_data = base64encode(<<-EOT
    [settings.ecs]
    cluster = "${aws_ecs_cluster.ecscluster.name}"
    enable-spot-instance-draining = true
    image-cleanup-enabled = true
    image-cleanup-wait = "1h"
    image-cleanup-age = "1h"

    [settings.bootstrap-containers.eip]
    source = "${data.aws_caller_identity.current.account_id}.dkr.ecr.${data.aws_region.current.name}.amazonaws.com/${var.service_name}:eip-bootstrap"
    mode = "always"
    essential = false
    user-data = "${base64encode(aws_eip.origin.allocation_id)}"
  EOT
  )

  tag_specifications {
    resource_type = "instance"
    tags = {
      Name = "${var.service_name}-ecs"
    }
  }
}

resource "aws_autoscaling_group" "ecs" {
  name                = "${var.service_name}-ecs"
  min_size            = 1
  max_size            = 1
  desired_capacity    = 1
  vpc_zone_identifier = var.subnets
  capacity_rebalance  = true

  mixed_instances_policy {
    instances_distribution {
      on_demand_base_capacity                  = 0
      on_demand_percentage_above_base_capacity = 0
      spot_allocation_strategy                 = "price-capacity-optimized"
    }

    launch_template {
      launch_template_specification {
        launch_template_id = aws_launch_template.ecs.id
        version            = aws_launch_template.ecs.latest_version
      }

      override {
        instance_type = "c7g.medium"
      }
      override {
        instance_type = "c6g.medium"
      }
      override {
        instance_type = "m7g.medium"
      }
      override {
        instance_type = "m6g.medium"
      }
    }
  }

  # A new launch template version (e.g. new Bottlerocket AMI) replaces the instance.
  # With a single instance this means a few minutes of origin downtime, covered by CloudFront.
  instance_refresh {
    strategy = "Rolling"
    preferences {
      min_healthy_percentage = 0
    }
  }

  tag {
    key                 = "AmazonECSManaged"
    value               = "true"
    propagate_at_launch = true
  }
}

resource "aws_ecs_capacity_provider" "ec2" {
  name = "${var.service_name}-ec2"

  auto_scaling_group_provider {
    auto_scaling_group_arn         = aws_autoscaling_group.ecs.arn
    managed_termination_protection = "DISABLED"

    managed_scaling {
      status = "DISABLED"
    }
  }
}

# FARGATE providers stay attached until the old Fargate service is removed.
resource "aws_ecs_cluster_capacity_providers" "cluster" {
  cluster_name       = aws_ecs_cluster.ecscluster.name
  capacity_providers = ["FARGATE", "FARGATE_SPOT", aws_ecs_capacity_provider.ec2.name]
}
