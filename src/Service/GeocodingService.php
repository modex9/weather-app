<?php

namespace App\Service;

use App\Entity\Location;
use LogicException;
use App\Service\RedisCacheService;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\Exception\ClientException;
use App\Utils\DataKeys;

class GeocodingService
{
    public function __construct(private HttpClientInterface $positionStackClient, private RedisCacheService $redisCache, private string $accessKey) {
        $this->positionStackClient = $positionStackClient;
        $this->redisCache = $redisCache;
        $this->accessKey = $accessKey;
    }

    public function getGeocodingData(Location $location) : array
    {
        $country = $location->getCountry();
        $city = $location->getCity();

        if(!$country || !$city)
            throw new LogicException('Country and city must be provided to get the geocoding data.');

        $cacheKey = $country . '_' . $city;

        return $this->redisCache->getData($cacheKey, function() use ($country, $city) {
            $response = $this->positionStackClient->request('GET', 'forward', [ 
                'query' => [
                    'query' => $country . ',' . $city,
                    'access_key' => $this->accessKey
                ]
            ]);
    
            $statusCode = $response->getStatusCode();
    
            if($statusCode !== 200) {
                throw new ClientException($response);
            }
    
            $json = json_decode($response->getContent());
    
            $data[DataKeys::LAT_KEY] = $json->data[0]->{DataKeys::LAT_KEY} ?? 0;
            $data[DataKeys::LONG_KEY] = $json->data[0]->{DataKeys::LONG_KEY} ?? 0;
    
            return $data;
        });
    }

}