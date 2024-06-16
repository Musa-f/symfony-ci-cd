#!/bin/bash

cd /var/www/

php /var/www/bin/console cache:clear

echo "Running migrations..."
php /var/www/bin/console doctrine:migrations:migrate --no-interaction

echo "Loading fixtures..."
php /var/www/bin/console doctrine:fixtures:load --no-interaction

echo "Starting Apache..."
exec apache2-foreground
