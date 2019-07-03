<?php

namespace app\models\entities;

use ActiveRecord;

class AccountIp extends ActiveRecord\Model implements EntityInterface
{
    public static $table = 'account_ip';

    public static function tableName()
    {
        return 'account_ip';
    }
}