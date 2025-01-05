variable "ecr_image_tag_nginx" {
  description = "Specifies the ECR Image Tag for nginx container."
  type        = string
}

variable "ecr_image_tag_php" {
  description = "Specifies the ECR Image Tag for PHP-FPM container."
  type        = string
}

variable "environment" {
  description = "The Environment"
  type        = string
}

variable "exec_role_arn_autoscale" {
  description = "Specifies the ARN of the Execution Role for Autoscaling."
  type        = string
}

variable "service_name" {
  description = "The name of the service being created. It identifies all the resources related to it."
  type        = string
  default     = "badge-poser"
}

variable "subnets" {
  description = "Specifies the ID of Subnets belongin to the correct VPC."
  type        = list(string)
}

variable "vpc_id" {
  description = "Specifies the ID of an existing VPC in which to launch your container instances."
  type        = string
}

variable "env_app_debug" {
  description = "Environment variable for APP_DEBUG"
  type        = string
  default     = "0"
}

variable "env_app_env" {
  description = "Environment variable for APP_ENV"
  type        = string
  default     = "prod"
}

variable "env_app_secret" {
  description = "Environment variable for APP_SECRET"
  type        = string
}

variable "env_app_xdebug" {
  description = "Environment variable for APP_XDEBUG"
  type        = string
  default     = "0"
}

variable "env_app_xdebug_host" {
  description = "Environment variable for APP_XDEBUG_HOST"
  type        = string
}

variable "env_bitbucket_auth_method" {
  description = "Environment variable for BITBUCKET_AUTH_METHOD"
  type        = string
  default     = "http_password"
}

variable "env_bitbucket_secret" {
  description = "Environment variable for BITBUCKET_SECRET"
  type        = string
}

variable "env_bitbucket_token" {
  description = "Environment variable for BITBUCKET_TOKEN"
  type        = string
}

variable "env_circleci_token" {
  description = "Environment variable for CIRCLE_CI_TOKEN"
  type        = string
}

variable "env_github_auth_method" {
  description = "Environment variable for GITHUB_AUTH_METHOD"
  type        = string
  default     = "access_token_header"
}

variable "env_github_secret" {
  description = "Environment variable for GITHUB_SECRET"
  type        = string
}

variable "env_github_username" {
  description = "Environment variable for GITHUB_USERNAME"
  type        = string
}

variable "env_phpfpm_host" {
  description = "Environment variable for PHPFPM_HOST"
  type        = string
  default     = "127.0.0.1"
}

variable "env_resolver_ip" {
  description = "Environment variable for RESOLVER_IP"
  type        = string
  default     = "169.254.169.253"
}

variable "env_sentry_dsn" {
  description = "Environment variable for SENTRY_DSN"
  type        = string
}

variable "env_trusted_proxies" {
  description = "Environment variable for TRUSTED_PROXIES"
  type        = string
  default     = "REMOTE_ADDR"
}

variable "env_gitlab_token" {
  description = "Environment variable for GITLAB_TOKEN"
  type        = string
}
