<?php
    /**
     * Утилита для заполнения базы данных тестовыми записями
     */

    use App\Model\Item;

    $fill_count = 1000000;
    $image_url = 'https://file-examples-com.github.io/uploads/2017/10/file_example_JPG_100kB.jpg';
    $desc = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec pretium cursus magna, nec tincidunt metus molestie non. Cras faucibus metus diam, ut pretium elit blandit ut. Mauris ut nisi mauris. Duis varius nec quam id dictum. Donec eleifend aliquam dictum. Proin eleifend augue felis, ut fringilla mauris vehicula eu. Nulla in pulvinar tortor, a suscipit dui. Vivamus tristique egestas efficitur. Nam id bibendum felis. Vivamus pellentesque placerat dolor eu faucibus. Vivamus a elit id dolor ultricies hendrerit pharetra nec diam. Morbi odio eros, malesuada vel augue id, pellentesque eleifend arcu. Donec rhoncus maximus tortor nec varius. Fusce tempus, erat id consequat commodo, mauris nunc bibendum risus, eu dignissim nisl enim vel diam.';

    require_once(__DIR__ . '/../vendor/autoload.php');
    $dotenv = new Framework\Config\Dotenv();
    $dotenv->loadFromFile(__DIR__ . '/../.env');
    $dotenv->setGlobals();

    $count = 0;

    for($i = 0; $i < $fill_count; $i++) {
        $id = var_export(
            Item::insert([
                'name' => 'Тестовый товар ' . $i,
                'desc' => $desc,
                'price' => rand(1, 150) * 100,
                'image_url' => $image_url,
            ]), 1
        );

        if($id > 0) {
            $count++;
        }

        print("Insert record #".($i+1)." (inserted id {$id}) \n");
    }

    print("Ready! Inserted {$count} records \n\n");
