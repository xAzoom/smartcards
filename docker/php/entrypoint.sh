#!/bin/sh

composer install -n
exec "$@"