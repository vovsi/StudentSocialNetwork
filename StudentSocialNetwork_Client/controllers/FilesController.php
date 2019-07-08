<?php

namespace app\controllers;

use app\config\ConfigAPI;
use app\services\Utils;

class FilesController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $data = array();
        if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $ut = new Utils();
            $authData = $ut->checkAuth();
            $data = $authData;
            $this->view->params['data'] = $authData;

            $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/files/getfiles?ip=".$_SERVER['REMOTE_ADDR']);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Cookie: email=' . $_COOKIE['email'] . '; password=' . $_COOKIE['password'] . ''
            ));
            $response = curl_exec($ch);
            $dataResp = json_decode($response, true);
            curl_close($ch);

            if (isset($dataResp['errors'])) {
                if (!empty($dataResp['errors'])) {
                    $_SESSION['errors'] = $dataResp['errors'];
                }
            }

            $data['files'] = $dataResp['files'];

        } else {
            $_SESSION['errors'] = ['' => 'Необходима авторизация.'];
        }

        return $this->render('index', $data);
    }

    public function actionLoad()
    {
        if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $ut = new Utils();
            $authData = $ut->checkAuth();
            $this->view->params['data'] = $authData;

            if (isset($_FILES['load_file'])) {
                if (!empty($_FILES['load_file']['name'])) {
                    if (Utils::checkFile($_FILES['load_file']['name'])) {
                        move_uploaded_file($_FILES["load_file"]["tmp_name"], $_FILES["load_file"]["name"]);

                        $binString = file_get_contents($_FILES["load_file"]["name"]);
                        $hexString = base64_encode($binString);
                        $type = pathinfo($_FILES["load_file"]["name"], PATHINFO_EXTENSION);
                        //$mimeType = Utils::getMimeTypeOfFile($type);
                        $base64Encode = 'data:application/' . $type . ';base64,' . $hexString;
                        unlink($_FILES["load_file"]["name"]);

                        $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/files/load?ip=".$_SERVER['REMOTE_ADDR']);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            'Cookie: email=' . $_COOKIE['email'] . '; password=' . $_COOKIE['password'] . ''
                        ));
                        curl_setopt($ch, CURLOPT_POSTFIELDS, //тут переменные которые будут переданы методом POST
                            array(
                                'fileName' => $_FILES["load_file"]["name"],
                                'file' => $base64Encode
                            ));
                        $response = curl_exec($ch);
                        $dataResult = json_decode($response, true);
                        curl_close($ch);

                        if (isset($dataResult['errors'])) {
                            $_SESSION['errors'] = $dataResult['errors'];
                        }
                        if (isset($dataResult['status'])) {
                            if ($dataResult['status'] == "OK") {
                                header("Location: /files");
                                exit;
                            }
                        }
                    } else {
                        $_SESSION['errors']['fileError'] = 'Формат файла недопустим. Разрешается: \'pdf\', \'ppt\', 
                        \'pptx\', \'rar\', \'txt\', \'doc\', \'docx\', \'dot\', \'docm\', \'dotx\', \'dotm\', \'docb\',
                         \'xls\', \'xlt\', \'xlm\', \'xlsx\', \'xlsm\', \'xltx\', \'xltm\', \'zip\'';
                    }
                } else {
                    $_SESSION['errors'][] = 'Ошибка введенных данных. Проверьте что файл выбран.';
                }
            } else {
                $_SESSION['errors'][] = 'Ошибка введенных данных. Проверьте что файл выбран.';
            }
        } else {
            $_SESSION['errors'][] = 'Необходима авторизация.';
        }

        header("Location: /files");
        exit;
    }
}
