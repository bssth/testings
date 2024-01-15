<?php

namespace Framework\Web;

/**
 * Представляет HTTP-запрос. Чаще всего данные берутся из суперглобальных переменных, но
 * этот класс позволяет обрабатывать запросы в любом окружении (например, RoadRunner, Swoole и т.д.)
 */
class Request
{
    /**
     * Изначальный URI
     */
    protected string $uri;

    /**
     * Время получения запроса (можно использовать для подсчёта времени ответа)
     */
    protected float $start_time;

    protected object $get;

    protected object $post;

    /**
     * Получить запрос на основе данных из суперглобальных переменных (для окружений Apache, PHP-FPM и т.п.)
     */
    public static function fromGlobals(): self
    {
        $uri = $_SERVER['REQUEST_URI'];
        if(strpos($uri, '?') !== false) {
            $uri = strstr($uri, '?', true);
        }

        return new static($uri, $_SERVER['REQUEST_TIME_FLOAT'], $_GET, $_POST);
    }

    public function __construct(string $uri, float $start_time = 0, ?array $get = null, ?array $post = null)
    {
        $this->uri = $uri;
        $this->start_time = $start_time;

        // Массивы преобразовываются в объекты для красоты кода в тех местах, где мы получаем нужные значения
        $this->get = (object)($get ?? []);
        $this->post = (object)($post ?? []);
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getStartTime(): float
    {
        return $this->start_time;
    }

    public function getQuery(): object
    {
        return $this->get;
    }

    public function getPost(): object
    {
        return $this->post;
    }
}
