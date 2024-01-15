<?php
    /**
     * Единая точка входа для веб-приложения
     * Здесь объявляются все роуты и прочие настройки
     */

    require_once(__DIR__ . '/../vendor/autoload.php');

    mb_internal_encoding("UTF-8");
    date_default_timezone_set('Etc/GMT+3');

    try {
        $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->load();

        $request = Zend\Diactoros\ServerRequestFactory::fromGlobals(
            $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
        );

        $router = new League\Route\Router;

        $router->map('GET', '/', [Mike4ip\Cloud\Controller\Home::class, 'index']);
        $router->map('GET', '/login', [Mike4ip\Cloud\Controller\Home::class, 'login']);
        $router->map('GET', '/logout', [Mike4ip\Cloud\Controller\Home::class, 'logout']);
        $router->map('GET', '/register', [Mike4ip\Cloud\Controller\Home::class, 'register']);
        $router->map('POST', '/login', [Mike4ip\Cloud\Controller\Home::class, 'login_do']);
        $router->map('POST', '/register', [Mike4ip\Cloud\Controller\Home::class, 'register_do']);

        $router->map('POST', '/upload', [Mike4ip\Cloud\Controller\File::class, 'upload']);

        $response = $router->dispatch($request);
        (new Zend\HttpHandlerRunner\Emitter\SapiEmitter)->emit($response);
    } catch (Exception $e) {
        print(
            Mike4ip\Cloud\Core\Templater::i()->load('error.html')->render()
        );
    }