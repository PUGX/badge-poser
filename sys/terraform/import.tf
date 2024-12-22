import {
  to = aws_cloudwatch_event_rule.eventrulecontributorsupdate
  id = "default/app-contributors-update"
}

import {
  to = aws_security_group.sgecs
  id = "sg-06c2c1b1e7d48f166"
}

import {
  to = aws_cloudwatch_log_group.cloudwatchloggroup
  id = "badge-poser-logs"
}

import {
  to = aws_ecs_cluster.ecscluster
  id = "badge-poser-cluster-prod"
}

import {
  to = aws_ecs_service.ecsservice
  id = "badge-poser-cluster-prod/badge-poser"
}

import {
  to = aws_ecs_task_definition.ecstask
  id = "arn:aws:ecs:eu-west-1:478389220392:task-definition/badge-poser:138"
}

import {
  to = aws_security_group.sgelb
  id = "sg-039400b411ff60301"
}

import {
  to = aws_iam_user.iamusergithubactions
  id = "github_action_deploy"
}

import {
  to = aws_iam_access_key.iamkey
  id = ""
}

import {
  to = aws_security_group.sgredis
  id = "sg-09ad9402145d8eb17"
}

import {
  to = aws_elasticache_cluster.rediscluster
  id = "poser-stats"
}

import {
  to = aws_elasticache_subnet_group.redissubnet
  id = "poser-subnet"
}

import {
  to = aws_lb_target_group.elbtargetgroup
  id = "arn:aws:elasticloadbalancing:eu-west-1:478389220392:targetgroup/badegposer/d24c3e0c7d0276d3"
}

import {
  to = aws_security_group_rule.sgelb_ingress_http
  id = "sg-039400b411ff60301_ingress_tcp_80_80_0.0.0.0/0"
}

import {
  to = aws_security_group_rule.sgelb_ingress_https
  id = "sg-039400b411ff60301_ingress_tcp_443_443_0.0.0.0/0"
}

import {
  to = aws_security_group_rule.sgredis_ingress_redis
  id = "sg-09ad9402145d8eb17_ingress_tcp_6379_6379_0.0.0.0/0"
}

import {
  to = aws_security_group_rule.sgecs_ingress_http
  id = "sg-06c2c1b1e7d48f166_ingress_tcp_80_80_0.0.0.0/0"
}

import {
  to = aws_security_group_rule.sgecs_ingress_https
  id = "sg-06c2c1b1e7d48f166_ingress_tcp_443_443_0.0.0.0/0"
}


import {
  to = aws_lb.elb
  id = "arn:aws:elasticloadbalancing:eu-west-1:478389220392:loadbalancer/app/badge-poser-elb/81d698b74f86c6b9"
}

import {
  to = aws_lb_listener.elblistener80
  id = "arn:aws:elasticloadbalancing:eu-west-1:478389220392:listener/app/badge-poser-elb/81d698b74f86c6b9/cff7a4a219047f82"
}

import {
  to = aws_lb_listener.elblistener443
  id = "arn:aws:elasticloadbalancing:eu-west-1:478389220392:listener/app/badge-poser-elb/81d698b74f86c6b9/fc943ccbe12b086f"
}


import {
  to = aws_lb_listener_rule.elblistenerrule80
  id = "arn:aws:elasticloadbalancing:eu-west-1:478389220392:listener-rule/app/badge-poser-elb/81d698b74f86c6b9/cff7a4a219047f82/23a572ed933cc547"
}


import {
  to = aws_lb_listener_rule.elblistenerrule443
  id = "arn:aws:elasticloadbalancing:eu-west-1:478389220392:listener-rule/app/badge-poser-elb/81d698b74f86c6b9/fc943ccbe12b086f/57de0169e306c96e"
}
