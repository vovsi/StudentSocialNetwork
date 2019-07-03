<?php

namespace app\models\entities;

use ActiveRecord;

class Poll extends ActiveRecord\Model implements EntityInterface
{
    public static $table = 'poll';

    public static function tableName()
    {
        return 'poll';
    }
}