# REQUIREMENTS

 - Ansible
 - AWS CloudFormation

# TOOLS

 - [Logz.io](https://app-uk.logz.io/#/dashboard/kibana/discover) (Log Management)
 - [StatusCake](https://app.statuscake.com) (Monitoring)
 - [Sentry.io](https://sentry.io/organizations/pugx) (Application Errors)

# SETUP

1. Create the AWS CloudFormation Stack using `cloudformation/stack.cf.yaml`.
1. Once got the Public IP, change the `ansible/inventory` file.
1. Configure the environment variables in `.env.dist`
1. Then, provision the instance with:

```bash
ansible-galaxy install -r ansible/requirements.yml
ansible-playbook -i inventory ansible/playbooks/setup.yml
```

# DEPLOY

```bash
ansible-playbook -i inventory ansible/playbooks/deploy.yml
```

**NOTE:** Need a rollback? You need to do it manually :(

# COMPILE BREF

## BREF

```
cd runtime
cd base ; docker build --file php-74.Dockerfile -t bref/build-php-74 --target build-environment .
cd base ; docker build --file php-74.Dockerfile -t bref/tmp/cleaned-build-php-74 .
cd layers/function ; docker build -t bref/php-74 --build-arg PHP_VERSION=74 .
cd layers/fpm ; docker build -t bref/php-74-fpm --build-arg PHP_VERSION=74 .
cd layers/fpm ; docker build -t fabiocicerchia/bref-php-74-fpm --build-arg PHP_VERSION=74 .
cd layers/fpm-dev ; docker build -t bref/php-74-fpm-dev --build-arg PHP_VERSION=74 .
cd layers/fpm-dev ; docker build -t fabiocicerchia/bref-php-74-fpm-dev --build-arg PHP_VERSION=74 .
```

## BREF-EXTRA

```
cd layers/gd; docker build -t fabiocicerchia/bref-extra-gd-php-74 . --build-arg PHP_VERSION=74
```
