terraform {
  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "~> 5.0"
    }
  }

  #   backend "s3" {
  #     bucket = "mybucket"
  #     key    = "prod.tf"
  #     region = "eu-west-1"
  #   }
}

provider "aws" {
  profile = "poser"
  default_tags {
    tags = {
      "env" = var.service_name
    }
  }
}

# CloudFront only accepts ACM certificates from us-east-1.
provider "aws" {
  alias   = "us_east_1"
  region  = "us-east-1"
  profile = "poser"
  default_tags {
    tags = {
      "env" = var.service_name
    }
  }
}
