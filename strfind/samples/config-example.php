<?php
    require_once(__DIR__ . '/../vendor/autoload.php');

    // Создаём экземпляр класса-поисковика
    $finder = new Mike4ip\Strfind\Finder();

    // Задаём путь к файлу конфигурации
    $finder->setConfig(__DIR__ . '/sample-files/example-config.yml');

    // Задаём в качестве файла example-json.json
    // Здесь ничего плохого не случится - файл нам подходит
    $finder->setLocalFile(__DIR__ . '/sample-files/example-json.json');

    // Задаём в качестве файла example-file.txt
    // В данном случае получим ошибку, ведь файл не соответствует требованиям
    try {
        $finder->setLocalFile(__DIR__ . '/sample-files/example-file.txt');
    } catch (Exception $e) {
        print('Предусмотренная проблема со вторым файлом: ' . $e->getMessage() . PHP_EOL);
    }