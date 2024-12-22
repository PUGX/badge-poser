resource "aws_security_group" "sgredis" {
  description = "${var.service_name}-redis"
  name        = "${var.service_name}-redis"
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

resource "aws_security_group_rule" "sgredis_ingress_redis" {
  type              = "ingress"
  security_group_id = aws_security_group.sgredis.id
  cidr_blocks       = ["0.0.0.0/0"]
  from_port         = 6379
  protocol          = "tcp"
  to_port           = 6379
}

resource "aws_elasticache_cluster" "rediscluster" {
  cluster_id                 = "poser-stats"
  auto_minor_version_upgrade = true
  node_type                  = "cache.t4g.micro"
  subnet_group_name          = aws_elasticache_subnet_group.redissubnet.id
  engine                     = "redis"
  engine_version             = "7.0"
  security_group_ids         = [aws_security_group.sgredis.id]
  snapshot_retention_limit   = 1
  transit_encryption_enabled = false
  tags = {
    "env" = "badge-poser"
  }
  tags_all = {
    "env" = "badge-poser"
  }
}

resource "aws_elasticache_subnet_group" "redissubnet" {
  name        = "poser-subnet"
  description = "poser-subnet"
  subnet_ids  = var.subnets
}
