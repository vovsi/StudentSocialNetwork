<?php

namespace app\controllers;

use app\config\ConfigAPI;
use app\services\Utils;

class FavoritesController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $data = array();
        if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $ut = new Utils();
            $authData = $ut->checkAuth();
            $data = $authData;
            $this->view->params['data'] = $authData;

            $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/favorites/getmyfavorites?limit=10&offset=0&ip="
                . $_SERVER['REMOTE_ADDR']);
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

            if (isset($dataResp['favorites'])) {
                for ($i = 0; $i < count($dataResp['favorites']); $i++) {
                    $dataResp['favorites'][$i]['photo_path'] = 'data:image/jpeg;base64,' .
                        base64_encode(file_get_contents($dataResp['favorites'][$i]['photo_path']));
                }
                $data['favorites'] = $dataResp['favorites'];
                $data['is_there_more_favorites'] = $dataResp['is_there_more_favorites'];
            }
        } else {
            $_SESSION['errors'] = ['' => 'Необходима авторизация.'];
        }

        return $this->render('index', $data);
    }

    /*public function actionRemovefavorite(){
        if(isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $ut = new Utils();
            $authData = $ut->checkAuth();
            if(!empty($authData['auth_data'])){
                $this->view->params['data'] = $authData;

                if(isset($_GET['id']) && isset($_GET['view'])){
                    $ch = curl_init("http://".Config_API::HOST_API."/v1/favorites/remove?ip=".$_SERVER['REMOTE_ADDR']);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Cookie: email='.$_COOKIE['email'].'; password='.$_COOKIE['password'].''
                    ));
                    curl_setopt($ch, CURLOPT_POSTFIELDS, //тут переменные которые будут переданы методом POST
                        array (
                            'id'=>$_GET['id']
                        ));
                    $response = curl_exec($ch);
                    $dataResult = json_decode($response, true);
                    curl_close($ch);

                    if(isset($dataResult['errors'])){
                        if(!empty($dataResult['errors'])){
                            $_SESSION['errors'] = $dataResult['errors'];
                            switch ($_GET['view']){
                                case "favorites":
                                    header("Location: /favorites");
                                    exit;
                                    break;
                                case "profile":
                                    header("Location: /" . $_GET['id']);
                                    exit;
                                    break;
                                default:
                                    break;
                            }
                        }
                    }

                    if(isset($dataResult['status'])){
                        if($dataResult['status']=="OK"){
                            switch ($_GET['view']){
                                case "favorites":
                                    header("Location: /favorites");
                                    exit;
                                    break;
                                case "profile":
                                    header("Location: /" . $_GET['id']);
                                    exit;
                                    break;
                                default:
                                    break;
                            }
                        }
                    }
                }
            }else{
                $_SESSION['errors'] = [''=>'Необходима авторизация.'];
            }
        }else{
            $_SESSION['errors'] = [''=>'Необходима авторизация.'];
        }
        //return $this->render('index', $data);
    }

    public function actionAddfavorite(){
        if(isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $ut = new Utils();
            $authData = $ut->checkAuth();
            if(!empty($authData['auth_data'])){
                $this->view->params['data'] = $authData;

                if(isset($_GET['id']) && isset($_GET['view'])){
                    $ch = curl_init("http://".Config_API::HOST_API."/v1/favorites/add");
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Cookie: email='.$_COOKIE['email'].'; password='.$_COOKIE['password'].''
                    ));
                    curl_setopt($ch, CURLOPT_POSTFIELDS, //тут переменные которые будут переданы методом POST
                        array (
                            'id'=>$_GET['id']
                        ));
                    $response = curl_exec($ch);
                    $dataResult = json_decode($response, true);
                    curl_close($ch);

                    if(isset($dataResult['errors'])){
                        if(!empty($dataResult['errors'])){
                            $_SESSION['errors'] = $dataResult['errors'];
                        }
                    }

                    if(isset($dataResult['status'])){
                        if($dataResult['status']=="OK"){
                            switch ($_GET['view']){
                                case "favorites":
                                    header("Location: /favorites");
                                    exit;
                                    break;
                                case "profile":
                                    header("Location: /" . $_GET['id']);
                                    exit;
                                    break;
                                default:
                                    break;
                            }
                        }
                    }
                }
            }else{
                $_SESSION['errors'] = [''=>'Необходима авторизация.'];
            }
        }else{
            $_SESSION['errors'] = [''=>'Необходима авторизация.'];
        }
        //return $this->render('index', $data);
    }*/
}
