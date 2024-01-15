<?php

namespace Mike4ip\Cloud\Core;

use Aura\Session\Segment;
use Aura\Session\SessionFactory;

/**
 * Класс-фабрика для создания экземпляра сессий
 * @package Mike4ip\Cloud\Core
 */
class Session
{
    /**
     * Создать экземпляр сессий
     * @return Segment
     */
    public static function i(): Segment
    {
        $session_factory = new SessionFactory;
        $session = $session_factory->newInstance($_COOKIE);
        $session->setName('sid');
        $segment = $session->getSegment('Cloudstorage');
        return $segment;
    }
}