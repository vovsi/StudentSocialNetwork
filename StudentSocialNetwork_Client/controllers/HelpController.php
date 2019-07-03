<?php

namespace app\controllers;

use app\services\Utils;

class HelpController extends \yii\web\Controller
{
    public function actionIndex()
    {
        if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $ut = new Utils();
            $authData = $ut->checkAuth();
            $data = $authData;
            $this->view->params['data'] = $authData;

            if (!empty($authData['auth_data'])) {
                return $this->render('index', $data);
            } else {
                $_SESSION['errors'] = ['' => 'Необходима авторизация.'];
            }
        } else {
            $_SESSION['errors'] = ['' => 'Необходима авторизация.'];
        }
        header("Location: /");
        exit;
    }

    public function actionRegister()
    {
        $data = array();
        if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $ut = new Utils();
            $authData = $ut->checkAuth();
            if (!empty($authData['auth_data'])) {
                $this->view->params['data'] = $authData;
            }
        } else {
            $this->view->params['data'] = $data;
        }
        return $this->render('register', $data);
    }

}
