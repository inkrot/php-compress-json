<?php

namespace CompressJson\Memory;

class InMemoryMemory extends AbstractMemory
{
    private function __construct(StoreInterface $store, CacheInterface $cache)
    {
        parent::__construct($store, $cache);
    }

    static function create(): InMemoryMemory {
        return new self(
            new InMemoryStore(),
            new InMemoryCache(),
        );
    }
}