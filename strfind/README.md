# strfind

[![Latest Stable Version](https://poser.pugx.org/mikechip/strfind/version)](https://packagist.org/packages/mikechip/strfind)
[![Total Downloads](https://poser.pugx.org/mikechip/strfind/downloads)](https://packagist.org/packages/mikechip/strfind)
[![Latest Unstable Version](https://poser.pugx.org/mikechip/strfind/v/unstable)](//packagist.org/packages/mikechip/strfind)
[![License](https://poser.pugx.org/mikechip/strfind/license)](https://packagist.org/packages/mikechip/strfind)
[![composer.lock available](https://poser.pugx.org/mikechip/strfind/composerlock)](https://packagist.org/packages/mikechip/strfind)
[![Build Status](https://travis-ci.org/mikechip/strfind.svg?branch=master)](https://travis-ci.org/mikechip/strfind)
[![Coverage Status](https://coveralls.io/repos/github/mikechip/strfind/badge.svg?branch=master)](https://coveralls.io/github/mikechip/strfind?branch=master)

Библиотека предназначена для поиска подстроки в файлах
и возвращения её позиции. 

Доступна установка через Composer:

```bash
    composer require mikechip/strfind
```

Основу составляет класс Finder, экземпляр которого
нужно создать для работы:

```php
    $finder = new Mike4ip\Strfind\Finder();
```

С помощью методов этого же класса нужно задать объект
для поиска. Например, в случае с локальным файлом
это делается так:

```php
    $finder->setLocalFile(__DIR__ . '/example-file.txt');
```

Последнее, что нужно сделать перед самим поиском - это
задать провайдер. Провайдеры используются для реализации
самой логики поиска. Например, провайдер Substr будет
просто искать в заданном источнике подстроку и вернёт
результат - строку и позицию, где она была найдена.

Задаётся провайдер следующим образом:

```php
    $finder->setProvider(
        new Mike4ip\Strfind\Provider\Substr()
    );
```

После этого наконец-то совершается сам поиск:

```php
     $result = $finder->find( 'Lorem ipsum' );
```

Любой поиск возвращает объект FinderResult, поля которого
содержат исчерпывающую информацию о результатах поиска.

```php
    print("Подстрока найдена\n");
    print("в строке {$result->line}, позиция {$result->row}\n");
    print("Строка, в которой найдено: {$result->string}\n");
```

Больше примеров можно увидеть в папке samples.