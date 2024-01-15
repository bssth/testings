<?php

namespace Mike4ip\Strfind\Provider;

use Mike4ip\Strfind\FinderResult;

/**
 * Interface Base
 * @package Mike4ip\Strfind\Provider
 */
interface Base
{
    /**
     * Установить ресурс, в котором будем искать.
     * Обычно это делается в классе Finder, вручную
     * пользоваться функцией без надобности не стоит.
     * @param $resource
     */
    public function setResource($resource);

    /**
     * Совершить поиск с помощью провайдера по указанному шаблону
     * @param string $pattern
     * @return FinderResult
     */
    public function find(string $pattern = null): FinderResult;
}