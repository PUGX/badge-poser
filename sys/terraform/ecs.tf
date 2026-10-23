locals {
  container_definitions = templatefile("ecs/task-definition.json", {
    account_id                = data.aws_caller_identity.current.account_id
    aws_region                = data.aws_region.current.name
    service_name              = var.service_name
    ecr_image_tag_nginx       = var.ecr_image_tag_nginx
    ecr_image_tag_php         = var.ecr_image_tag_php
    cloudwatch_log_group      = aws_cloudwatch_log_group.cloudwatchloggroup.name
    env_app_debug             = var.env_app_debug
    env_app_env               = var.env_app_env
    env_app_xdebug            = var.env_app_xdebug
    env_app_xdebug_host       = var.env_app_xdebug_host
    env_bitbucket_auth_method = var.env_bitbucket_auth_method
    env_github_auth_method    = var.env_github_auth_method
    env_phpfpm_host           = var.env_phpfpm_host
    env_redis_host            = aws_elasticache_cluster.rediscluster.cache_nodes[0].address
    env_resolver_ip           = var.env_resolver_ip
    env_sentry_dsn            = var.env_sentry_dsn
    env_trusted_proxies       = var.env_trusted_proxies
  })
}

resource "aws_security_group" "sgecs" {
  description = "${var.service_name}-ecs"
  name        = "${var.service_name}-ecs"
  egress {
    cidr_blocks = ["0.0.0.0/0"]
    protocol    = "-1"
    from_port   = 0
    to_port     = 0
  }
  vpc_id = var.vpc_id
}

resource "aws_security_group_rule" "sgecs_ingress_http" {
  type              = "ingress"
  security_group_id = aws_security_group.sgecs.id
  cidr_blocks       = ["0.0.0.0/0"]
  from_port         = 80
  protocol          = "tcp"
  to_port           = 80
}
resource "aws_security_group_rule" "sgecs_ingress_https" {
  type              = "ingress"
  security_group_id = aws_security_group.sgecs.id
  cidr_blocks       = ["0.0.0.0/0"]
  from_port         = 443
  protocol          = "tcp"
  to_port           = 443
}

resource "aws_cloudwatch_log_group" "cloudwatchloggroup" {
  name              = "${var.service_name}-logs"
  retention_in_days = 14
}

resource "aws_ecs_cluster" "ecscluster" {
  name = "${var.service_name}-cluster-${var.environment}"
  setting {
    name  = "containerInsights"
    value = "disabled"
  }
}

resource "aws_ecs_service" "ecsservice" {
  cluster                           = aws_ecs_cluster.ecscluster.arn
  desired_count                     = 1
  health_check_grace_period_seconds = 15

  capacity_provider_strategy {
    base              = 0
    capacity_provider = "FARGATE_SPOT"
    weight            = 2
  }
  capacity_provider_strategy {
    base              = 1
    capacity_provider = "FARGATE"
    weight            = 1
  }

  load_balancer {
    container_name   = "nginx"
    container_port   = 80
    target_group_arn = aws_lb_target_group.elbtargetgroup.id
  }
  name            = var.service_name
  task_definition = "${var.service_name}:${aws_ecs_task_definition.ecstask.revision}"
  network_configuration {
    assign_public_ip = true
    security_groups  = [aws_security_group.sgecs.id]
    subnets          = var.subnets
  }
}

resource "aws_ecs_task_definition" "ecstask" {
  execution_role_arn    = aws_iam_role.ecs_task_role.arn
  container_definitions = local.container_definitions
  memory                = "2048"
  family                = var.service_name
  requires_compatibilities = [
    "FARGATE"
  ]
  network_mode = "awsvpc"
  cpu          = "1024"
  skip_destroy = true
}

# Bottlerocket/EC2 variant of the service. Host networking: nginx and php-fpm talk
# over 127.0.0.1 and egress uses the instance Elastic IP (awsvpc tasks on EC2 get
# no public IP and would need a NAT gateway).
resource "aws_ecs_task_definition" "ec2" {
  family                   = "${var.service_name}-ec2"
  execution_role_arn       = aws_iam_role.ecs_task_role.arn
  container_definitions    = local.container_definitions
  requires_compatibilities = ["EC2"]
  network_mode             = "host"
  cpu                      = "1024"
  # Fits the 2 GB instance types after Bottlerocket and the ECS agent.
  memory = "1536"

  runtime_platform {
    operating_system_family = "LINUX"
    cpu_architecture        = "ARM64"
  }
}

resource "aws_ecs_service" "ec2" {
  name            = "${var.service_name}-ec2"
  cluster         = aws_ecs_cluster.ecscluster.arn
  task_definition = aws_ecs_task_definition.ec2.arn
  desired_count   = 1

  # One instance and fixed host ports: the old task must stop before the new one starts.
  deployment_minimum_healthy_percent = 0
  deployment_maximum_percent         = 100

  capacity_provider_strategy {
    capacity_provider = aws_ecs_capacity_provider.ec2.name
    base              = 1
    weight            = 1
  }

  depends_on = [aws_ecs_cluster_capacity_providers.cluster]
}
