#!/usr/bin/env bash

project_name="tcommerce"

command="sudo docker-compose --project-name=$project_name exec webserver sh"
tput setaf 6; printf "Executing: $command\n"; tput sgr0
exec $command
