<?php

namespace app\models\entities;

use ActiveRecord;

class File extends ActiveRecord\Model implements EntityInterface
{
    public static $table = 'file';

    public static function tableName()
    {
        return 'file';
    }
}