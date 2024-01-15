<?php

namespace Mike4ip\Strfind;

use Mike4ip\Strfind\Exception\BadFile;
use Mike4ip\Strfind\Exception\NotFound;
use Mike4ip\Strfind\Exception\NotReady;
use Mike4ip\Strfind\Provider\Base;

/**
 * Главный класс для работы с библиотекой
 * @package Mike4ip\Strfind
 */
class Finder
{
    /**
     * Провайдер, который будет использован для сравнения
     * @var Base
     */
    protected $provider;

    /**
     * Путь к файлу в виде строки
     * @var string
     */
    protected $path;

    /**
     * Ресурс файла/обработчика
     * @var resource
     */
    protected $resource;

    /**
     * Требования к файлам
     * @var array
     */
    protected $requirements = [];

    /**
     * Изменить провайдер (обработчик, от которого зависит, что и как именно мы ищем)
     * @param Base $provider
     */
    public function setProvider(Base $provider): void
    {
        $this->provider = $provider;
    }

    /**
     * @param string $path
     * @return bool
     * @throws NotFound
     */
    public function setConfig(string $path): bool
    {
        if(!file_exists($path))
            throw new NotFound('Config file not found in ' . $path);

        $config = yaml_parse_file($path);

        if(isset($config['requirements']))
            $this->requirements = $config['requirements'];

        return true;
    }

    /**
     * Установить режим поиска по локальному файлу
     * @param string $path Путь к файлу
     * @return bool
     * @throws NotFound
     * @throws BadFile
     */
    public function setLocalFile(string $path): bool
    {
        if(!file_exists($path))
            throw new NotFound('Не удалось найти файл: ' . $path);

        if(count($this->requirements))
        {
            $req = $this->requirements;

            if(isset($req['mime']) && !in_array($mime = mime_content_type($path), $req['mime']))
                throw new BadFile('Bad MIME type ('.$mime.') for file ' . $path);

            if(isset($req['max_size']) && ($size = filesize($path)) > $req['max_size'])
                throw new BadFile('File '.$path.' is too large. Size: ' . $size);
        }

        return $this->setResource( fopen($path, 'r') );
    }

    /**
     * Установить произвольный ресурс в качестве источника для чтения
     * @param $resource
     * @return bool
     */
    public function setResource($resource): bool
    {
        if(!is_resource($resource))
            return false;

        $this->resource = $resource;
        return true;
    }

    /**
     * Начать поиск в файле/ресурсе по шаблону
     * @param string|null $pattern
     * @return FinderResult
     * @throws NotReady
     */
    public function find(string $pattern = null): FinderResult
    {
        if(!$this->resource || !$this->provider)
            throw new NotReady('Finder is not ready');

        $this->provider->setResource($this->resource);
        return $this->provider->find($pattern);
    }
}