<?php

namespace app\controllers;

use app\config\ConfigAPI;
use app\services\Utils;
use Yii;

class MainController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $data = array();
        if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            // Проверяем авторизацию
            $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/main/checkauth?ip=".$_SERVER['REMOTE_ADDR']);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Cookie: email=' . $_COOKIE['email'] . '; password=' . $_COOKIE['password'] . ''
            ));
            $response = curl_exec($ch);
            $data = json_decode($response, true);
            curl_close($ch);
            $this->view->params['data'] = $data;

            // Если указан id в URL, то отобразить профиль
            if (isset($_GET['id'])) {
                if (!empty($_GET['id'])) {
                    // Получаем данные профиля
                    $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/main/getprofiletouser?id=" . $_GET['id']
                        ."&ip=".$_SERVER['REMOTE_ADDR']);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Cookie: email=' . $_COOKIE['email'] . '; password=' . $_COOKIE['password'] . ''
                    ));
                    $response = curl_exec($ch);
                    $dataProfile = json_decode($response, true);
                    curl_close($ch);
					
					if (isset($dataProfile['errors'])) {
                        $_SESSION['errors'] = $dataProfile['errors'];
                        header("Location: /");
                        exit;
                    }
					
                    $dataProfile['auth_data'] = $data['auth_data'];
                    $dataProfile['profile']['photo_path'] = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($dataProfile['profile']['photo_path']));

                    // Получаем первые 10 записей профиля
                    $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/main/getposts?id=" . $_GET['id']
                        . "&limit=10&offset=0&ip=".$_SERVER['REMOTE_ADDR']);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Cookie: email=' . $_COOKIE['email'] . '; password=' . $_COOKIE['password'] . ''
                    ));
                    $response = curl_exec($ch);
                    $dataPosts = json_decode($response, true);
                    curl_close($ch);

                    $dataProfile['posts'] = $dataPosts['posts'];
                    $dataProfile['is_there_more_posts'] = $dataPosts['is_there_more_posts'];
                    if (isset($dataPosts['errors'])) {
                        $_SESSION['errors'] = $dataPosts['errors'];
                        header("Location: /");
                        exit;
                    }

                    if (isset($dataProfile['posts'])) {
                        for ($i = 0; $i < count($dataProfile['posts']); $i++) {
                            $dataProfile['posts'][$i]['photo_FROM'] = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($dataProfile['posts'][$i]['photo_FROM']));
                            if ($dataProfile['posts'][$i]['path_to_image'] != null) {
                                $dataProfile['posts'][$i]['path_to_image'] = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($dataProfile['posts'][$i]['path_to_image']));
                            }
                        }
                    }

                    return $this->render('profile', $dataProfile);
                }
            }
        }

        // Если пользователь авторизируется
        if (Yii::$app->request->isPost) {
            // Удалить прошлые данные авторизации
            setcookie('email', 0, strtotime('-1 days'), '/');
            setcookie('password', 0, strtotime('-1 days'), '/');

            $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/main/auth");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, //тут переменные которые будут переданы методом POST
                array(
                    'email' => Yii::$app->request->post()['email'],
                    'password' => Yii::$app->request->post()['password'],
                    'ip' => $_SERVER['REMOTE_ADDR']
                ));
            $response = curl_exec($ch);
            $data = json_decode($response, true);
            curl_close($ch);

            if (!isset($data['errors'])) {
                // Установить новые данные авторизации
                setcookie('email', $data['auth_data']['email'], 0, '/');
                setcookie('password', $data['auth_data']['password_hash'], 0, '/');
            }
        }

        if (isset($data['errors'])) {
            $_SESSION['errors'] = $data['errors'];
        }
        $this->view->params['data'] = $data;
        return $this->render('index', $data);
    }

    /*public function actionRemovepost(){

        if(isset($_COOKIE['email']) && isset($_COOKIE['password'])){
            if(isset($_GET['rp']))
            {
                if(!empty($_GET['rp']))
                {
                    $explodeRoute = explode('/', $_GET['rp']);
                    $explodeStr = explode('_', $explodeRoute[1]);
                    $actionQ = $explodeStr[0]; // Действие
                    $idActionQ = $explodeStr[1]; // id к которому будет выполняться действие

                    $ut = new Utils();
                    $data = $ut->getInfoProfile($explodeRoute[0], $_COOKIE['email'], $_COOKIE['password']);
                    $this->view->params['data'] = $data['auth_data'];
                    $data['refresh'] = true;

                    switch ($actionQ)
                    {
                        case "removePost": // Удаление записи
                            {
                                $ch = curl_init("http://".Config_API::HOST_API."/v1/main/removepost");
                                curl_setopt($ch, CURLOPT_POST, 1);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                    'Cookie: email='.$_COOKIE['email'].'; password='.$_COOKIE['password'].''
                                ));
                                curl_setopt($ch, CURLOPT_POSTFIELDS, //тут переменные которые будут переданы методом POST
                                    array (
                                        'idPost'=>$idActionQ,
                                    ));
                                $response = curl_exec($ch);
                                $dataResult = json_decode($response, true);
                                curl_close($ch);
                                break;
                            }
                        default:
                            break;
                    }
                }
            }
            if(isset($data['errors'])){
                $_SESSION['errors'] = $data['errors'];
            }
        }else{
            $_SESSION['errors'] = [''=>'Необходима авторизация.'];
        }
        return $this->render('profile',$data);
    }*/

    /*public function actionAddpost(){

        if(isset($_COOKIE['email']) && isset($_COOKIE['password'])){
            $ut = new Utils();
            $data = $ut->getInfoProfile($_POST['account_to_id'], $_COOKIE['email'], $_COOKIE['password']);
            $this->view->params['data'] = $data['auth_data'];
            $data['refresh'] = true;


            if(isset(Yii::$app->request->post()['account_to_id']) && isset(Yii::$app->request->post()['text'])){
                if(!empty(Yii::$app->request->post()['account_to_id'])){

                    $accountToId =  Yii::$app->request->post()['account_to_id'];
                    $message = null;
                    $videoLink = null;

                    if(isset(Yii::$app->request->post()['video_link'])){
                        if(!empty(Yii::$app->request->post()['video_link'])){
                            $videoLink = Yii::$app->request->post()['video_link'];
                        }
                    }

                    if(isset(Yii::$app->request->post()['text'])){
                        if(!empty(Yii::$app->request->post()['text'])){
                            if(iconv_strlen(Yii::$app->request->post()['text'])<3000){
                                $message = Yii::$app->request->post()['text'];
                            }else{
                                $_SESSION['errors'] = [''=>'Длина сообщения больше допустимого значения. Допускается: 3000 символов.'];
                                return $this->render('profile',$data);
                            }
                        }
                    }

                    // Если пост с картинкой
                    $base64Encode = null;
                    if(isset($_FILES["att_photoCreatePost"])){
                        if(!empty($_FILES["att_photoCreatePost"]['name'])){
                            if(Utils::checkFileImage($_FILES["att_photoCreatePost"]["name"])){
                                move_uploaded_file($_FILES["att_photoCreatePost"]["tmp_name"], $_FILES["att_photoCreatePost"]["name"]);

                                $binString = file_get_contents($_FILES["att_photoCreatePost"]["name"]);
                                $hexString = base64_encode($binString);
                                $type = pathinfo($_FILES["att_photoCreatePost"]["name"], PATHINFO_EXTENSION);
                                $base64Encode = 'data:image/' . $type . ';base64,' . $hexString;
                                unlink($_FILES["att_photoCreatePost"]["name"]);
                            }else{
                                $dataResult['errors']['file'] = "Ошибка загрузки изображения. Убедитесь что файл является изображением. Допустимые форматы: gif, png, jpg, jpeg";
                            }
                        }
                    }

                    // Send...
                    if(!isset($dataResult['errors']['file'])){
                        $ch = curl_init("http://".Config_API::HOST_API."/v1/main/addpost");
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            'Cookie: email='.$_COOKIE['email'].'; password='.$_COOKIE['password'].''
                        ));
                        curl_setopt($ch, CURLOPT_POSTFIELDS, //тут переменные которые будут переданы методом POST
                            array (
                                'account_to_id'=>Yii::$app->request->post()['account_to_id'],
                                'message'=>$message,
                                'file' =>(!empty($base64Encode)) ? $base64Encode : null,
                                'video_link' => $videoLink
                            ));
                        $response = curl_exec($ch);
                        $dataResult = json_decode($response, true);
                        curl_close($ch);
                    }
                }else{
                    $dataResult['errors'][] = 'Ошибка введенных данных.';
                }
            }else{
                $dataResult['errors'][] = 'Ошибка введенных данных.';
            }

            if(isset($dataResult['errors'])){
                $_SESSION['errors'] = $dataResult['errors'];
            }
        }else{
            $_SESSION['errors'] = [''=>'Необходима авторизация.'];
        }

        return $this->render('profile',$data);
    }*/

    public function actionUpdatephoto()
    {
        if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $ut = new Utils();
            $data = $ut->getInfoProfile($_POST['account_to_id'], $_COOKIE['email'], $_COOKIE['password']);
            $this->view->params['data'] = $data['auth_data'];
            $data['refresh'] = true;
            if (!empty($data['auth_data'])) {
                if (isset($_FILES["update_photo"])) {
                    if (!empty($_FILES["update_photo"]['name'])) {
                        if (Utils::checkFileImage($_FILES["update_photo"]["name"])) {
                            move_uploaded_file($_FILES["update_photo"]["tmp_name"], $_FILES["update_photo"]["name"]);

                            $binString = file_get_contents($_FILES["update_photo"]["name"]);
                            $hexString = base64_encode($binString);
                            $type = pathinfo($_FILES["update_photo"]["name"], PATHINFO_EXTENSION);
                            $base64Encode = 'data:image/' . $type . ';base64,' . $hexString;
                            unlink($_FILES["update_photo"]["name"]);

                            $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/main/updatephoto?ip=".$_SERVER['REMOTE_ADDR']);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                'Cookie: email=' . $_COOKIE['email'] . '; password=' . $_COOKIE['password'] . ''
                            ));
                            curl_setopt($ch, CURLOPT_POSTFIELDS, //тут переменные которые будут переданы методом POST
                                array(
                                    'file' => (!empty($base64Encode)) ? $base64Encode : null
                                ));
                            $response = curl_exec($ch);
                            $dataResult = json_decode($response, true);
                            curl_close($ch);
                            if (isset($dataResult['errors'])) {
                                $data['errors'] = $dataResult['errors'];
                            }
                        } else {
                            $data['errors'][] = 'Неверный формат файла. Допустимые форматы: gif, png, jpg, jpeg';
                        }
                    } else {
                        $data['errors'][] = 'Ошибка обнаружения файла.';
                    }
                } else {
                    $data['errors'][] = 'Ошибка обнаружения файла.';
                }
            } else {
                $_SESSION['errors'][] = 'Необходима авторизация.';
                return $this->render('index', $data);
            }
            if (isset($data['errors'])) {
                $_SESSION['errors'] = $data['errors'];
            }
        } else {
            $_SESSION['errors'] = ['' => 'Необходима авторизация.'];
        }

        return $this->render('profile', $data);
    }

    /*public function actionRemoveblacklist(){
        $data = array();
        if(isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $ut = new Utils();
            $authData = $ut->checkAuth();
            if(!empty($authData['auth_data'])){
                $data = $authData;
                $data['refresh'] = true;
                $this->view->params['data'] = $authData;

                if(isset($_GET['id']) && isset($_GET['view'])){
                    $ch = curl_init("http://".Config_API::HOST_API."/v1/main/removeblacklist");
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
                                case "dialog":
                                    header("Location: /messages/dialog?id=" . $_GET['view_id']);
                                    exit;
                                    break;
                                case "profile":
                                    header("Location: /" . $_GET['view_id']);
                                    exit;
                                    break;
                                case "settings":
                                    header("Location: /settings");
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
        return $this->render('index', $data);
    }

    public function actionAddblacklist(){
        $data = array();
        if(isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $ut = new Utils();
            $authData = $ut->checkAuth();
            if(!empty($authData['auth_data'])){
                $data = $authData;
                $data['refresh'] = true;
                $this->view->params['data'] = $authData;

                if(isset($_GET['id']) && isset($_GET['view']) && isset($_GET['view_id'])){
                    $ch = curl_init("http://".Config_API::HOST_API."/v1/main/addblacklist");
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
                                case "dialog":
                                    header("Location: /messages/dialog?id=" . $_GET['view_id']);
                                    exit;
                                    break;
                                case "profile":
                                    header("Location: /" . $_GET['view_id']);
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

        header("Location: /");
        exit;
        //return $this->render('index', $data);
    }*/

    public function actionLogout()
    {
        // Выход из аккаунта
        setcookie('email', 0, strtotime('-1 days'), '/');
        setcookie('password', 0, strtotime('-1 days'), '/');

        header("Location: /");
        exit;
    }

    public function actionError()
    {
        return $this->render("error");
    }
}
