# REQUIREMENTS

 - Ansible
 - AWS CloudFormation

# SETUP

Create the AWS CloudFormation Stack using `stack.cf.yaml`.
Once got the Public IP, change the `inventory` file.

Then, provision the instance with:

```bash
ansible-galaxy install -r requirements.yml
ansible-playbook -i inventory playbooks/setup.yml
```

# DEPLOY

```bash
ansible-playbook -i inventory playbooks/deploy.yml
```
