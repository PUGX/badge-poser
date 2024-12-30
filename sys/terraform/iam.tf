data "aws_iam_policy_document" "iamusergithubactions" {
  statement {
    sid       = "GitHubActionsDeploy"
    effect    = "Allow"
    actions   = ["cloudformation:CreateChangeSet", "sts:GetCallerIdentity"]
    resources = ["arn:aws:ecr:eu-west-1:*:repository/badge-poser", "arn:aws:cloudformation:eu-west-1:*:stack/poser-ecs/6ad34900-d679-11ea-a884-0a9b71aae734"]
  }
  statement {
    sid    = "GitHubActionsDeployECR"
    effect = "Allow"
    actions = [
      "ecr:BatchCheckLayerAvailability",
      "ecr:BatchGetImage",
      "ecr:CompleteLayerUpload",
      "ecr:DescribeImages",
      "ecr:DescribeRepositories",
      "ecr:GetDownloadUrlForLayer",
      "ecr:InitiateLayerUpload",
      "ecr:ListImages",
      "ecr:PutImage",
      "ecr:UploadLayerPart"
    ]
    resources = ["arn:aws:ecr:eu-west-1:*:repository/badge-poser"]
  }
  statement {
    sid       = "GitHubActionsDeployECRToken"
    effect    = "Allow"
    actions   = ["ecr:GetAuthorizationToken"]
    resources = ["*"]
  }
}

resource "aws_iam_user_policy" "lb_ro" {
  name   = "GitHubActionsDeploy"
  user   = aws_iam_user.iamusergithubactions.name
  policy = data.aws_iam_policy_document.iamusergithubactions.json
}

resource "aws_iam_user" "iamusergithubactions" {
  name = "github_action_deploy"
}

resource "aws_iam_access_key" "iamkey" {
  user = aws_iam_user.iamusergithubactions.name
}

resource "aws_iam_role" "ecs_task_role" {
  name = "${var.service_name}-ecs-exec"

  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Action = "sts:AssumeRole"
        Effect = "Allow"
        Sid    = ""
        Principal = {
          Service = "ecs-tasks.amazonaws.com"
        }
      },
    ]
  })
}

resource "aws_iam_role_policy" "read_secrets_policy" {
  name = "read-secrets"
  role = aws_iam_role.ecs_task_role.id

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
    {
        Action   = ["secretsmanager:GetSecretValue", "secretsmanager:DescribeSecret"]
        Effect   = "Allow"
        Resource = aws_secretsmanager_secret.poser.arn
    },
    ]
  })
}

resource "aws_iam_role_policy_attachment" "ecs_task_role" {
  role       = aws_iam_role.ecs_task_role.name
  policy_arn = "arn:aws:iam::aws:policy/service-role/AmazonECSTaskExecutionRolePolicy"
}
