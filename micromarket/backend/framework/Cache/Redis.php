<?php

namespace Framework\Cache;

class Redis extends \Redis
{
    protected static ?self $i = null;

    public static function i(): self
    {
        if(!self::$i) {
            self::$i = new Redis();
            self::$i->connect(
                getenv('REDIS_HOST'), getenv('REDIS_PORT')
            );
        }

        return self::$i;
    }
}
