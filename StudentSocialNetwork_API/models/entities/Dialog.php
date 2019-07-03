<?php

namespace app\models\entities;

use ActiveRecord;

class Dialog extends ActiveRecord\Model implements EntityInterface
{
    public static $table = 'dialog';

    public static function tableName()
    {
        return 'dialog';
    }
}