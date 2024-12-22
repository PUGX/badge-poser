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
  tags = {
    env = var.service_name
  }
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
  // CF Property(CapacityProviders) = [
  //   "FARGATE",
  //   "FARGATE_SPOT"
  // ]
  setting {
    name  = "containerInsights"
    value = "disabled"
  }
  tags = {
    env = var.service_name
  }
}

resource "aws_ecs_service" "ecsservice" {
  cluster                           = aws_ecs_cluster.ecscluster.arn
  desired_count                     = 1
  health_check_grace_period_seconds = 15
  #   launch_type                       = "FARGATE"


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
  tags = {
    env = var.service_name
  }
}

resource "aws_ecs_task_definition" "ecstask" {
  execution_role_arn = var.exec_role_arn
  container_definitions = jsonencode([
    {
      name = "phpfpm"
      portMappings = [
        {
          hostPort      = 9000
          protocol      = "tcp"
          containerPort = 9000
        }
      ]
      command               = []
      credentialSpecs       = []
      dnsSearchDomains      = []
      dnsServers            = []
      dockerLabels          = {}
      dockerSecurityOptions = []
      entryPoint            = []
      environment = [
        {
          name  = "APP_ENV"
          value = var.env_appenv
        },
        {
          name  = "APP_DEBUG"
          value = var.env_appdebug
        },
        {
          name  = "APP_SECRET"
          value = var.env_appsecret
        },
        {
          name  = "APP_XDEBUG"
          value = var.env_appxdebug
        },
        {
          name  = "APP_XDEBUG_HOST"
          value = var.env_appxdebughost
        },
        {
          name  = "REDIS_HOST"
          value = var.env_redishost
        },
        {
          name  = "GITHUB_AUTH_METHOD"
          value = var.env_githubauthmethod
        },
        {
          name  = "GITHUB_USERNAME"
          value = var.env_githubusername
        },
        {
          name  = "GITHUB_SECRET"
          value = var.env_githubsecret
        },
        {
          name  = "CIRCLE_CI_TOKEN"
          value = var.env_circlecitoken
        },
        {
          name  = "SENTRY_DSN"
          value = var.env_sentrydsn
        },
        {
          name  = "BITBUCKET_AUTH_METHOD"
          value = var.env_bitbucketauthmethod
        },
        {
          name  = "BITBUCKET_SECRET"
          value = var.env_bitbucketsecret
        },
        {
          name  = "BITBUCKET_TOKEN"
          value = var.env_bitbuckettoken
        },
        {
          name  = "TRUSTED_PROXIES"
          value = var.env_trustedproxies
        },
        {
          name  = "GITLAB_TOKEN"
          value = var.env_gitlabtoken
        }
      ]
      image            = "${data.aws_caller_identity.current.account_id}.dkr.ecr.${data.aws_region.current.name}.amazonaws.com/${var.service_name}:phpfpm-${var.ecr_image_tag_php}"
      essential        = true
      environmentFiles = []
      extraHosts       = []
      links            = []
      mountPoints      = []
      secrets          = []
      systemControls   = []
      ulimits          = []
      volumesFrom      = []
      logConfiguration = {
        logDriver = "awslogs"
        options = {
          awslogs-group         = aws_cloudwatch_log_group.cloudwatchloggroup.name
          awslogs-region        = data.aws_region.current.name
          awslogs-stream-prefix = "${var.service_name}-phpfpm"
        }
        secretOptions = []
      }
    },
    {
      name = "nginx"

      dependsOn = [
        {

          condition     = "START"
          containerName = "phpfpm"
        }
      ]
      command               = []
      credentialSpecs       = []
      dnsSearchDomains      = []
      dnsServers            = []
      dockerLabels          = {}
      dockerSecurityOptions = []
      entryPoint            = []
      portMappings = [
        {
          hostPort      = 80
          protocol      = "tcp"
          containerPort = 80
        }
      ]
      environment = [
        {
          name  = "PHPFPM_HOST"
          value = var.env_phpfpmhost
        },
        {
          name  = "REDIS_HOST"
          value = var.env_redishost
        },
        {
          name  = "RESOLVER_IP"
          value = var.env_resolverip
        }
      ]
      image            = "${data.aws_caller_identity.current.account_id}.dkr.ecr.${data.aws_region.current.name}.amazonaws.com/${var.service_name}:nginx-${var.ecr_image_tag_nginx}"
      essential        = true
      environmentFiles = []
      extraHosts       = []
      links            = []
      mountPoints      = []
      secrets          = []
      systemControls   = []
      ulimits          = []
      volumesFrom      = []
      logConfiguration = {
        logDriver = "awslogs"
        options = {
          awslogs-group         = aws_cloudwatch_log_group.cloudwatchloggroup.name
          awslogs-region        = data.aws_region.current.name
          awslogs-stream-prefix = "${var.service_name}-nginx"
        }
        secretOptions = []
      }
    }
  ])
  memory = "2048"
  family = var.service_name
  requires_compatibilities = [
    "FARGATE"
  ]
  network_mode = "awsvpc"
  cpu          = "1024"
  tags = {
    env = var.service_name
  }
}
