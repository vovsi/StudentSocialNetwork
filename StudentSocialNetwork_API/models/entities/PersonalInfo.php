<?php

namespace app\models\entities;

use ActiveRecord;

class PersonalInfo extends ActiveRecord\Model implements EntityInterface
{
    public static $table = 'personal_info';

    public static function tableName()
    {
        return 'personal_info';
    }
}