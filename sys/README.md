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

## BUILD BASE IMAGE

```
aws ecr get-login-password --profile badge-poser | docker login --password-stdin -u AWS XXXXXXXXXXXX.dkr.ecr.eu-west-1.amazonaws.com
docker build -t XXXXXXXXXXXX.dkr.ecr.eu-west-1.amazonaws.com/badge-poser:base -f sys/docker/Dockerfile.base .
docker push XXXXXXXXXXXX.dkr.ecr.eu-west-1.amazonaws.com/badge-poser:base
```

## DEPLOY

```
docker build -t XXXXXXXXXXXX.dkr.ecr.eu-west-1.amazonaws.com/badge-poser:latest -f sys/docker/Dockerfile .
docker push XXXXXXXXXXXX.dkr.ecr.eu-west-1.amazonaws.com/badge-poser:latest
```

Then, update the task definition and switch version in the service.
