<?php
namespace Framework\Web;

use Framework\Rest\Errors\NotFound;

class Router
{
    const CONTROLLER_PREFIX = '\\App\\Api\\';
    const DEFAULT_CONTROLLER = 'Index';
    const DEFAULT_METHOD = 'index';

    protected Request $request;

    /**
     * Обрабатывает переданный запрос с побочными эффектами и отдаёт готовый ответ для вывода
     */
    public static function pass(Request $request): Response
    {
        $router = new static($request);
        $controller = $router->getMatchController();
        if(!$controller) {
            return $router->getErrorResponse($request);
        }

        $method = $router->getMatchMethod();
        if(!method_exists($controller, $method)) {
            return $router->getErrorResponse($request);
        }

        return call_user_func([$controller, $method], $request);
    }

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getMatchController(): ?Controller
    {
        if(!strlen($part = $this->getPath()[0])) {
            $class_name = self::DEFAULT_CONTROLLER;
        } else {
            $class_name = ucfirst(
                strtolower(
                    $part
                )
            );
        }

        $class_name = static::CONTROLLER_PREFIX . $class_name;
        if(!class_exists($class_name)) {
           return null;
        }

        $controller = new $class_name;
        if(!($controller instanceof Controller)) {
            return null;
        }

        return new $class_name;
    }

    public function getMatchMethod(): string
    {
        return 'action' . ucfirst(
            strtolower(
                $this->getPath()[1] ?? self::DEFAULT_METHOD
            )
        );
    }

    public function getErrorResponse(Request $request): Response
    {
        return (new NotFound())($request);
    }

    /**
     * @return string[]
     */
    public function getPath(): array
    {
        return explode('/', trim($this->request->getUri(), '/'));
    }
}
