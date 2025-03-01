#!/usr/bin/env sh

function install_sail() {
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v "$(pwd):/var/www/html" \
        -w /var/www/html \
        laravelsail/php82-composer:latest \
        composer install --ignore-platform-reqs
}

if [ $1 == 'install' ]; then
    install_sail
else
    ./vendor/bin/sail $@
fi
