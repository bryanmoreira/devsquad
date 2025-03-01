rm -f junit.xml &&\
/usr/bin/php8.2 /usr/local/bin/composer install &&\
cp .env.example .env &&\
php artisan key:generate &&\
./vendor/bin/phpunit --log-junit=junit.xml &&\
/usr/bin/php8.2 score
