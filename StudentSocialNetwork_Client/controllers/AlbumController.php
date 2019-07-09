<?php

namespace app\controllers;

use app\config\ConfigAPI;
use app\services\Utils;
use Yii;

class AlbumController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $data = array();
        if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $ut = new Utils();
            $authData = $ut->checkAuth();
            if (!empty($authData['auth_data'])) {
                $data = $authData;
                $this->view->params['data'] = $authData;

                if (!empty($_GET['id'])) {
                    $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/album/getalbum?id="
                        . $_GET['id'] . "&limit=100&offset=0&ip=" . $_SERVER['REMOTE_ADDR']);
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
                    if (isset($dataResp['photos'])) {
                        for ($i = 0; $i < count($dataResp['photos']); $i++) {
                            $dataResp['photos'][$i]['path'] = 'data:image/jpeg;base64,' .
                                base64_encode(file_get_contents($dataResp['photos'][$i]['path']));
                        }

                        $data['photos'] = $dataResp['photos'];
                    }
                } else {
                    $_SESSION['errors'] = ['' => 'Не указан id.'];
                }
            } else {
                $_SESSION['errors'] = ['' => 'Необходима авторизация.'];
            }
        } else {
            $_SESSION['errors'] = ['' => 'Необходима авторизация.'];
        }
        return $this->render('index', $data);
    }

    public function actionAddphoto()
    {
        if (isset($_COOKIE['email']) && $_COOKIE['password']) {
            $ut = new Utils();
            $this->view->params['data'] = $ut->checkAuth();
        } else {
            $_SESSION['errors'] = ['' => 'Необходима авторизация.'];
            header("Location: /");
            exit;
        }
        return $this->render('addPhoto');
    }

    public function actionLoadphoto()
    {
        $errors = array();
        $data['refresh'] = true;
        if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $ut = new Utils();
            $authData = $ut->checkAuth();
            $this->view->params['data'] = $authData;

            if (isset($_FILES['photo'])) {
                if (!empty($_FILES['photo']['name'])) {
                    if (Utils::checkFileImage($_FILES['photo']['name'])) {
                        $description = null;
                        if (!empty(Yii::$app->request->post()['description'])) {
                            $description = htmlspecialchars(Yii::$app->request->post()['description']);
                        }

                        move_uploaded_file($_FILES["photo"]["tmp_name"], $_FILES["photo"]["name"]);

                        $binString = file_get_contents($_FILES["photo"]["name"]);
                        $hexString = base64_encode($binString);
                        $type = pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION);
                        $base64Encode = 'data:image/' . $type . ';base64,' . $hexString;
                        unlink($_FILES["photo"]["name"]);


                        $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/album/add?ip=" . $_SERVER['REMOTE_ADDR']);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            'Cookie: email=' . $_COOKIE['email'] . '; password=' . $_COOKIE['password'] . ''
                        ));
                        curl_setopt($ch, CURLOPT_POSTFIELDS,
                            array(
                                'file' => $base64Encode,
                                'description' => $description,
                            ));
                        $response = curl_exec($ch);
                        $dataResult = json_decode($response, true);
                        curl_close($ch);

                        if (isset($dataResult['errors'])) {
                            $errors[] = $dataResult['errors'];
                        }
                        if (isset($dataResult['status'])) {
                            if ($dataResult['status'] == "OK") {
                                header("Location: /album?id=" . $authData['auth_data']['id']);
                                exit;
                            }
                        }
                    } else {
                        $_SESSION['errors']['fileError'] = 'Формат файла недопустим. Разрешается: ' .
                            implode(", ", Utils::ALLOW_IMAGE_TYPES);
                    }
                } else {
                    $errors[] = 'Ошибка введенных данных. Проверьте что файл выбран.';
                }
            } else {
                $errors[] = 'Ошибка введенных данных. Проверьте что файл выбран.';
            }
        } else {
            $errors[] = 'Необходима авторизация.';
        }

        if (count($errors) > 0) {
            $_SESSION['errors'] = $errors;
        }

        return $this->render('addPhoto');
    }

    public function actionRemovephoto()
    {
        $errors = array();
        $data['refresh'] = true;
        if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $ut = new Utils();
            $authData = $ut->checkAuth();
            $this->view->params['data'] = $authData;

            if (isset($_GET['id'])) {
                $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/album/remove?ip=" . $_SERVER['REMOTE_ADDR']);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Cookie: email=' . $_COOKIE['email'] . '; password=' . $_COOKIE['password'] . ''
                ));
                curl_setopt($ch, CURLOPT_POSTFIELDS,
                    array(
                        'id' => $_GET['id'],
                    ));
                $response = curl_exec($ch);
                $dataResult = json_decode($response, true);
                curl_close($ch);

                if (isset($dataResult['errors'])) {
                    $errors[] = $dataResult['errors'];
                }
                if (isset($dataResult['status'])) {
                    if ($dataResult['status'] == "OK") {
                        header("Location: /album?id=" . $authData['auth_data']['id']);
                        exit;
                    }
                }
            } else {
                $errors[] = "Не найден id фото.";
            }
        } else {
            $errors[] = "Необходима авторизация.";
        }

        if (count($errors) > 0) {
            $_SESSION['errors'] = $errors;
        }

        return $this->render('index', $data);
    }
}
