<?php

namespace app\models;

class ControlsAPI extends \yii\db\ActiveRecord
{
    // rest api utilities
    public const HOST = "http://18.223.123.52/"; //"http://socialnetworkforstudents.zzz.com.ua/";

    public static function checkAuth()
    {
        $db = new DBHelper();
        // Записать данные авторизации из куки (если они есть) иначе из get запроса
        $email = "";
        $pass = "";
        if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $email = $_COOKIE['email'];
            $pass = $_COOKIE['password'];
        } else {
            if (isset($_GET['email']) && isset($_GET['password'])) {
                $email = $_GET['email'];
                $pass = $_GET['password'];
            }
        }
        $res = $db->auth($email, $pass);

        //$resultArray['auth_data'] = $db->checkHashPasswords($email, $res['password_hash']);
        $resultArray = null;
        $ipUser = null;
        // Если ip указан в get запросе, то берем оттуда, инчае напрямую из запроса
        if(isset($_GET['ip'])) {
            $ipUser = $_GET['ip'];
        }else{
            // При старте на хостинге заменить верхнюю строку на следующую:
            // }else if($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']){
            $ipUser = $_SERVER['REMOTE_ADDR'];
        }
        if(!empty($ipUser)) {
            // Если ip пользователя привязан к аккаунту, то получаем его данные
            if ($db->checkIpExists($res['id'], $ipUser)) {
                $resultArray['auth_data'] = $db->checkHashPasswords($email, $res['password_hash']);
            }
        }


        // Проверить что пользователь авторизирован
        if (!empty($resultArray['auth_data'])) {
            $db->refreshVisit($resultArray['auth_data']['id']);
            $countNewDialogsMsgs = $db->getCountNotViewedDialogsMessages($resultArray['auth_data']['id']);
            $countNewConversationsMsgs = $db->getCountNotViewedConversationsMessages($resultArray['auth_data']['id']);
            $resultArray['count_new_group_msgs'] = $countNewDialogsMsgs + $countNewConversationsMsgs;

            $resultArray['photo_path'] = self::HOST . $db->getAvatarAccount($resultArray['auth_data']['id']);
        }
        return $resultArray;
    }
}