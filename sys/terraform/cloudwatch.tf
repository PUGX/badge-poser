# TODO: import dashboard

resource "aws_cloudwatch_event_rule" "eventrulecontributorsupdate" {
  name                = "app-contributors-update"
  schedule_expression = "rate(24 hours)"
  state               = "DISABLED"
  // CF Property(Targets) = [
  //   {
  //     Id = "phpfpm"
  //     Arn = aws_ecs_cluster.ecscluster.arn
  //     RoleArn = var.exec_role_arn
  //     Input = "{"containerOverrides":[{"name":"phpfpm","command":["./bin/console","app:contributors:update"]}]}"
  //     EcsParameters = {
  //       TaskDefinitionArn = aws_ecs_task_definition.ecstask.arn
  //       LaunchType = "FARGATE"
  //       NetworkConfiguration = {
  //         AwsVpcConfiguration = {
  //           SecurityGroups = [
  //             aws_security_group.sgecs.arn
  //           ]
  //           Subnets = var.subnets
  //         }
  //       }
  //     }
  //   }
  // ]
}
