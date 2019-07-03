<?php

namespace app\models\entities;

use ActiveRecord;

class PollAnswer extends ActiveRecord\Model implements EntityInterface
{
    public static $table = 'poll_answer';

    public static function tableName()
    {
        return 'poll_answer';
    }
}