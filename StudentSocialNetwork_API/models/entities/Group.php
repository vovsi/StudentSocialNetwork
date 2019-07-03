<?php

namespace app\models\entities;

use ActiveRecord;

class Group extends ActiveRecord\Model implements EntityInterface
{
    public static $table = 'group';

    public static function tableName()
    {
        return 'group';
    }
}