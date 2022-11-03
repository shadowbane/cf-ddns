#!/bin/sh
printenv | grep -e CLOUDFLARE >> /etc/environment

crond -f
