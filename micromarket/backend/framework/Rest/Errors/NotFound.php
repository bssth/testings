<?php

namespace Framework\Rest\Errors;

use Framework\Rest\EndpointController;
use Framework\Web\Request;
use Framework\Web\Response;

/**
 * Стандартный обработчик ошибки 404 для Rest API
 */
final class NotFound extends EndpointController
{
    public function __invoke(Request $request): Response
    {
        return $this->apiError($request, 404, 'Endpoint not found');
    }
}
