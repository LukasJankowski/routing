<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Loaders\Redis;

use LukasJankowski\Routing\Loaders\CacheInterface;
use Predis\Client;

final class RedisCache implements CacheInterface
{
    /**
     * RedisCache constructor.
     */
    public function __construct(private Client $client, private string $key)
    {
    }

    /**
     * @inheritdoc
     */
    public function get(): array
    {
        $routes = $this->client->get($this->key);

        return $routes === null ? [] : unserialize($routes);
    }

    /**
     * @inheritdoc
     */
    public function set(array $routes): void
    {
        $this->client->set($this->key, serialize($routes));
    }
}
