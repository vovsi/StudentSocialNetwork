<?php

namespace app\models\entities;

use ActiveRecord;

class VisitUser extends ActiveRecord\Model implements EntityInterface
{
    public static $table = 'visit_user';

    public static function tableName()
    {
        return 'visit_user';
    }
}