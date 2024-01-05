Project installation
-----------
* `docker-compose up -d`
* `docker network create nginx-proxy`
* `cp .env.example .env` 
* `docker exec -i articles-system-laravel.test-1 composer install`
* `docker exec -i articles-system-laravel.test-1 php artisan key:generate`
* `docker exec -t articles-system-laravel.test-1 php artisan migrate:fresh --seed`


## API Endpoints 

1. /api/register
2. /api/login
3. /api/currency-rates
4. /api/fetch-currencies
5. /api/currency-rates/{date}
6. /api/logout
7. /api/excel-export
