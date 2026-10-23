resource "aws_acm_certificate" "cdn" {
  provider          = aws.us_east_1
  domain_name       = "poser.pugx.org"
  validation_method = "DNS"

  lifecycle {
    create_before_destroy = true
  }
}

# Blocks the apply until the records from the `cdn_certificate_validation`
# output exist in the pugx.org DNS zone.
resource "aws_acm_certificate_validation" "cdn" {
  provider        = aws.us_east_1
  certificate_arn = aws_acm_certificate.cdn.arn
}

# Cache only what the app marks as cacheable (badges set s-maxage/max-age).
resource "aws_cloudfront_cache_policy" "origin_headers" {
  name        = "${var.service_name}-origin-headers"
  min_ttl     = 0
  default_ttl = 0
  max_ttl     = 31536000

  parameters_in_cache_key_and_forwarded_to_origin {
    enable_accept_encoding_brotli = true
    enable_accept_encoding_gzip   = true

    cookies_config {
      cookie_behavior = "none"
    }
    headers_config {
      header_behavior = "none"
    }
    # Badge style/label options come in the query string.
    query_strings_config {
      query_string_behavior = "all"
    }
  }
}

data "aws_cloudfront_origin_request_policy" "all_viewer" {
  name = "Managed-AllViewer"
}

resource "aws_cloudfront_distribution" "cdn" {
  enabled         = true
  is_ipv6_enabled = true
  http_version    = "http2and3"
  price_class     = "PriceClass_100"
  aliases         = ["poser.pugx.org"]

  origin {
    origin_id   = "ec2"
    domain_name = aws_eip.origin.public_dns

    # TLS ends at CloudFront; the origin only accepts CloudFront's IP ranges.
    custom_origin_config {
      http_port              = 80
      https_port             = 443
      origin_protocol_policy = "http-only"
      origin_ssl_protocols   = ["TLSv1.2"]
    }
  }

  default_cache_behavior {
    target_origin_id         = "ec2"
    viewer_protocol_policy   = "redirect-to-https"
    allowed_methods          = ["DELETE", "GET", "HEAD", "OPTIONS", "PATCH", "POST", "PUT"]
    cached_methods           = ["GET", "HEAD"]
    compress                 = true
    cache_policy_id          = aws_cloudfront_cache_policy.origin_headers.id
    origin_request_policy_id = data.aws_cloudfront_origin_request_policy.all_viewer.id
  }

  restrictions {
    geo_restriction {
      restriction_type = "none"
    }
  }

  viewer_certificate {
    acm_certificate_arn      = aws_acm_certificate_validation.cdn.certificate_arn
    ssl_support_method       = "sni-only"
    minimum_protocol_version = "TLSv1.2_2021"
  }
}

output "cdn_certificate_validation" {
  description = "DNS records to add to the pugx.org zone to validate the CloudFront certificate."
  value = [for o in aws_acm_certificate.cdn.domain_validation_options : {
    name  = o.resource_record_name
    type  = o.resource_record_type
    value = o.resource_record_value
  }]
}

output "cdn_domain_name" {
  description = "Point the poser.pugx.org CNAME here at cutover."
  value       = aws_cloudfront_distribution.cdn.domain_name
}
