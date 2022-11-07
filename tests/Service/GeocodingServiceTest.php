<?php

namespace App\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Service\GeocodingService;
use App\Utils\DataKeys;
use App\Entity\Location;
use LogicException;

class GeocodingServiceTest extends KernelTestCase
{
    public function testGetService(): GeocodingService
    {
        $geocodingService = self::getContainer()->get(GeocodingService::class);
        
        self::assertNotNull($geocodingService);

        return $geocodingService;
    }

    /**
     * @depends testGetService
     */
    public function testDataRetrieved(GeocodingService $geocodingService)
    {
        // Valid location. Expect non-zero coordinates.
        $location = new Location();
        $location->setCountry('Lietuva');
        $location->setCity('Kaunas');
        $data = $geocodingService->getGeocodingData($location);

        self::assertNotEquals(0, $data[DataKeys::LAT_KEY]);
        self::assertNotEquals(0, $data[DataKeys::LONG_KEY]);

        // Fake location. Expect zero coordinates.
        $location = new Location();
        $location->setCountry('LietuvaXX');
        $location->setCity('KaunasXX');
        $data = $geocodingService->getGeocodingData($location);

        self::assertSame(0, $data[DataKeys::LAT_KEY]);
        self::assertSame(0, $data[DataKeys::LONG_KEY]);
    }

    /**
     * @depends testGetService
     */
    public function testCountryException(GeocodingService $geocodingService)
    {
        // Country missing. Expect logic exception.
        $location = new Location();
        $location->setCity('Kaunas');
        $this->expectException(LogicException::class);
        $geocodingService->getGeocodingData($location);
    }

    /**
     * @depends testGetService
     */
    public function testCityException(GeocodingService $geocodingService)
    {
        // City missing. Expect logic exception.
        $location = new Location();
        $location->setCountry('Lietuva');
        $this->expectException(LogicException::class);
        $geocodingService->getGeocodingData($location);
    }
}