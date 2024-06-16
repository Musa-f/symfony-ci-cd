#!/bin/bash

cd /var/www/

echo "Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

echo "Loading fixtures..."
php bin/console doctrine:fixtures:load --no-interaction

echo "Starting Apache..."
exec apache2-foreground
