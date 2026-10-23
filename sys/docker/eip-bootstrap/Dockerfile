FROM amazon/aws-cli:2.38.4

COPY sys/docker/eip-bootstrap/associate-eip.sh /usr/local/bin/associate-eip.sh

ENTRYPOINT ["/usr/local/bin/associate-eip.sh"]
