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
  // CF Property(CapacityProviders) = [
  //   "FARGATE",
  //   "FARGATE_SPOT"
  // ]
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
  execution_role_arn = var.exec_role_arn
  container_definitions = templatefile("ecs/task-definition.json", {
    account_id              = data.aws_caller_identity.current.account_id
    aws_region              = data.aws_region.current.name
    service_name            = var.service_name
    ecr_image_tag_nginx     = var.ecr_image_tag_nginx
    ecr_image_tag_php       = var.ecr_image_tag_php
    cloudwatchloggroup      = aws_cloudwatch_log_group.cloudwatchloggroup.name
    env_appdebug            = var.env_appdebug
    env_appenv              = var.env_appenv
    env_appsecret           = var.env_appsecret
    env_appxdebug           = var.env_appxdebug
    env_appxdebughost       = var.env_appxdebughost
    env_bitbucketauthmethod = var.env_bitbucketauthmethod
    env_bitbucketsecret     = var.env_bitbucketsecret
    env_bitbuckettoken      = var.env_bitbuckettoken
    env_circlecitoken       = var.env_circlecitoken
    env_githubauthmethod    = var.env_githubauthmethod
    env_githubsecret        = var.env_githubsecret
    env_githubusername      = var.env_githubusername
    env_gitlabtoken         = var.env_gitlabtoken
    env_phpfpmhost          = var.env_phpfpmhost
    env_redishost           = var.env_redishost
    env_redishost           = var.env_redishost
    env_resolverip          = var.env_resolverip
    env_sentrydsn           = var.env_sentrydsn
    env_trustedproxies      = var.env_trustedproxies
  })
  memory = "2048"
  family = var.service_name
  requires_compatibilities = [
    "FARGATE"
  ]
  network_mode = "awsvpc"
  cpu          = "1024"
}
