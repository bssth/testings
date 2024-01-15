<?php

namespace Framework\Emitter;

use Framework\Web\Response;

interface Base
{
    public function emit(Response $response): void;
}
