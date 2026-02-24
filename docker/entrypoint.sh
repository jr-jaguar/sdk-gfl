#!/bin/sh
set -e

if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        cp .env.example .env
        echo "\033[32mEntrypoint: .env created from .env.example\033[0m"
    else
        echo "\033[33mEntrypoint: WARNING! .env.example not found.\033[0m"
    fi
fi

exec "$@"
