<?php

namespace Mike4ip\Strfind\Provider;

use Mike4ip\Strfind\FinderResult;

/**
 * Провайдер, позволяющий искать подстроку
 * @package Mike4ip\Strfind\Provider
 */
class Substr implements Base
{
    /**
     * @var resource
     */
    protected $resource;

    /**
     * @param $resource
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
    }

    /**
     * Совершить поиск с помощью провайдера по указанному шаблону
     * @param string $pattern
     * @return FinderResult
     */
    public function find(string $pattern = null): FinderResult
    {
        $result = new FinderResult();
        $line_number = 0;

        // читаем построчно, пока файл не закончится
        while( ($line = fgets($this->resource)) !== false )
        {
            $line_number++;
            $substr_pos = mb_strpos($line, $pattern);

            if($substr_pos !== false) {
                $result->found = true;
                $result->line = $line_number;
                $result->row = $substr_pos;
                $result->string = $line;
                break;
            }
        }

        return $result;
    }
}