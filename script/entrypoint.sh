php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load 
exec apache2-foreground
