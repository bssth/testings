<?php
    require_once(__DIR__ . '/../vendor/autoload.php');

    // Искомая строка, которую будем искать
    $string_to_find = 'aliquam';

    // Создаём экземпляр класса-поисковика
    $finder = new Mike4ip\Strfind\Finder();

    // Устанавливаем нужный провайдер (в нашем случае тот, который позволяет искать по подстроке)
    $finder->setProvider(
        new Mike4ip\Strfind\Provider\Substr()
    );

    // Устанавливаем источник поиска (в нашем случае локальный файл)
    $finder->setLocalFile(__DIR__ . '/sample-files/example-file.txt');

    // Получаем результат
    $result = $finder->find( $string_to_find );

    if(!$result->found) {
        print("Искомая подстрока не найдена\n");
        return;
    }

    // Выводим всё, что можно
    print("Подстрока {$string_to_find} найдена\n");
    print("в строке {$result->line}, позиция {$result->row}\n");
    print("Строка, в которой найдено: {$result->string}\n");