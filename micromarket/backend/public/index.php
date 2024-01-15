<?php
    /**
     * Главная точка входа для бэкенда. Передаёт глобальные переменные в роутер.
     */

    require_once(__DIR__ . '/../vendor/autoload.php');

    $dotenv = new Framework\Config\Dotenv();
    $dotenv->loadFromFile(__DIR__ . '/../.env');
    $dotenv->setGlobals();

    $request = Framework\Web\Request::fromGlobals();
    $response = Framework\Web\Router::pass($request);

    $emitter = new Framework\Emitter\DefaultWeb();
    $emitter->emit($response);
