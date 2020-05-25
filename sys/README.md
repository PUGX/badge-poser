# SYS CONFIGS

## REQUIREMENTS

 - Ansible
 - AWS CloudFormation

## TOOLS

 - [Logz.io](https://app-uk.logz.io/#/dashboard/kibana/discover) (Log Management)
 - [StatusCake](https://app.statuscake.com) (Monitoring)
 - [Sentry.io](https://sentry.io/organizations/pugx) (Application Errors)

## SETUP

1. Create the AWS CloudFormation Stack using `cloudformation/stack.cf.yaml`.
1. Once got the Public IP, change the `ansible/inventory` file.
1. Configure the environment variables in `.env.dist`
1. Then, provision the instance with:

```bash
ansible-galaxy install -r ansible/requirements.yml
ansible-playbook -i inventory ansible/playbooks/setup.yml
```

## DEPLOY

```bash
ansible-playbook -i inventory ansible/playbooks/deploy.yml
```

## ROLLBACK

```bash
ansible-playbook -i inventory ansible/playbooks/rollback.yml
```
