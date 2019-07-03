<?php

namespace app\models\entities;

use ActiveRecord;

class Privacy extends ActiveRecord\Model implements EntityInterface
{
    public static $table = 'privacy';

    public static function tableName()
    {
        return 'privacy';
    }
}