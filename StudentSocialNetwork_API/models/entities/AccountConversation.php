<?php

namespace app\models\entities;

use ActiveRecord;

class AccountConversation extends ActiveRecord\Model implements EntityInterface
{
    public static $table = 'account_conversation';

    public static function tableName()
    {
        return 'account_conversation';
    }
}