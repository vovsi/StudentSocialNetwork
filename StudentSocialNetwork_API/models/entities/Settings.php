<?php

namespace app\models\entities;

use ActiveRecord;

class Settings extends ActiveRecord\Model implements EntityInterface
{
    public static $table = 'settings';

    public static function tableName()
    {
        return 'settings';
    }
}