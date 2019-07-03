<?php

namespace app\modules\v1\controllers;

use app\config\ConfigDataDB;
use app\models\ControlsAPI;
use app\models\DBHelper;
use Yii;
use yii\rest\ActiveController;

class SettingsController extends ActiveController
{
    public $modelClass = 'app\models\ControlsAPI';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Request-Method' => ['GET', 'POST'],
            ],

        ];
        $behaviors['contentNegotiator'] = [
            'class' => \yii\filters\ContentNegotiator::className(),
            'formats' => [
                'application/json' => \yii\web\Response::FORMAT_JSON,
            ],
        ];
        return $behaviors;
    }

    // Получить данные настроек приватности
    public function actionGetdataprivacy()
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();
        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                $dataOut['privacy'] = $db->getPrivacyOfUser($data['auth_data']['id']);
            } else {
                $errors[] = 'Аккаунт заблокирован.';
            }
        } else {
            $errors[] = 'Необходима авторизация.';
        }
        $dataOut['errors'] = $errors;
        return $dataOut;
    }

    // Получить список чёрного списка пользователя
    public function actionGetdatablacklist($limit = 10, $offset = 0)
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();
        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                return $db->getBlackListOfUserLimitAndOffset($data['auth_data']['id'], $limit,
                    $offset);
            } else {
                $errors[] = 'Аккаунт заблокирован.';
            }
        } else {
            $errors[] = 'Необходима авторизация.';
        }
        $dataOut['errors'] = $errors;
        return $dataOut;
    }

    // Получить profile data
    public function actionGetdataprofile()
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();
        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                $dataOut['profile'] = $db->getProfileById($data['auth_data']['id']);
            } else {
                $errors[] = 'Аккаунт заблокирован.';
            }
        } else {
            $errors[] = 'Необходима авторизация.';
        }
        $dataOut['errors'] = $errors;
        return $dataOut;
    }

    // Сохранить profile data
    public function actionSaveprofile()
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();
        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                $firstName = htmlspecialchars(Yii::$app->request->post('first_name'));
                $lastName = htmlspecialchars(Yii::$app->request->post('last_name'));
                $patronymic = htmlspecialchars(Yii::$app->request->post('patronymic'));
                $email = htmlspecialchars(Yii::$app->request->post('email'));
                $gender = htmlspecialchars(Yii::$app->request->post('gender'));
                $phoneNumber = htmlspecialchars(Yii::$app->request->post('phone_number'));
                $activities = htmlspecialchars(Yii::$app->request->post('activities'));
                $interests = htmlspecialchars(Yii::$app->request->post('interests'));
                $aboutMe = htmlspecialchars(Yii::$app->request->post('about_me'));
                $dateBirthday = htmlspecialchars(Yii::$app->request->post('date_birthday'));

                if (!empty($firstName) && !empty($lastName) &&
                    !empty($patronymic) && !empty($email) &&
                    !empty($gender)) {
                    if (1 == preg_match('/^((([0-9A-Za-z]{1}[-0-9A-z\.]{1,}[0-9A-Za-z]{1})|([0-9А-Яа-я]{1}[-0-9А-я\.]{1,}[0-9А-Яа-я]{1}))@([-0-9A-Za-z]{1,}\.){1,2}[-A-Za-z]{2,})$/u',
                            $email)) {
                        if (iconv_strlen($firstName) <= ConfigDataDB::LIMIT_SYMBOLS_PROFILE_FIRST_NAME &&
                            iconv_strlen($lastName) <= ConfigDataDB::LIMIT_SYMBOLS_PROFILE_LAST_NAME &&
                            iconv_strlen($patronymic) <= ConfigDataDB::LIMIT_SYMBOLS_PROFILE_PATRONYMIC &&
                            iconv_strlen($email) <= ConfigDataDB::LIMIT_SYMBOLS_PROFILE_EMAIL &&
                            iconv_strlen($gender) <= ConfigDataDB::LIMIT_SYMBOLS_PROFILE_GENDER &&
                            iconv_strlen($phoneNumber) <= ConfigDataDB::LIMIT_SYMBOLS_PROFILE_PHONE_NUMBER &&
                            iconv_strlen($activities) <= ConfigDataDB::LIMIT_SYMBOLS_PROFILE_ACTIVITIES &&
                            iconv_strlen($interests) <= ConfigDataDB::LIMIT_SYMBOLS_PROFILE_INTERESTS &&
                            iconv_strlen($aboutMe) <= ConfigDataDB::LIMIT_SYMBOLS_PROFILE_ABOUT_ME &&
                            iconv_strlen($dateBirthday) <= ConfigDataDB::LIMIT_SYMBOLS_RAW_DATE) {
                            if ($db->genderExists($gender)) {
                                $foundAcc = $db->getAccountAsArrayByEmail($email);
                                // Проверка что такой email не указан у другого пользователя
                                if (!empty($foundAcc)) {
                                    if ($foundAcc['id'] != $data['auth_data']['id']) {
                                        $errors[] = 'Указанный email уже используется другим пользователем';
                                        $dataOut['errors'] = $errors;
                                        return $dataOut;
                                    }
                                }

                                if ($db->changeProfile($data['auth_data']['id'],
                                    $firstName,
                                    $lastName,
                                    $patronymic,
                                    $email,
                                    $gender,
                                    $phoneNumber,
                                    $activities,
                                    $interests,
                                    $aboutMe,
                                    $dateBirthday)) {
                                    return ['status' => 'OK'];
                                } else {
                                    $errors[] = 'Ошибка сохранения данных.';
                                }
                            } else {
                                $errors[] = 'Указан несуществующий пол. Допустим: Мужской, Женский.';
                            }
                        } else {
                            $errors[] = 'Максимальные кол-ва символов в полях: Имя('.ConfigDataDB::LIMIT_SYMBOLS_PROFILE_FIRST_NAME.')'.
                            ' Фамилия('.ConfigDataDB::LIMIT_SYMBOLS_PROFILE_LAST_NAME.')'.
                            ' Отчество('.ConfigDataDB::LIMIT_SYMBOLS_PROFILE_PATRONYMIC.')'.
                            ' Email('.ConfigDataDB::LIMIT_SYMBOLS_PROFILE_EMAIL.')'.
                            ' Пол('.ConfigDataDB::LIMIT_SYMBOLS_PROFILE_GENDER.')'.
                            ' Номер телефона('.ConfigDataDB::LIMIT_SYMBOLS_PROFILE_PHONE_NUMBER.')'.
                            ' Деятельность('.ConfigDataDB::LIMIT_SYMBOLS_PROFILE_ACTIVITIES.')'.
                            ' Интересы('.ConfigDataDB::LIMIT_SYMBOLS_PROFILE_INTERESTS.')'.
                            ' О мне('.ConfigDataDB::LIMIT_SYMBOLS_PROFILE_ABOUT_ME.')'.
                            ' День рождение('.ConfigDataDB::LIMIT_SYMBOLS_RAW_DATE.')';
                        }
                    } else {
                        $errors[] = 'Email имеет неверный формат. Пример: email@mail.ru.';
                    }
                } else {
                    $errors[] = "Вы не заполнили обязательные поля (с пометкой <p style=\"color: red;display: inline-block\"> * </p>).";
                }
            } else {
                $errors[] = 'Аккаунт заблокирован.';
            }
        } else {
            $errors[] = 'Необходима авторизация.';
        }
        $dataOut['errors'] = $errors;
        return $dataOut;
    }

    // Сохранить данные приватности
    public function actionSaveprivacy()
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();
        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                $writePost = Yii::$app->request->post('write_post');

                if ($writePost == 'all' || $writePost == 'nobody') {
                    if ($db->savePrivacyOfUser($data['auth_data']['id'], $writePost)) {
                        // Успешное сохранение данных
                        return ['status' => 'OK'];
                    } else {
                        $errors[] = 'Ошибка сохранения данных приватности.';
                    }
                } else {
                    $errors[] = 'Неверно указано значение для поля "write_post" Доступно: all, nobody.';
                }
            } else {
                $errors[] = 'Аккаунт заблокирован.';
            }
        } else {
            $errors[] = 'Необходима авторизация.';
        }
        $dataOut['errors'] = $errors;
        return $dataOut;
    }

    // Изменить пароль
    public function actionChangepassword()
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();
        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                $oldPasswordHash = Yii::$app->request->post()['old_password_hash'];
                $newPassword = Yii::$app->request->post()['new_password'];

                if (password_verify($oldPasswordHash, $data['auth_data']['password'])) {
                    if (iconv_strlen($newPassword) < 100) {
                        if ($db->setNewPasswordToAccount($data['auth_data']['id'], $newPassword)) {
                            return ['status' => 'OK'];
                        } else {
                            $errors[] = 'Ошибка смены пароля.';
                        }
                    } else {
                        $errors[] = 'Пароль не должне быть длинее 100 символов.';
                    }
                } else {
                    $errors[] = 'Старый пароль неверный.';
                }
            } else {
                $errors[] = 'Аккаунт заблокирован.';
            }
        } else {
            $errors[] = 'Необходима авторизация.';
        }
        $dataOut['errors'] = $errors;
        return $dataOut;
    }
}