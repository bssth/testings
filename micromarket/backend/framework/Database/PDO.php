<?php

namespace Framework\Database;

use PDO as Driver;

/**
 * Обёртка для PDO, реализующая паттерн Singleton для базы данных
 */
class PDO
{
    protected static Driver $i;

    public static function i(): Driver
    {
        if(!isset(self::$i)) {
            $dsn = getenv('DATABASE');

            $opt = [
                Driver::ATTR_ERRMODE            => Driver::ERRMODE_EXCEPTION,
                Driver::ATTR_DEFAULT_FETCH_MODE => Driver::FETCH_ASSOC,
                Driver::ATTR_EMULATE_PREPARES   => false,
            ];

            self::$i = new Driver($dsn, getenv('DB_USER'), getenv('DB_PASSWORD'), $opt);
        }

        return self::$i;
    }
}
