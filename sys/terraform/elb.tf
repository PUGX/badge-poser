resource "aws_security_group" "sgelb" {
  description = "${var.service_name}-elb"
  name        = "${var.service_name}-elb"
  egress {
    cidr_blocks = ["0.0.0.0/0"]
    protocol    = "-1"
    from_port   = 0
    to_port     = 0
  }
  vpc_id = var.vpc_id
}

resource "aws_security_group_rule" "sgelb_ingress_http" {
  type              = "ingress"
  security_group_id = aws_security_group.sgelb.id
  cidr_blocks       = ["0.0.0.0/0"]
  from_port         = 80
  protocol          = "tcp"
  to_port           = 80
}
resource "aws_security_group_rule" "sgelb_ingress_https" {
  type              = "ingress"
  security_group_id = aws_security_group.sgelb.id
  cidr_blocks       = ["0.0.0.0/0"]
  from_port         = 443
  protocol          = "tcp"
  to_port           = 443
}

resource "aws_lb_target_group" "elbtargetgroup" {
  name        = "badegposer"
  port        = 80
  protocol    = "HTTP"
  vpc_id      = var.vpc_id
  target_type = "ip"
}

resource "aws_lb" "elb" {
  name            = "${var.service_name}-elb"
  subnets         = var.subnets
  security_groups = [aws_security_group.sgelb.id]
}

resource "aws_lb_listener" "elblistener80" {
  load_balancer_arn = aws_lb.elb.arn
  port              = 80
  protocol          = "HTTP"
  default_action {
    type = "fixed-response"
    fixed_response {
      content_type = "text/plain"
      message_body = "AWS is a teapot"
      status_code  = "418"
    }
  }
}

resource "aws_lb_listener" "elblistener443" {
  load_balancer_arn = aws_lb.elb.arn
  certificate_arn   = aws_acm_certificate.cert.arn
  port              = 443
  default_action {
    type = "fixed-response"
    fixed_response {
      content_type = "text/plain"
      message_body = "https teapot"
      status_code  = "418"
    }
  }
}

resource "aws_appautoscaling_target" "asscalabletarget" {
  max_capacity       = 1
  min_capacity       = 1
  resource_id        = "service/${var.service_name}-cluster-${var.environment}/${var.service_name}"
  role_arn           = var.exec_role_arn_autoscale
  scalable_dimension = "ecs:service:DesiredCount"
  service_namespace  = "ecs"
}

resource "aws_lb_listener_rule" "elblistenerrule80" {
  listener_arn = aws_lb_listener.elblistener80.arn
  action {
    type = "fixed-response"
    fixed_response {
      content_type = "text/plain"
      message_body = "AWS is a teapot"
      status_code  = "418"
    }
  }
  condition {}
}

resource "aws_lb_listener_rule" "elblistenerrule443" {
  listener_arn = aws_lb_listener.elblistener443.arn
  action {
    type             = "forward"
    target_group_arn = aws_lb_target_group.elbtargetgroup.arn

    forward {
      target_group {
        arn    = aws_lb_target_group.elbtargetgroup.arn
        weight = 1
      }
    }
  }
  condition {
    host_header {
      values = [
        "poser.pugx.org",
      ]
    }
  }
  condition {
    path_pattern {
      values = ["/*"]
    }
  }
}
