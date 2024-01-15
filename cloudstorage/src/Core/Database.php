<?php

namespace Mike4ip\Cloud\Core;

use SafeMySQL;

/**
 * Класс-фабрика для создания экземпляра базы данных
 * @package Mike4ip\Cloud\Core
 */
class Database
{
    /**
     * Получить экземпляр базы данных
     * @return SafeMySQL
     */
    public static function i(): SafeMySQL
    {
        return new SafeMySQL([
            'host'      => getenv('db_host'),
            'user'      => getenv('db_user'),
            'pass'      => getenv('db_pass'),
            'db'        => getenv('db_name')
        ]);
    }
}