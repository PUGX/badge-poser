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

```bash
ROLLBACK_VER=20200521055734
curl -sO https://gordalina.github.io/cachetool/downloads/cachetool.phar
chmod +x cachetool.phar
rm /application/current
ln -s /application/releases/$ROLLBACK_VER current
systemctl restart php-fpm.service
php cachetool.phar opcache:reset --fcgi=/run/php-fpm/www.sock
rm cachetool.phar
```
