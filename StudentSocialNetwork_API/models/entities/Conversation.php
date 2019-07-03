<?php

namespace app\models\entities;

use ActiveRecord;

class Conversation extends ActiveRecord\Model implements EntityInterface
{
    public static $table = 'conversation';

    public static function tableName()
    {
        return 'conversation';
    }
}