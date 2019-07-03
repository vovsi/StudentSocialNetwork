<?php

namespace app\config;

class ConfigDB
{
    const HOST = 'localhost';
    const PORT = '3306';
    const USER = 'root';
    const PASS = '';

    public static function getConnectionString($database, $driver = 'mysql')
    {
        return $driver . "://" . self::USER . ":" . self::PASS . "@" . self::HOST . "/" . $database . "?charset=utf8";
    }
}