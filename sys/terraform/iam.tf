resource "aws_iam_user" "iamusergithubactions" {
  // CF Property(Policies) = [
  //   {
  //     PolicyName = "GitHubActionsDeploy"
  //     PolicyDocument = {
  //       Version = "2012-10-17"
  //       Statement = [
  //         {
  //           Sid = "GitHubActionsDeploy"
  //           Effect = "Allow"
  //           Action = [
  //             "cloudformation:CreateChangeSet",
  //             "sts:GetCallerIdentity"
  //           ]
  //           Resource = [
  //             "arn:aws:ecr:eu-west-1:*:repository/badge-poser",
  //             "arn:aws:cloudformation:eu-west-1:*:stack/poser-ecs/6ad34900-d679-11ea-a884-0a9b71aae734"
  //           ]
  //         },
  //         {
  //           Sid = "GitHubActionsDeployECR"
  //           Effect = "Allow"
  //           Action = [
  //             "ecr:BatchCheckLayerAvailability",
  //             "ecr:BatchGetImage",
  //             "ecr:CompleteLayerUpload",
  //             "ecr:DescribeImages",
  //             "ecr:DescribeRepositories",
  //             "ecr:GetDownloadUrlForLayer",
  //             "ecr:InitiateLayerUpload",
  //             "ecr:ListImages",
  //             "ecr:PutImage",
  //             "ecr:UploadLayerPart"
  //           ]
  //           Resource = "arn:aws:ecr:eu-west-1:*:repository/badge-poser"
  //         },
  //         {
  //           Sid = "GitHubActionsDeployECRToken"
  //           Effect = "Allow"
  //           Action = [
  //             "ecr:GetAuthorizationToken"
  //           ]
  //           Resource = "*"
  //         }
  //       ]
  //     }
  //   }
  // ]
  name = "github_action_deploy"
  tags = {
    env = var.service_name
  }
}

resource "aws_iam_access_key" "iamkey" {
  user = aws_iam_user.iamusergithubactions.name
}
