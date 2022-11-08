## Description

This is a simple application to get weather data for a location, which is estimated by the Country and city that user specifies.
User input is converted to latitude and longitude, provided by Free **PositionStack API**. This approach was selected, because most weather
API's work with lat/long query parameters.

Currently App uses two Free weather data providers: **OpenWeather** and **AmbeeData**.

Any number of weather data providers could be registered in the Application.
The following guide assumes that developer has knowledge of:
 - [Symfony Services](https://symfony.com/doc/current/service_container.html)
 - [Symfony HTTP Client](https://symfony.com/doc/current/http_client.html)

#### The guide to register new weather data provider:
 - Get the API key of the service, put into **`.env`** file and name it **`{DATA_PROVIDER_NAME}_API_KEY`**
 - In **config/packages/framework.yaml** define a HTTP client for your provider. If provider authenticates with header, you can configure authentication by passing the API key from **`.env`**
 **_('%env({DATA_PROVIDER_NAME}_API_KEY)%')_** into headers section, matched by the header name, that your data provider requires.
 - In **config/services.yaml** define your service. Arguments:
    - **HTTP client** defined in the second step.
    - **RedisCacheService** (recommended, unless you don't have Redis server)
    - **API key** - this is necessary, only if your data provider authenticates you through query/POST data, which you would have to pass to the HTTP client.

#### Requirements to launch the App:
 - PHP 8.1
 - MySQL 8
 - Composer 2
 - Npm
 - Symfony CLI
 - Redis Server && PHP-Redis module (you can switch to other types of caching in **`config/packages/cache.yaml`**. More info: [Cache](https://symfony.com/doc/current/cache.html))
 - PositionStack, OpenWeather, AmbeeData API keys.

#### To launch this App follow these steps:
 - Clone this repo or download the .zip of the repository.
 - Open the directory where you cloned the project in the terminal.
 - Run `composer install`
 - Run `npm install --save-dev`
 - Run `npm run build`
 - Inside `.env.example` file change parameters `user`, `pass` and `database` with your Database Username, password and Database Name used for this project.
 - Also in the same file provide `POSITION_STACK_API_KEY`, `OPENWEATHER_API_KEY`, `AMBEEDATA_API_KEY` values.
 - Rename the file `.env.example` to **`.env`**.
 - Execute database migrations by running `php bin/console doctrine:migrations:migrate`
 - Launch the app by running `symfony server:start`
 - Go to `http://localhost:8000` to use the app.