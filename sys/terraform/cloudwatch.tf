# TODO: import dashboard

resource "aws_cloudwatch_event_rule" "eventrulecontributorsupdate" {
  name                = "app-contributors-update"
  schedule_expression = "rate(24 hours)"
  state               = "DISABLED"
}
