<?php

namespace Mike4ip\Cloud\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Класс-фабрика для создания экземпляра шаблонизатора Twig
 * @package Mike4ip\Cloud\Core
 */
class Templater
{
    /**
     * Получить экземпляр шаблонизатора
     * @return Environment
     */
    public static function i(): Environment
    {
        $loader = new FilesystemLoader(__DIR__ . '/../../templates');
        $twig = new Environment($loader);
        return $twig;
    }
}