<?php

namespace App\Provider\Weather;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Contracts\WeatherProviderInterface;
use App\Service\RedisCacheService;
use Symfony\Component\HttpClient\Exception\ClientException;

class OpenWeatherProvider implements WeatherProviderInterface
{
    public function __construct(private HttpClientInterface $openWeatherClient, private RedisCacheService $redisCache, private string $apiKey) {}

    public function getTemperatureByGeoclocation(float $lat, float $long) : float
    {
        $cacheKey = str_replace("\\", '', get_class($this)) . '_lat' . $lat . 'long' . $long;
        return $this->redisCache->getData($cacheKey, function() use ($lat, $long) {
            $response = $this->openWeatherClient->request('GET', 'weather', [ 
                'query' => [
                    'appid' => $this->apiKey,
                    'units' => 'metric',
                    'lat' => $lat,
                    'lon' => $long,
                ]
            ]);
    
            $statusCode = $response->getStatusCode();
    
            if($statusCode !== 200) {
                throw new ClientException($response);
            }
    
            $json = json_decode($response->getContent());
    
            $temperature = $json->main->temp ?? 0;

            return $temperature;
        }, static::WEATHER_CACHE_EXPIRY);
    }
}