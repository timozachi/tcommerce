#!/usr/bin/env bash
cd ../
sudo chmod 776 logs
sudo chmod 776 logs/admin
sudo chmod 776 logs/api
sudo chmod 776 logs/frontend
sudo chmod 776 cache

composer install
