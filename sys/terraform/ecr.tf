resource "aws_ecr_lifecycle_policy" "badge_poser" {
  repository = var.service_name

  policy = jsonencode({
    rules = [
      # No "untagged" rule: per-arch images behind multi-arch tags are untagged.
      {
        rulePriority = 1
        description  = "Keep the last 10 phpfpm images"
        selection    = { tagStatus = "tagged", tagPrefixList = ["phpfpm-"], countType = "imageCountMoreThan", countNumber = 10 }
        action       = { type = "expire" }
      },
      {
        rulePriority = 2
        description  = "Keep the last 10 nginx images"
        selection    = { tagStatus = "tagged", tagPrefixList = ["nginx-"], countType = "imageCountMoreThan", countNumber = 10 }
        action       = { type = "expire" }
      },
    ]
  })
}
