resource "aws_acm_certificate" "cert" {
  domain_name       = "poser.pugx.org"
  validation_method = "DNS"

  lifecycle {
    create_before_destroy = true
  }
}
