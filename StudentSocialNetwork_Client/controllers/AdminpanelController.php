<?php

namespace app\controllers;

use app\config\ConfigAPI;
use app\services\Utils;
use Yii;

class AdminpanelController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $data = array();
        if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $ut = new Utils();
            $authData = $ut->checkAuth();
            $data = $authData;
            $this->view->params['data'] = $authData;

            if (!empty($authData['auth_data'])) {
                if ($authData['auth_data'] != null && $authData['auth_data']['role'] == 'admin') {
                    // Получение списка групп
                    $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/adminpanel/getgroups?ip=" . $_SERVER['REMOTE_ADDR']);
                    curl_setopt($ch, CURLOPT_POST, 0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Cookie: email=' . $_COOKIE['email'] . '; password=' . $_COOKIE['password'] . ''
                    ));
                    $response = curl_exec($ch);
                    $dataResult = json_decode($response, true);
                    curl_close($ch);

                    if (isset($dataResult['errors'])) {
                        if (!empty($dataResult['errors'])) {
                            $_SESSION['errors'] = $dataResult['errors'];
                        }
                    }

                    if (isset($dataResult['groups'])) {
                        $data['groups'] = $dataResult['groups'];
                    }

                    // Получение списка админов
                    $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/adminpanel/getadmins?ip=" . $_SERVER['REMOTE_ADDR']);
                    curl_setopt($ch, CURLOPT_POST, 0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Cookie: email=' . $_COOKIE['email'] . '; password=' . $_COOKIE['password'] . ''
                    ));
                    $response = curl_exec($ch);
                    $dataResult = json_decode($response, true);
                    curl_close($ch);

                    if (isset($dataResult['errors'])) {
                        if (!empty($dataResult['errors'])) {
                            $_SESSION['errors'] = $dataResult['errors'];
                        }
                    }

                    if (isset($dataResult['admins'])) {
                        $data['admins'] = $dataResult['admins'];
                    }
                } else {
                    $_SESSION['errors'][] = "Доступно только администрации.";
                }
            } else {
                $_SESSION['errors'][] = "Необходима авторизация.";
            }
        } else {
            $_SESSION['errors'][] = "Необходима авторизация.";
        }

        return $this->render('index', $data);
    }

    public function actionRegistrationaccount()
    {
        if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $ut = new Utils();
            $authData = $ut->checkAuth();
            $this->view->params['data'] = $authData;

            if (!empty($authData['auth_data'])) {
                if ($authData['auth_data'] != null && $authData['auth_data']['role'] == 'admin') {
                    if (isset(Yii::$app->request->post()['first_name']) && isset(Yii::$app->request->post()['last_name']) &&
                        isset(Yii::$app->request->post()['patronymic']) && isset(Yii::$app->request->post()['group']) &&
                        isset(Yii::$app->request->post()['role']) && isset(Yii::$app->request->post()['gender']) &&
                        isset(Yii::$app->request->post()['email'])) {
                        if (!empty(Yii::$app->request->post()['first_name']) && !empty(Yii::$app->request->post()['last_name']) &&
                            !empty(Yii::$app->request->post()['patronymic']) && !empty(Yii::$app->request->post()['group']) &&
                            !empty(Yii::$app->request->post()['role']) && !empty(Yii::$app->request->post()['gender']) &&
                            !empty(Yii::$app->request->post()['email'])) {
                            if (1 == preg_match(Utils::REGEX_VALID_EMAIL, Yii::$app->request->post()['email'])) {
                                if (iconv_strlen(Yii::$app->request->post()['first_name']) < 100 &&
                                    iconv_strlen(Yii::$app->request->post()['last_name']) < 100 &&
                                    iconv_strlen(Yii::$app->request->post()['patronymic']) < 100) {

                                    $ch = curl_init("http://" . ConfigAPI::HOST_API .
                                        "/v1/adminpanel/registrationaccount?ip=" . $_SERVER['REMOTE_ADDR']);
                                    curl_setopt($ch, CURLOPT_POST, 1);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                        'Cookie: email=' . $_COOKIE['email'] . '; password=' . $_COOKIE['password'] . ''
                                    ));
                                    curl_setopt($ch, CURLOPT_POSTFIELDS,
                                        //тут переменные которые будут переданы методом POST
                                        array(
                                            'first_name' => Yii::$app->request->post()['first_name'],
                                            'last_name' => Yii::$app->request->post()['last_name'],
                                            'patronymic' => Yii::$app->request->post()['patronymic'],
                                            'group' => Yii::$app->request->post()['group'],
                                            'role' => Yii::$app->request->post()['role'],
                                            'gender' => Yii::$app->request->post()['gender'],
                                            'email' => Yii::$app->request->post()['email'],
                                        ));
                                    $response = curl_exec($ch);
                                    $dataResult = json_decode($response, true);
                                    curl_close($ch);

                                    if (isset($dataResult['errors'])) {
                                        $_SESSION['errors'] = $dataResult['errors'];
                                    }
                                    if (isset($dataResult['status'])) {
                                        if ($dataResult['status'] == "OK") {
                                            $_SESSION['about_action'][] = "Пользователь успешно зарегистрирован! 
                                                Запишите следующие данные авторизации:";
                                            $_SESSION['password_reg'] = $dataResult['password'];
                                            $_SESSION['email_reg'] = Yii::$app->request->post()['email'];
                                        }
                                    }
                                } else {
                                    $_SESSION['errors'][] = 'Имя, фамилия и отчество не должно содержать более 100 символов.';
                                }
                            } else {
                                $_SESSION['errors'][] = 'Неверный формат email.';
                            }
                        } else {
                            $_SESSION['errors'][] = 'Вы не заполнили все поля.';
                        }
                    } else {
                        $_SESSION['errors'][] = 'Вы не заполнили все поля.';
                    }
                } else {
                    $_SESSION['errors'][] = 'Доступно только администрации.';
                }
            } else {
                $_SESSION['errors'][] = 'Необходима авторизация.';
            }
        } else {
            $_SESSION['errors'][] = 'Необходима авторизация.';
        }

        header("Location: /adminpanel");
        exit;
    }

    public function actionBlockaccount()
    {
        if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $ut = new Utils();
            $authData = $ut->checkAuth();
            $this->view->params['data'] = $authData;

            if (!empty($authData['auth_data'])) {
                if ($authData['auth_data'] != null && $authData['auth_data']['role'] == 'admin') {
                    if (isset(Yii::$app->request->post()['email']) && isset(Yii::$app->request->post()['actionBlock'])) {
                        if (Yii::$app->request->post()['actionBlock'] == "Заблокировать") {
                            if (Yii::$app->request->post()['email'] != $authData['auth_data']['email']) {
                                if (!empty(Yii::$app->request->post()['email'])) {
                                    $ch = curl_init("http://" . ConfigAPI::HOST_API .
                                        "/v1/adminpanel/blockaccount?ip=" . $_SERVER['REMOTE_ADDR']);
                                    curl_setopt($ch, CURLOPT_POST, 1);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                        'Cookie: email=' . $_COOKIE['email'] . '; password=' . $_COOKIE['password'] . ''
                                    ));
                                    curl_setopt($ch, CURLOPT_POSTFIELDS,
                                        // Тут переменные которые будут переданы методом POST
                                        array(
                                            'email' => Yii::$app->request->post()['email'],
                                        ));
                                    $response = curl_exec($ch);
                                    $dataResult = json_decode($response, true);
                                    curl_close($ch);

                                    if (isset($dataResult['errors'])) {
                                        $_SESSION['errors'] = $dataResult['errors'];
                                    }
                                    if (isset($dataResult['status'])) {
                                        if ($dataResult['status'] == "OK") {
                                            $_SESSION['about_action'][] = "Аккаунт успешно заблокирован.";
                                        }
                                    }
                                } else {
                                    $_SESSION['errors'][] = 'Вы не заполнили все поля.';
                                }
                            } else {
                                $_SESSION['errors'][] = 'Блокировать себя запрещено.';
                            }
                        } else {
                            if (Yii::$app->request->post()['actionBlock'] == "Разблокировать") {
                                if (Yii::$app->request->post()['email'] != $authData['auth_data']['email']) {
                                    if (!empty(Yii::$app->request->post()['email'])) {
                                        $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/adminpanel/unblockaccount?ip="
                                            . $_SERVER['REMOTE_ADDR']);
                                        curl_setopt($ch, CURLOPT_POST, 1);
                                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                            'Cookie: email=' . $_COOKIE['email'] . '; password=' . $_COOKIE['password'] . ''
                                        ));
                                        curl_setopt($ch, CURLOPT_POSTFIELDS,
                                            array(
                                                'email' => Yii::$app->request->post()['email'],
                                            ));
                                        $response = curl_exec($ch);
                                        $dataResult = json_decode($response, true);
                                        curl_close($ch);

                                        if (isset($dataResult['errors'])) {
                                            $_SESSION['errors'] = $dataResult['errors'];
                                        }
                                        if (isset($dataResult['status'])) {
                                            if ($dataResult['status'] == "OK") {
                                                $_SESSION['about_action'][] = "Аккаунт успешно разблокирован.";
                                            }
                                        }
                                    } else {
                                        $_SESSION['errors'][] = 'Вы не заполнили все поля.';
                                    }
                                } else {
                                    $_SESSION['errors'][] = 'Разблокировать себя запрещено.';
                                }
                            } else {
                                $_SESSION['errors'][] = 'Неизвестная операция.';
                            }
                        }
                    } else {
                        $_SESSION['errors'][] = 'Вы не заполнили все поля.';
                    }
                } else {
                    $_SESSION['errors'][] = 'Доступно только администрации.';
                }
            } else {
                $_SESSION['errors'][] = 'Необходима авторизация.';
            }
        } else {
            $_SESSION['errors'][] = 'Необходима авторизация.';
        }

        header("Location: /adminpanel");
        exit;
    }

    public function actionCreategroup()
    {
        if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $ut = new Utils();
            $authData = $ut->checkAuth();
            $this->view->params['data'] = $authData;

            if (!empty($authData['auth_data'])) {
                if ($authData['auth_data'] != null && $authData['auth_data']['role'] == 'admin') {
                    if (isset(Yii::$app->request->post()['nameGroup'])) {
                        if (!empty(Yii::$app->request->post()['nameGroup'])) {
                            $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/adminpanel/creategroup?ip="
                                . $_SERVER['REMOTE_ADDR']);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                'Cookie: email=' . $_COOKIE['email'] . '; password=' . $_COOKIE['password'] . ''
                            ));
                            curl_setopt($ch, CURLOPT_POSTFIELDS,
                                array(
                                    'name' => Yii::$app->request->post()['nameGroup'],
                                ));
                            $response = curl_exec($ch);
                            $dataResult = json_decode($response, true);
                            curl_close($ch);

                            if (isset($dataResult['errors'])) {
                                $_SESSION['errors'] = $dataResult['errors'];
                            }
                            if (isset($dataResult['status'])) {
                                if ($dataResult['status'] == "OK") {
                                    $_SESSION['about_action'][] = "Группа успешно создана.";
                                }
                            }
                        } else {
                            $_SESSION['errors'][] = 'Вы не заполнили все поля.';
                        }
                    } else {
                        $_SESSION['errors'][] = 'Вы не заполнили все поля.';
                    }
                } else {
                    $_SESSION['errors'][] = 'Доступно только администрации.';
                }
            } else {
                $_SESSION['errors'][] = 'Необходима авторизация.';
            }
        } else {
            $_SESSION['errors'][] = 'Необходима авторизация.';
        }

        header("Location: /adminpanel");
        exit;
    }

    public function actionRenamegroup()
    {
        if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $ut = new Utils();
            $authData = $ut->checkAuth();
            $this->view->params['data'] = $authData;

            if (!empty($authData['auth_data'])) {
                if ($authData['auth_data'] != null && $authData['auth_data']['role'] == 'admin') {
                    if (isset(Yii::$app->request->post()['group']) && isset(Yii::$app->request->post()['newNameGroup'])) {
                        if (!empty(Yii::$app->request->post()['group']) && !empty(Yii::$app->request->post()['newNameGroup'])) {
                            $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/adminpanel/renamegroup?ip="
                                . $_SERVER['REMOTE_ADDR']);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                'Cookie: email=' . $_COOKIE['email'] . '; password=' . $_COOKIE['password'] . ''
                            ));
                            curl_setopt($ch, CURLOPT_POSTFIELDS,
                                array(
                                    'oldName' => Yii::$app->request->post()['group'],
                                    'newName' => Yii::$app->request->post()['newNameGroup'],
                                ));
                            $response = curl_exec($ch);
                            $dataResult = json_decode($response, true);
                            curl_close($ch);

                            if (isset($dataResult['errors'])) {
                                $_SESSION['errors'] = $dataResult['errors'];
                            }
                            if (isset($dataResult['status'])) {
                                if ($dataResult['status'] == "OK") {
                                    $_SESSION['about_action'][] = "Группа успешно переименована.";
                                }
                            }
                        } else {
                            $_SESSION['errors'][] = 'Вы не заполнили все поля.';
                        }
                    } else {
                        $_SESSION['errors'][] = 'Вы не заполнили все поля.';
                    }
                } else {
                    $_SESSION['errors'][] = 'Доступно только администрации.';
                }
            } else {
                $_SESSION['errors'][] = 'Необходима авторизация.';
            }
        } else {
            $_SESSION['errors'][] = 'Необходима авторизация.';
        }

        header("Location: /adminpanel");
        exit;
    }

    public function actionMoveuser()
    {
        if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $ut = new Utils();
            $authData = $ut->checkAuth();
            $this->view->params['data'] = $authData;

            if (!empty($authData['auth_data'])) {
                if ($authData['auth_data'] != null && $authData['auth_data']['role'] == 'admin') {
                    if (isset(Yii::$app->request->post()['idAccount']) && isset(Yii::$app->request->post()['group'])) {
                        if (!empty(Yii::$app->request->post()['idAccount']) && !empty(Yii::$app->request->post()['group'])) {
                            $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/adminpanel/moveusertodiferentgroup?ip="
                                . $_SERVER['REMOTE_ADDR']);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                'Cookie: email=' . $_COOKIE['email'] . '; password=' . $_COOKIE['password'] . ''
                            ));
                            curl_setopt($ch, CURLOPT_POSTFIELDS,
                                array(
                                    'userId' => Yii::$app->request->post()['idAccount'],
                                    'nameGroup' => Yii::$app->request->post()['group'],
                                ));
                            $response = curl_exec($ch);
                            $dataResult = json_decode($response, true);
                            curl_close($ch);

                            if (isset($dataResult['errors'])) {
                                $_SESSION['errors'] = $dataResult['errors'];
                            }
                            if (isset($dataResult['status'])) {
                                if ($dataResult['status'] == "OK") {
                                    $_SESSION['about_action'][] = "Перемещение пользователя в группу <b>" .
                                        Yii::$app->request->post()['group'] . "</b> завершено.";
                                }
                            }
                        } else {
                            $_SESSION['errors'][] = 'Вы не заполнили все поля.';
                        }
                    } else {
                        $_SESSION['errors'][] = 'Вы не заполнили все поля.';
                    }
                } else {
                    $_SESSION['errors'][] = 'Доступно только администрации.';
                }
            } else {
                $_SESSION['errors'][] = 'Необходима авторизация.';
            }
        } else {
            $_SESSION['errors'][] = 'Необходима авторизация.';
        }

        header("Location: /adminpanel");
        exit;
    }
}
