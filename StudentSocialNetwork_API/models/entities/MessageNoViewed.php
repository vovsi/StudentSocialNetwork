<?php

namespace app\models\entities;

use ActiveRecord;

class MessageNoViewed extends ActiveRecord\Model implements EntityInterface
{
    public static $table = 'message_no_viewed';

    public static function tableName()
    {
        return 'message_no_viewed';
    }
}