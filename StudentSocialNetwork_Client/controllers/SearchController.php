<?php

namespace app\controllers;

use app\config\ConfigAPI;
use app\services\Utils;
use Yii;

class SearchController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $data = array();
        if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $ut = new Utils();
            $authData = $ut->checkAuth();
            $data = $authData;
            $this->view->params['data'] = $authData;

            if (isset(Yii::$app->request->post()['query_text'])) {
                $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/search/users?ip=".$_SERVER['REMOTE_ADDR']);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Cookie: email=' . $_COOKIE['email'] . '; password=' . $_COOKIE['password'] . ''
                ));
                curl_setopt($ch, CURLOPT_POSTFIELDS, //тут переменные которые будут переданы методом POST
                    array(
                        'limit' => 1000,
                        'offset' => 0,
                        'query' => Yii::$app->request->post()['query_text'],
                    ));
                $response = curl_exec($ch);
                $dataResult = json_decode($response, true);
                curl_close($ch);

                if (isset($dataResult['errors'])) {
                    if (!empty($dataResult['errors'])) {
                        $_SESSION['errors'] = $dataResult['errors'];
                    }
                }

                if (isset($dataResult['result_search']) && isset($dataResult['search_text'])) {
                    $data['result_search'] = $dataResult['result_search'];
                    $data['search_text'] = $dataResult['search_text'];

                    for ($i = 0; $i < count($data['result_search']); $i++) {
                        $data['result_search'][$i]['photo_path'] = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($data['result_search'][$i]['photo_path']));
                    }
                }
            } else {
                //$_SESSION['errors'] = [''=>'Данные поиска не найдены.'];
            }
        } else {
            $_SESSION['errors'] = ['' => 'Необходима авторизация.'];
        }

        return $this->render('index', $data);
    }

}
