#!/usr/bin/env bash

project_name="tcommerce"

command="sudo docker-compose --project-name=$project_name stop"
tput setaf 6; printf "Executing: $command\n"; tput sgr0
exec $command
