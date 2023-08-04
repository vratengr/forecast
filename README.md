Vanessa - Sample Code
===========

**Task detail:**

Please create a simple Laravel/Symfony/(Any other framework you like) site where
a user will be able to provide his city and country via form
and after submission system will display current weather forecast.  

Forecast temperature should be calculated as an average based on different APIs,
at least 2 different data sources  
(ex. API1 will return temperature 25, API2 will return temperature 27 so the result should be (25+27)/2 ie. 26)  

Feel free to use https://openweathermap.org/API and any other API you like.  

* Few notes:
    * results should be stored in the database
    * a simple caching mechanism should be added
    * ability to easily add new data sources (how to register them, interfaces etc.)
    * clean data separation
    * nice to have - latest PHP mechanisms (ex. traits)

===========

**Installation:**

Prerequisites: Composer, Symfony CLI, MySQL, NPM

1. Clone the repo or extract the zip file in your preferred directory
1. Install bundles (only if cloning, for zip file all bundles are already included)
    * composer install
    * composer require symfony/webpack-encore-bundle
    * npm install
    * npm run dev
1. Update the DATABASE_URL in .env file based on your database credentials
1. Create the database or run:
    * php bin/console doctrine:database:create
1. Run migrations
    * php bin/console doctrine:migrations:migrate
1. Start apache server or you can use Symfony CLI
    * symfony server:start
1. Access the app using the URL provided in the CLI
    eg: http://127.0.0.1:8000/


===========

**Code review:**

* PHP:
    * src/Controller/WeatherController.php
    * src/Traits/WeatherTrait.php
* Database:
    * migrations/Initial001.php
* Frontend:
    * assests/styles/app.css
    * templates/

===========

*A Symfony project to show code samples.
October 8, 2021*