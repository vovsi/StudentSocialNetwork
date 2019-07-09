<?php

namespace app\models\entities;

interface EntityInterface
{
    // Название таблицы в б/д
    public static function tableName();
}