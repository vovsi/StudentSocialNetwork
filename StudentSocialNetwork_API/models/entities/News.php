<?php

namespace app\models\entities;

use ActiveRecord;

class News extends ActiveRecord\Model implements EntityInterface
{
    public static $table = 'news';

    public static function tableName()
    {
        return 'news';
    }
}