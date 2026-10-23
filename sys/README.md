# SYS CONFIGS

## REQUIREMENTS

 - Ansible
 - Terraform
 - Docker with buildx and arm64 emulation (`docker run --privileged --rm tonistiigi/binfmt --install arm64`)

## TOOLS

 - [StatusCake](https://app.statuscake.com) (Monitoring)
 - [Sentry.io](https://sentry.io/organizations/pugx) (Application Errors)

## SETUP

Infrastructure lives in `terraform/`: CloudFront → Bottlerocket ECS host (Spot ASG of 1, Graviton) → ElastiCache.

1. Request the CloudFront certificate: `terraform -chdir=sys/terraform apply -target=aws_acm_certificate.cdn`.
2. Add the records from `terraform -chdir=sys/terraform output cdn_certificate_validation` to the `pugx.org` DNS zone.
3. `make deploy_prod`, then `terraform apply` with the same image tag.
4. Point `poser.pugx.org` (CNAME) to `terraform output cdn_domain_name`.

## BUILD & DEPLOY

`make deploy_prod` builds multi-arch images, pushes them to ECR and runs `terraform plan` with the new tag. Apply with the same tag:

```
terraform -chdir=sys/terraform apply -var="ecr_image_tag_nginx=$VER" -var="ecr_image_tag_php=$VER"
```

## TESTING

### Stable

```
docker compose up
npm install artillery
./node_modules/.bin/artillery run sys/docker/artillery.yml
```
