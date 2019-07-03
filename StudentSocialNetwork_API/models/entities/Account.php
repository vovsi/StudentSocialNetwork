<?php

namespace app\models\entities;

use ActiveRecord;

class Account extends ActiveRecord\Model implements EntityInterface
{
    public static $table = 'account';

    public static function tableName()
    {
        return 'account';
    }
}