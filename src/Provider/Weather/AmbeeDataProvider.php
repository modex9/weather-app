<?php

namespace App\Provider\Weather;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Contracts\WeatherProviderInterface;
use App\Service\RedisCacheService;
use Symfony\Component\HttpClient\Exception\ClientException;

class AmbeeDataProvider implements WeatherProviderInterface
{
    public function __construct(private HttpClientInterface $ambeeDataClient, private RedisCacheService $redisCache) {}

    public function getTemperatureByGeoclocation(float $lat, float $long) : float
    {
        $cacheKey = get_class($this) . '_lat' . $lat . 'long' . $long;
        return $this->redisCache->getData($cacheKey, function() use ($lat, $long) {
            $response = $this->ambeeDataClient->request('GET', 'weather/latest/by-lat-lng', [ 
                'query' => [
                    'lat' => $lat,
                    'lng' => $long,
                    'units' => 'si'
                ]
            ]);
    
            $statusCode = $response->getStatusCode();
    
            if($statusCode !== 200) {
                throw new ClientException($response);
            }
    
            $json = json_decode($response->getContent());
            $temperature = $json->data->temperature ?? 0;

            return $temperature;
        }, static::WEATHER_CACHE_EXPIRY);
    }
}