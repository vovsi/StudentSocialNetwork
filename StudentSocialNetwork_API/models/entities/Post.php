<?php

namespace app\models\entities;

use ActiveRecord;

class Post extends ActiveRecord\Model implements EntityInterface
{
    public static $table = 'post';

    public static function tableName()
    {
        return 'post';
    }
}