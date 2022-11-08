<?php

namespace App\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Service\WeatherForecastService;

class WeatherForecastServiceTest extends KernelTestCase
{
    public function testGetService(): WeatherForecastService
    {
        $weatherForecastService = self::getContainer()->get(WeatherForecastService::class);
        
        self::assertNotNull($weatherForecastService);

        return $weatherForecastService;
    }

    /**
     * @depends testGetService
     */
    public function testForecastResults(WeatherForecastService $weatherForecastService)
    {
        // It's deifficult to test in general, because weather temperature is not stable and hard to predict, it could be negative or positive.
        // But we can assume that in places like Saudi Arabia, the temnprature is always positive.

        // Abu Dhabi
        $lat = 24.365906;
        $lng = 54.582223;

        $temp = $weatherForecastService->getWeatherForecast($lat, $lng);

        self::assertGreaterThan(0, $temp);
    }
}