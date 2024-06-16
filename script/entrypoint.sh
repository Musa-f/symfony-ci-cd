#!/bin/bash
cd /var/www/
php /var/www/bin/console cache:clear
php /var/www/bin/console doctrine:migrations:migrate --no-interaction
php /var/www/bin/console doctrine:fixtures:load --no-interaction
exec apache2-foreground
