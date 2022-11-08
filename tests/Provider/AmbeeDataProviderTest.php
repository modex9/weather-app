<?php

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Provider\Weather\AmbeeDataProvider;
use Symfony\Component\HttpClient\Exception\ClientException;

class AmbeeDataProviderTest extends KernelTestCase
{
    public function testGetProvider(): AmbeeDataProvider
    {
        $ambeeDataProvider = self::getContainer()->get(AmbeeDataProvider::class);
        
        self::assertNotNull($ambeeDataProvider);

        return $ambeeDataProvider;
    }

    /**
     * @depends testGetProvider
     */
    public function testForecastResults(AmbeeDataProvider $ambeeDataProvider)
    {
        // It's difficult to test in general, because weather temperature is not stable and hard to predict, it could be negative or positive.
        // But we can assume that in places like Saudi Arabia, the temnprature is always positive.

        // Abu Dhabi
        $lat = 24.365906;
        $lng = 54.582223;

        $temp = $ambeeDataProvider->getTemperatureByGeoclocation($lat, $lng);

        self::assertGreaterThan(0, $temp);
    }

    /**
     * @depends testGetProvider
     */
    public function testBadLatitude(AmbeeDataProvider $ambeeDataProvider)
    {
        $lat = 24365906;
        $lng = 54.582223;

        $this->expectException(ClientException::class);
        $this->expectExceptionCode(400);
        $ambeeDataProvider->getTemperatureByGeoclocation($lat, $lng);
    }

    /**
     * @depends testGetProvider
     */
    public function testBadLongitude(AmbeeDataProvider $ambeeDataProvider)
    {
        $lat = 24.365906;
        $lng = 54582223;

        $this->expectException(ClientException::class);
        $this->expectExceptionCode(400);
        $ambeeDataProvider->getTemperatureByGeoclocation($lat, $lng);
    }
}