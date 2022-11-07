<?php

namespace App\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Service\RedisCacheService;
use App\Utils\DataKeys;
use App\Entity\Location;
use LogicException;

class RedisCacheServiceTest extends KernelTestCase
{
    public function testGetService(): RedisCacheService
    {
        $redisCacheService = self::getContainer()->get(RedisCacheService::class);
        
        self::assertNotNull($redisCacheService);

        return $redisCacheService;
    }

    /**
     * @depends testGetService
     */
    public function testCaching(RedisCacheService $redisCacheService)
    {
        $cacheKey = 'cacheKey';

        $location = new Location();
        $location->setCountry('Poland');

        $data = $redisCacheService->getData($cacheKey, function() use($location) {
            return $location->getCountry();
        });
        self::assertSame('Poland', $data);


        $location->setCountry('Lithuania');

        // Still expect Poland, because data was cached.
        $data = $redisCacheService->getData($cacheKey, function() use($location) {
            return $location->getCountry();
        });
        self::assertSame('Poland', $data);

        $result = $redisCacheService->delete($cacheKey);
        self::assertTrue($result);

        // After data was delete from cache, expect new data to be saved.
        $data = $redisCacheService->getData($cacheKey, function() use($location) {
            return $location->getCountry();
        });
        self::assertSame('Lithuania', $data);

        // Clear
        $redisCacheService->delete($cacheKey);
    }
}