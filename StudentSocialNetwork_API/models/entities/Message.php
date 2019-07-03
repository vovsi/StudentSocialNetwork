<?php

namespace app\models\entities;

use ActiveRecord;

class Message extends ActiveRecord\Model implements EntityInterface
{
    public static $table = 'message';

    public static function tableName()
    {
        return 'message';
    }
}