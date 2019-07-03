<?php

namespace app\models\entities;

use ActiveRecord;

class BlackList extends ActiveRecord\Model implements EntityInterface
{
    public static $table = 'black_list';

    public static function tableName()
    {
        return 'black_list';
    }
}