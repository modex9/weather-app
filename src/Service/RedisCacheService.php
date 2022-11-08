<?php

namespace App\Service;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Closure;

class RedisCacheService
{
    CONST CACHE_DEFAULT_EXPIRY = 3600;

    public function __construct(private CacheInterface $cache) {}

    public function getData(string $key, Closure $closure, int $expiry = 0) : mixed
    {
        $data = $this->cache->get($key, function (ItemInterface $item, bool &$save) use ($closure, $expiry) {
            if($expiry)
                $item->expiresAfter($expiry);
            else
                $item->expiresAfter(self::CACHE_DEFAULT_EXPIRY);

            return $closure();
        });
        return $data;
    }

    public function delete(string $key) : bool
    {
        return $this->cache->delete($key);
    }

}