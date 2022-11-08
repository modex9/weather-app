<?php

namespace App\Service;

use App\Entity\Location;
use LogicException;
use App\Service\RedisCacheService;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\Exception\ClientException;
use App\Utils\DataKeys;
use App\Contracts\WeatherProviderInterface; 

class WeatherForecastService
{
    public function __construct(iterable $weatherProviders)
    {
        $this->weatherProviders = $weatherProviders;
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