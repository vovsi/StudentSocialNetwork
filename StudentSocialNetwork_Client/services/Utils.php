<?php

namespace app\services;

use app\config\ConfigAPI;

class Utils
{
    // Разрешенные типы файлов, для загрузки в Мои файлы
    const ALLOW_FILE_TYPES = array(
        'pdf',
        'ppt',
        'pptx',
        'rar',
        'txt',
        'doc',
        'docx',
        'dot',
        'docm',
        'dotx',
        'dotm',
        'docb',
        'xls',
        'xlt',
        'xlm',
        'xlsx',
        'xlsm',
        'xltx',
        'xltm',
        'zip'
    );

    public function checkAuth()
    {
        // Проверяем авторизацию
        $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/main/checkauth?ip=".$_SERVER['REMOTE_ADDR']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Cookie: email=' . $_COOKIE['email'] . '; password=' . $_COOKIE['password'] . ''
        ));
        $response = curl_exec($ch);
        $data = json_decode($response, true);
        //print_r($data);
        curl_close($ch);
        return $data;
    }

    public function getInfoProfile($idShow, $email, $password)
    {
        // Если указан id в URL, то отобразить профиль
        if (isset($idShow)) {
            if (!empty($idShow)) {
                $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/main/getprofiletouser?id=" . $idShow . "&ip="
                    .$_SERVER['REMOTE_ADDR']);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Cookie: email=' . $email . '; password=' . $password . ''
                ));
                $response = curl_exec($ch);
                $dataProfile = json_decode($response, true);
                curl_close($ch);
                $dataProfile['auth_data'] = $this->checkAuth()['auth_data'];
                return $dataProfile;
            }
        }
        return null;
    }

    // Проверить на допустимость загружаемой картинки
    public static function checkFileImage($filename)
    {
        $allowed = array('gif', 'png', 'jpg', 'jpeg');
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!in_array($ext, $allowed)) {
            return false;
        }
        return true;
    }

    // Проверить на допустимость загружаемого файла
    public static function checkFile($filename)
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!in_array($ext, self::ALLOW_FILE_TYPES)) {
            return false;
        }
        return true;
    }

    // Получить mime type по расширениию файла
    public static function getMimeTypeOfFile($filename)
    {
        $res = "";
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        switch ($ext) {
            case 'zip':
                $res = "application/zip";
                break;
            case 'xltm':
                $res = "application/excel";
                break;
            case 'xltx':
                $res = "application/excel";
                break;
            case 'xlsm':
                $res = "application/excel";
                break;
            case 'xlsx':
                $res = "application/excel";
                break;
            case 'xlm':
                $res = "application/excel";
                break;
            case 'xlt':
                $res = "application/excel";
                break;
            case 'xls':
                $res = "application/excel";
                break;
            case 'docb':
                $res = "application/msword";
                break;
            case 'dotm':
                $res = "application/msword";
                break;
            case 'dotx':
                $res = "application/msword";
                break;
            case 'docm':
                $res = "application/msword";
                break;
            case 'dot':
                $res = "application/msword";
                break;
            case 'doc':
                $res = "application/msword";
                break;
            case 'txt':
                $res = "text/plain";
                break;
            case 'rar':
                $res = "application/zip";
                break;
            case 'pptx':
                $res = "application/mspowerpoint";
                break;
            case 'ppt':
                $res = "application/mspowerpoint";
                break;
            case 'pdf':
                $res = "application/pdf";
                break;
            default:
                break;
        }
        return $res;
    }

    // Проверить существование пола
    public static function genderExists($gender)
    {
        $genders = [0 => 'Мужской', 1 => 'Женский'];
        if (array_search($gender, $genders) !== false) {
            return true;
        } else {
            return false;
        }
    }
}