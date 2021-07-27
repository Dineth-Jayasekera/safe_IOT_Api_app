web: vendor/bin/heroku-php-apache2 public/
web: vendor/bin/heroku-php-apache2 -i custom_php.ini public/
worker: php artisan queue:restart && php artisan queue:work --tries=10
