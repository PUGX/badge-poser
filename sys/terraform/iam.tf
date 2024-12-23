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
