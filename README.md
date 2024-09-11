
##  3rd Party Data aggregation and store APP

According to Case Study i have developed laravel 3rd party Data aggregation and storage and i have build the steps of guidance to run the laravel project

### Version

laravel 10

### Clone Laravel Project

git clone https://github.com/Umayantha93/news.git


### Install Dependencies

composer install

### Generate .env

generate the code below on your project terminal: 
cp .env.example .env

update these lines as in your database
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=



### Implement API KEYS on .env

NEWS_API_KEY=c63bf41e4704413a9f57fd722376bb89
NYT_API_KEY=xDcazicyoWyA58CUNNpAjfS2TO8rq1zf
THE_GUARDIAN_API_KEY=ac3dd3e3-41a9-4767-8abe-d2c7e67b3dc0

### Handle the Cron Job

run this code in the terminal:
php artisan schedule:run


