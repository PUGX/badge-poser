# SYS CONFIGS

![AWS Stack](cloudformation/stack.png)

## REQUIREMENTS

 - Ansible
 - AWS CloudFormation

## TOOLS

 - [StatusCake](https://app.statuscake.com) (Monitoring)
 - [Sentry.io](https://sentry.io/organizations/pugx) (Application Errors)

## SETUP

1. Create the AWS CloudFormation Stack using `cloudformation/stack.cf.yaml`.

## BUILD IMAGES

```
aws ecr get-login-password --profile badge-poser | docker login --password-stdin -u AWS XXXXXXXXXXXX.dkr.ecr.eu-west-1.amazonaws.com

VER=$(date +%s);
docker build -t XXXXXXXXXXXX.dkr.ecr.eu-west-1.amazonaws.com/badge-poser:nginx-$VER -f sys/docker/nginx/Dockerfile .
docker build -t XXXXXXXXXXXX.dkr.ecr.eu-west-1.amazonaws.com/badge-poser:phpfpm-$VER -f sys/docker/php/Dockerfile .

docker push XXXXXXXXXXXX.dkr.ecr.eu-west-1.amazonaws.com/badge-poser:nginx-$VER
docker push XXXXXXXXXXXX.dkr.ecr.eu-west-1.amazonaws.com/badge-poser:php-$VER
```

## DEPLOY

Update the task definition and switch version in the service.
