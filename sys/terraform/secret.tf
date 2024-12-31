resource "aws_secretsmanager_secret" "poser" {
  name = var.service_name
}

resource "aws_secretsmanager_secret_version" "poser" {
  secret_id     = aws_secretsmanager_secret.poser.id
  secret_string = jsonencode({
    APP_SECRET       = var.env_appsecret
    GITHUB_SECRET    = var.env_githubsecret
    GITHUB_USERNAME  = var.env_githubusername
    CIRCLE_CI_TOKEN  = var.env_circlecitoken
    BITBUCKET_SECRET = var.env_bitbucketsecret
    BITBUCKET_TOKEN  = var.env_bitbuckettoken
    GITLAB_TOKEN     = var.env_gitlabtoken
  })
}
