# PHP Code Test

### Set up

Install docker-composer for the OS of your choice

Run `docker-compose up -d` in the root of the project

Then run: `docker exec app-php composer install`

Visit http://localhost:8080/

In order to run the tests use the following command: `docker exec app-php php bin/phpunit`
