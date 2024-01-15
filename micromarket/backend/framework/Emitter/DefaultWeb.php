<?php

namespace Framework\Emitter;

use Framework\Web\Response;

/**
 * Публикующий класс для стандартного окружения (веб через Apache/PHP-FPM с обычным выводом через print)
 */
class DefaultWeb implements Base
{
    public function emit(Response $response): void
    {
        ob_start();

        $headers = array_merge(
            $response->getHeaders(), ['Content-Type' => $response->getContentType()]
        );

        foreach($headers as $key => $value) {
            header(trim($key) . ': ' . trim($value));
        }

        ob_clean();
        print($response->getBody());
    }
}
