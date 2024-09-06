#!/usr/bin/env bash

SCRIPT_DIR=$(cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd)

DOMAIN=$1
if [ "$DOMAIN" == "" ]; then
  DOMAIN="default"
fi

# CA
openssl req \
  -new \
  -x509 \
  -days 9999 \
  -config "$SCRIPT_DIR/ca.cnf" \
  -keyout "$SCRIPT_DIR/ca-key.pem" \
  -out "$SCRIPT_DIR/ca-crt.pem"

openssl genrsa -out "$SCRIPT_DIR/server.key" 4096

# CSR
openssl req \
  -new \
  -config "$SCRIPT_DIR/server.cnf" \
  -key "$SCRIPT_DIR/server.key" \
  -out "$SCRIPT_DIR/server.csr"

# CERT
openssl x509 \
  -req \
  -extfile "$SCRIPT_DIR/server.cnf" \
  -days 3650 \
  -passin "pass:password" \
  -in "$SCRIPT_DIR/server.csr" \
  -CA "$SCRIPT_DIR/ca-crt.pem" \
  -CAkey "$SCRIPT_DIR/ca-key.pem" \
  -CAcreateserial \
  -out "$SCRIPT_DIR/server.pem"
