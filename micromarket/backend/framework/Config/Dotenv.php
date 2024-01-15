<?php
namespace Framework\Config;

/**
 * Контейнер для переменных окружения с возможностью помещения в глобальную область видимости
 */
class Dotenv
{
    protected array $vars = [];

    public function loadFromFile(string $path = null): bool
    {
        if(!is_string($path)) {
            $path = __DIR__ . '/../../.env';
        }

        if(!file_exists($path)) {
            return false;
        }

        $env = parse_ini_file($path, false, INI_SCANNER_TYPED);
        if(!is_array($env)) {
            return false;
        }

        $this->vars = $env;
        return true;
    }

    /**
     * Установить полученные переменные окружения глобально
     */
    public function setGlobals(): int
    {
        $cnt = 0;

        foreach($this->vars as $k => $v)
        {
            $cnt += (int)putenv($k . '=' . $v);
        }

        return $cnt;
    }

    public function __get(string $key)
    {
        return $this->vars[$key] ?? '';
    }
}
