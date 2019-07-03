<?php

namespace app\models\entities;

use ActiveRecord;

class Favorite extends ActiveRecord\Model implements EntityInterface
{
    public static $table = 'favorite';

    public static function tableName()
    {
        return 'favorite';
    }
}