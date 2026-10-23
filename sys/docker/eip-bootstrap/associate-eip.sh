#!/bin/sh
# Bottlerocket bootstrap container: attach the origin Elastic IP to this instance.
# The EIP allocation id arrives as the container user-data (see sys/terraform/ec2.tf).
set -eu

ALLOCATION_ID=$(cat /.bottlerocket/bootstrap-containers/current/user-data)
IMDS=http://169.254.169.254/latest
TOKEN=$(curl -sf -X PUT "$IMDS/api/token" -H "X-aws-ec2-metadata-token-ttl-seconds: 60")
metadata() { curl -sf -H "X-aws-ec2-metadata-token: $TOKEN" "$IMDS/meta-data/$1"; }

aws ec2 associate-address \
    --region "$(metadata placement/region)" \
    --instance-id "$(metadata instance-id)" \
    --allocation-id "$ALLOCATION_ID" \
    --allow-reassociation
