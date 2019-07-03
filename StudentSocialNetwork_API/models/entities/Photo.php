<?php

namespace app\models\entities;

use ActiveRecord;

class Photo extends ActiveRecord\Model implements EntityInterface
{
    public static $table = 'photo';

    public static function tableName()
    {
        return 'photo';
    }
}