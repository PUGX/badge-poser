resource "aws_secretsmanager_secret" "poser" {
  name = var.service_name
}

resource "aws_secretsmanager_secret_version" "poser" {
  secret_id = aws_secretsmanager_secret.poser.id
  secret_string = jsonencode({
    APP_SECRET       = var.env_app_secret
    GITHUB_SECRET    = var.env_github_secret
    GITHUB_USERNAME  = var.env_github_username
    CIRCLECI_TOKEN   = var.env_circleci_token
    BITBUCKET_SECRET = var.env_bitbucket_secret
    BITBUCKET_TOKEN  = var.env_bitbucket_token
    GITLAB_TOKEN     = var.env_gitlab_token
  })
}
