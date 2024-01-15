<?php

namespace App\Validator;

/**
 * Валидатор и санитайзер для проверки и исправления переданных параметров
 * при создании/редактировании товаров
 */
class Item
{
    const MAX_ITEM_PRICE = 1000000;
    const MAX_TITLE_LENGTH = 128;
    const MAX_DESC_LENGTH = 4000;
    const MAX_IMAGE_URL_LENGTH = 512;

    public function sanitize(array $data): array
    {
        $result = [];

        foreach($data as $key => $value) {
            switch($key) {
                case 'name':
                    $value = preg_replace('/[^a-zA-Zа-яА-ЯёЁ0-9()\- ]+/', '', $value);
                    if(mb_strlen($value) > 0) {
                        $result['name'] = mb_substr($value, 0, self::MAX_TITLE_LENGTH);
                    }
                    break;

                case 'desc':
                    $result['desc'] = mb_substr($value, 0, self::MAX_DESC_LENGTH);
                    break;

                case 'price':
                    if(is_numeric($value) && $value > 0) {
                       $result['price'] =
                           $value <= static::MAX_ITEM_PRICE ? $value : static::MAX_ITEM_PRICE;
                    }
                    break;

                case 'image_url':
                    if(strlen($value) > 0
                        && strlen($value) < static::MAX_IMAGE_URL_LENGTH
                        && filter_var($value, FILTER_VALIDATE_URL)
                    ) {
                        $result['image_url'] = $value;
                    }
                    break;
            }
        }

        return $result;
    }
}
