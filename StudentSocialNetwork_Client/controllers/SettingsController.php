<?php

namespace app\controllers;

use app\config\ConfigAPI;
use app\services\Utils;
use Yii;

class SettingsController extends \yii\web\Controller
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
                // Получение данных приватности
                $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/settings/getdataprivacy?ip=" . $_SERVER['REMOTE_ADDR']);
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

                if (isset($dataResult['privacy'])) {
                    $data['privacy'] = $dataResult['privacy'];
                }


                // Получение чёрного списка
                $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/settings/getdatablacklist?limit=10&offset=0&ip="
                    . $_SERVER['REMOTE_ADDR']);
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

                if (isset($dataResult['black_list'])) {
                    for ($i = 0; $i < count($dataResult['black_list']); $i++) {
                        $dataResult['black_list'][$i]['photo_path'] = 'data:image/jpeg;base64,' .
                            base64_encode(file_get_contents($dataResult['black_list'][$i]['photo_path']));
                    }
                    $data['black_list'] = $dataResult['black_list'];
                    $data['is_there_more_black_list'] = $dataResult['is_there_more_black_list'];
                }

                // Получение profile data
                $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/settings/getdataprofile?ip=" . $_SERVER['REMOTE_ADDR']);
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

                if (isset($dataResult['profile'])) {
                    $data['profile'] = $dataResult['profile'];
                }
            } else {
                $_SESSION['errors'] = ['' => 'Необходима авторизация.'];
            }
        } else {
            $_SESSION['errors'] = ['' => 'Необходима авторизация.'];
        }

        return $this->render('index', $data);
    }

    public function actionMydata()
    {
        if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $ut = new Utils();
            $authData = $ut->checkAuth();
            $this->view->params['data'] = $authData;

            if (!empty($authData['auth_data'])) {
                if (isset(Yii::$app->request->post()['first_name']) && isset(Yii::$app->request->post()['last_name']) &&
                    isset(Yii::$app->request->post()['patronymic']) && isset(Yii::$app->request->post()['email']) &&
                    isset(Yii::$app->request->post()['gender']) && isset(Yii::$app->request->post()['phone_number']) &&
                    isset(Yii::$app->request->post()['activities']) && isset(Yii::$app->request->post()['interests']) &&
                    isset(Yii::$app->request->post()['about_me']) && isset(Yii::$app->request->post()['date_birthday'])) {
                    if (!empty(Yii::$app->request->post()['first_name']) && !empty(Yii::$app->request->post()['last_name']) &&
                        !empty(Yii::$app->request->post()['patronymic']) && !empty(Yii::$app->request->post()['email']) &&
                        !empty(Yii::$app->request->post()['gender'])) {
                        if (1 == preg_match(Utils::REGEX_VALID_EMAIL, Yii::$app->request->post()['email'])) {
                            if (iconv_strlen(Yii::$app->request->post()['first_name']) < 100 &&
                                iconv_strlen(Yii::$app->request->post()['last_name']) < 100 &&
                                iconv_strlen(Yii::$app->request->post()['patronymic']) < 100) {
                                if (Utils::genderExists($_POST['gender'])) {
                                    $firstName = htmlspecialchars(Yii::$app->request->post()['first_name']);
                                    $lastName = htmlspecialchars(Yii::$app->request->post()['last_name']);
                                    $patronymic = htmlspecialchars(Yii::$app->request->post()['patronymic']);
                                    $email = htmlspecialchars(Yii::$app->request->post()['email']);
                                    $gender = htmlspecialchars(Yii::$app->request->post()['gender']);
                                    $phoneNumber = htmlspecialchars(Yii::$app->request->post()['phone_number']);
                                    $activities = htmlspecialchars(Yii::$app->request->post()['activities']);
                                    $interests = htmlspecialchars(Yii::$app->request->post()['interests']);
                                    $aboutMe = htmlspecialchars(Yii::$app->request->post()['about_me']);
                                    $dateBirthday = htmlspecialchars(Yii::$app->request->post()['date_birthday']);

                                    $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/settings/saveprofile?ip="
                                        . $_SERVER['REMOTE_ADDR']);
                                    curl_setopt($ch, CURLOPT_POST, 1);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                        'Cookie: email=' . $_COOKIE['email'] . '; password=' . $_COOKIE['password'] . ''
                                    ));
                                    curl_setopt($ch, CURLOPT_POSTFIELDS,
                                        array(
                                            'first_name' => $firstName,
                                            'last_name' => $lastName,
                                            'patronymic' => $patronymic,
                                            'email' => $email,
                                            'gender' => $gender,
                                            'phone_number' => $phoneNumber,
                                            'activities' => $activities,
                                            'interests' => $interests,
                                            'about_me' => $aboutMe,
                                            'date_birthday' => $dateBirthday,
                                        ));
                                    $response = curl_exec($ch);
                                    $dataResult = json_decode($response, true);
                                    curl_close($ch);

                                    if (isset($dataResult['errors'])) {
                                        $_SESSION['about_action'] = $dataResult['errors'];
                                    }
                                    if (isset($dataResult['status'])) {
                                        if ($dataResult['status'] == "OK") {
                                            $_SESSION['about_action'][] = "Данные успешно сохранены.";
                                        }
                                    }
                                    setcookie('email', $email, 0, '/');
                                } else {
                                    $_SESSION['errors'][] = 'Указан несуществующий пол. Допустим: Мужской, Женский.';
                                }
                            } else {
                                $_SESSION['errors'][] = 'Длина поля превышает допустимую длину (100 символов).';
                            }
                        } else {
                            $_SESSION['errors'][] = 'Email имеет неверный формат. Пример: email@mail.ru.';
                        }
                    } else {
                        $_SESSION['errors'][] = "Вы не заполнили обязательные поля (с пометкой <p style=\"color: red;display: inline-block\"> * </p>).";
                    }
                } else {
                    $_SESSION['errors'][] = 'Нехаполненные поля.';
                }
            } else {
                $_SESSION['errors'][] = 'Необходима авторизация.';
            }
        } else {
            $_SESSION['errors'][] = 'Необходима авторизация.';
        }
        header("Location: /settings");
        exit;
    }

    public function actionPrivacy()
    {
        if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $ut = new Utils();
            $authData = $ut->checkAuth();
            $this->view->params['data'] = $authData;

            if (!empty($authData['auth_data'])) {
                if (isset(Yii::$app->request->post()['write_post'])) {
                    if (Yii::$app->request->post()['write_post'] == 'Все' || Yii::$app->request->post()['write_post'] == 'Никто') {
                        if ($_POST['write_post'] == 'Все') {
                            $writePost = 'all';
                        } else {
                            $writePost = 'nobody';
                        }

                        $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/settings/saveprivacy?ip=" . $_SERVER['REMOTE_ADDR']);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            'Cookie: email=' . $_COOKIE['email'] . '; password=' . $_COOKIE['password'] . ''
                        ));
                        curl_setopt($ch, CURLOPT_POSTFIELDS,
                            array(
                                'write_post' => $writePost
                            ));
                        $response = curl_exec($ch);
                        $dataResult = json_decode($response, true);
                        curl_close($ch);

                        if (isset($dataResult['errors'])) {
                            $errors[] = $dataResult['errors'];
                        }
                        if (isset($dataResult['status'])) {
                            if ($dataResult['status'] == "OK") {
                                $_SESSION['about_action'][] = "Данные успешно сохранены.";
                            }
                        }
                    } else {
                        $_SESSION['errors'][] = 'Неверно указано значение для поля "write_post" Доступно: Все, Никто.';
                    }
                } else {
                    $_SESSION['errors'][] = 'Вы не заполнили все поля.';
                }
            } else {
                $_SESSION['errors'][] = 'Необходима авторизация.';
            }
        } else {
            $_SESSION['errors'][] = 'Необходима авторизация.';
        }

        header("Location: /settings");
        exit;
    }

    public function actionSecurity()
    {
        if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $ut = new Utils();
            $authData = $ut->checkAuth();
            $this->view->params['data'] = $authData;

            if (!empty($authData['auth_data'])) {
                if (isset(Yii::$app->request->post()['old_password']) && isset(Yii::$app->request->post()['new_password'])) {
                    if (!empty(Yii::$app->request->post()['old_password']) && !empty(Yii::$app->request->post()['new_password'])) {
                        if (md5(md5(Yii::$app->request->post()['old_password'])) == $authData['auth_data']['password_hash']) {
                            if (iconv_strlen(Yii::$app->request->post()['new_password']) < 100) {
                                $oldPasswordHash = $_COOKIE['password'];
                                $newPassword = md5(md5(Yii::$app->request->post()['new_password']));

                                $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/settings/changepassword?ip="
                                    . $_SERVER['REMOTE_ADDR']);
                                curl_setopt($ch, CURLOPT_POST, 1);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                    'Cookie: email=' . $_COOKIE['email'] . '; password=' . $_COOKIE['password'] . ''
                                ));
                                curl_setopt($ch, CURLOPT_POSTFIELDS,
                                    array(
                                        'old_password_hash' => $oldPasswordHash,
                                        'new_password' => $newPassword
                                    ));
                                $response = curl_exec($ch);
                                $dataResult = json_decode($response, true);
                                curl_close($ch);

                                if (isset($dataResult['errors'])) {
                                    $errors[] = $dataResult['errors'];
                                }
                                if (isset($dataResult['status'])) {
                                    if ($dataResult['status'] == "OK") {
                                        // Установить новые данные авторизации
                                        setcookie('email', $authData['auth_data']['email'], 0, '/');
                                        setcookie('password', $newPassword, 0, '/');
                                        $_SESSION['about_action'][] = "Пароль успешно изменен.";
                                    }
                                }
                            } else {
                                $_SESSION['errors'][] = 'Пароль не должне быть длинее 100 символов.';
                            }
                        } else {
                            $_SESSION['errors'][] = 'Старый пароль неверный.';
                        }
                    } else {
                        $_SESSION['errors'][] = 'Поля должны быть заполнены.';
                    }
                } else {
                    $_SESSION['errors'][] = 'Поля должны быть заполнены.';
                }
            } else {
                $_SESSION['errors'][] = 'Необходима авторизация.';
            }
        } else {
            $_SESSION['errors'][] = 'Необходима авторизация.';
        }

        header("Location: /");
        exit;
    }
}
