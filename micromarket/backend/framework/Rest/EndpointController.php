<?php

namespace Framework\Rest;

use Framework\Web\Controller;
use Framework\Web\Request;
use Framework\Web\Response;

/**
 * Расширенный вариант контроллера для работы с JSON REST API и хелперами для удобной отправки ответов
 */
class EndpointController extends Controller
{
    /**
     * Подсчитать время до ответа в миллисекундах
     */
    protected function fixResponseTime(Request $request): float
    {
        return round((microtime(true) - $request->getStartTime()) * 1000);
    }

    /**
     * Отправить отформатированный JSON-ответ
     */
    protected function apiResponse(Request $request, array $response): Response
    {
        return (new Response())
            ->withContentType('application/json')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'content-type')
            ->withBody(json_encode([
                'response' => $response,
                'request_time' => $this->fixResponseTime($request)
            ]));
    }

    protected function apiError(Request $request, int $code, string $text, array $data = null): Response
    {
        return (new Response())
            ->withContentType('application/json')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'content-type')
            ->withBody(json_encode([
                'error' => [
                    'code' => $code,
                    'text' => $text,
                    'data' => $data
                ],
                'request_time' => $this->fixResponseTime($request)
            ]));
    }
}
