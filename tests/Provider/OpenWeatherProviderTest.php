<?php

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Provider\Weather\OpenWeatherProvider;
use Symfony\Component\HttpClient\Exception\ClientException;

class OpenWeatherProviderTest extends KernelTestCase
{
    public function testGetProvider(): OpenWeatherProvider
    {
        $openWeatherProvider = self::getContainer()->get(OpenWeatherProvider::class);
        
        self::assertNotNull($openWeatherProvider);

        return $openWeatherProvider;
    }

    /**
     * @depends testGetProvider
     */
    public function testForecastResults(OpenWeatherProvider $openWeatherProvider)
    {
        // It's deifficult to test in general, because weather temperature is not stable and hard to predict, it could be negative or positive.
        // But we can assume that in places like Saudi Arabia, the temnprature is always positive.

        // Abu Dhabi
        $lat = 24.365906;
        $lng = 54.582223;

        $temp = $openWeatherProvider->getTemperatureByGeoclocation($lat, $lng);

        self::assertGreaterThan(0, $temp);
    }

    /**
     * @depends testGetProvider
     */
    public function testBadLatitude(OpenWeatherProvider $openWeatherProvider)
    {
        $lat = 24365906;
        $lng = 54.582223;

        $this->expectException(ClientException::class);
        $this->expectExceptionCode(400);
        $openWeatherProvider->getTemperatureByGeoclocation($lat, $lng);
    }

    /**
     * @depends testGetProvider
     */
    public function testBadLongitude(OpenWeatherProvider $openWeatherProvider)
    {
        $lat = 24.365906;
        $lng = 54582223;

        $this->expectException(ClientException::class);
        $this->expectExceptionCode(400);
        $openWeatherProvider->getTemperatureByGeoclocation($lat, $lng);
    }
}