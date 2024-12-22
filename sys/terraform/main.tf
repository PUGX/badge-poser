locals {
  mappings = {
  }
  stack_name = "stack"
}

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
}
