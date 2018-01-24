#!/usr/bin/env bash

args="-d"
services="webserver mysql redis"
project_name="tcommerce"

for arg in "$@"
do
	if [ "$arg" = "--recreate" ] || [ "$arg" = "-r" ]; then
		args="$args --force-recreate --build"
	elif [ "$arg" = "--no-cache" ] || [ "$arg" = "-n" ]; then
	    command="sudo docker-compose --project-name=$project_name build --no-cache $services"
        tput setaf 6; printf "Executing: $command\n"; tput sgr0
        exec $command
    fi
done

command="sudo docker-compose --project-name=$project_name up $args $services"
tput setaf 6; printf "Executing: $command\n"; tput sgr0
exec $command
