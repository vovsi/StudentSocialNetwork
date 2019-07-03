<?php

namespace app\models\entities;

use ActiveRecord;

class PollVoted extends ActiveRecord\Model implements EntityInterface
{
    public static $table = 'poll_voted';

    public static function tableName()
    {
        return 'poll_voted';
    }
}