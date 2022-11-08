<?php

namespace App\Service;

class WeatherForecastService
{
    public function __construct(private iterable $weatherProviders)
    {
    }

    public function getWeatherForecast(float $lat, float $long) : float
    {
        $sumTemperatures = 0;
        $countProviders = count($this->weatherProviders);
        foreach($this->weatherProviders as $weatherProvider)
        {
            $sumTemperatures += $weatherProvider->getTemperatureByGeoclocation($lat, $long);
        }

        return $sumTemperatures / $countProviders;
    }
}