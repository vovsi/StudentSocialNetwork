<?php

namespace app\models;

class ControlsAPI extends \yii\db\ActiveRecord
{
    // rest api utilities
    public const HOST = "http://13.59.143.75/"; //"http://socialnetworkforstudents.zzz.com.ua/";

    public static function checkAuth()
    {
        $db = new DBHelper();
        // Записать данные авторизации из куки (если они есть)
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

        $resultArray = null;
        // Если ip пользователя привязан к аккаунту, то получаем его данные
        $resultArray['auth_data'] = $db->checkHashPasswords($email, $res['password_hash']);
        /*if ($db->checkIpExists($res['id'], $_SERVER['REMOTE_ADDR'])) {
            $resultArray['auth_data'] = $db->checkHashPasswords($email, $res['password_hash']);
        }*/

        // Проверить что пользователь авторизирован
        if (!empty($resultArray['auth_data'])) {
            $db->refreshVisit($resultArray['auth_data']['id']);
            $countNewDialogsMsgs = $db->getCountNotViewedDialogsMessages($resultArray['auth_data']['id']);
            $countNewConversationsMsgs = $db->getCountNotViewedDialogsMessages($resultArray['auth_data']['id']);
            $resultArray['count_new_group_msgs'] = $countNewDialogsMsgs + $countNewConversationsMsgs;

            $resultArray['photo_path'] = self::HOST . $db->getAvatarAccount($resultArray['auth_data']['id']);
        }
        return $resultArray;
    }
}