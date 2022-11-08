<?php

namespace App\Contracts;

interface WeatherProviderInterface {

    const WEATHER_CACHE_EXPIRY = 900; // 15 minutes.

    public function getTemperatureByGeoclocation(float $lat, float $long) : float; 

}