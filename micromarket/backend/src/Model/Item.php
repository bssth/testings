<?php

namespace App\Model;
use Framework\Database\Model;

class Item extends Model
{
    public static function getTableName(): string
    {
        return 'items';
    }
}
