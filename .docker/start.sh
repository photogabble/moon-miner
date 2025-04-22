#!/usr/bin/env bash

set -e

role=${CONTAINER_ROLE:-app}
env=${APP_ENV:-production}

if [ "$env" != "local" ]; then
    echo "Caching configuration..."
    (cd /var/www/html && php artisan config:cache && php artisan route:cache && php artisan view:cache)
fi

if [ "$role" = "app" ]; then
    # Run default behaviour of the php:8.3-apache container
    exec apache2-foreground
elif [ "$role" = "ssr" ]; then
    # Run node application for SSR Vue templates
    # TODO
elif [ "$role" = "queue" ]; then
    # Run artisan queue:work command via supervisord
    /usr/bin/supervisord -c /usr/local/etc/supervisor/queue.conf
elif [ "$role" = "scheduler" ]; then
    # Run artisan schedule:run command via supervisord
    /usr/bin/supervisord -c /usr/local/etc/supervisor/scheduler.conf
else
    echo "Could not match the container role \"$role\""
    exit 1
fi
