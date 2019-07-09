<?php

namespace app\modules\v1\controllers;

use app\config\ConfigDataDB;
use app\models\ControlsAPI;
use app\models\DBHelper;
use Yii;
use yii\rest\ActiveController;

class AdminpanelController extends ActiveController
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

    // Get groups
    public function actionGetgroups()
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();
        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                // Если польхователь авторизирован и является с ролью admin
                if ($data['auth_data'] != null && $data['auth_data']['role'] == 'admin') {
                    $dataOut['groups'] = $db->getGroupsAsArray();
                } else {
                    $errors[] = 'Доступно только администрации.';
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

    // Get admins profiles
    public function actionGetadmins()
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();
        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                // Если польхователь авторизирован и является с ролью admin
                if ($data['auth_data'] != null && $data['auth_data']['role'] == 'admin') {
                    $dataOut['admins'] = $db->getAdminsAsArray();
                } else {
                    $errors[] = 'Доступно только администрации.';
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

    // Registration account
    public function actionRegistrationaccount()
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();
        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                // Если польхователь авторизирован и является с ролью admin
                if ($data['auth_data'] != null && $data['auth_data']['role'] == 'admin') {
                    if (isset(Yii::$app->request->post()['first_name']) && isset(Yii::$app->request->post()['last_name']) &&
                        isset(Yii::$app->request->post()['patronymic']) && isset(Yii::$app->request->post()['group']) &&
                        isset(Yii::$app->request->post()['role']) && isset(Yii::$app->request->post()['gender']) &&
                        isset(Yii::$app->request->post()['email'])) {
                        if (1 == preg_match(ConfigDataDB::REGEX_VALID_EMAIL,
                                Yii::$app->request->post()['email'])) {
                            if (iconv_strlen(Yii::$app->request->post()['first_name']) < 100 &&
                                iconv_strlen(Yii::$app->request->post()['last_name']) < 100 &&
                                iconv_strlen(Yii::$app->request->post()['patronymic']) < 100) {
                                if (!$db->emailExists(Yii::$app->request->post()['email'])) {
                                    if ($db->groupExists(Yii::$app->request->post()['group']) &&
                                        $db->roleExists(Yii::$app->request->post()['role'])) {
                                        if ($db->genderExists(Yii::$app->request->post()['gender'])) {
                                            $firstName = htmlspecialchars(Yii::$app->request->post()['first_name']);
                                            $lastName = htmlspecialchars(Yii::$app->request->post()['last_name']);
                                            $patronymic = htmlspecialchars(Yii::$app->request->post()['patronymic']);
                                            $email = htmlspecialchars(Yii::$app->request->post()['email']);
                                            $group = htmlspecialchars(Yii::$app->request->post()['group']);
                                            $role = htmlspecialchars(Yii::$app->request->post()['role']);
                                            $gender = htmlspecialchars(Yii::$app->request->post()['gender']);
                                            $password = $db->registrationAccount($firstName,
                                                $lastName,
                                                $patronymic,
                                                $email,
                                                $group,
                                                $role,
                                                $gender);
                                            if (!empty($password)) {
                                                // Всё хорошо
                                                return [
                                                    'status' => 'OK',
                                                    'password' => $password
                                                ];
                                            } else {
                                                $errors[] = 'Ошибка регистрации пользователя.';
                                            }
                                        } else {
                                            $errors[] = 'Неверный формат ввода пола. Допустимо: Мужской или Женский.';
                                        }
                                    } else {
                                        $errors[] = 'Заданная группа или роль несуществует.';
                                    }
                                } else {
                                    $errors[] = 'Пользователь с таким email уже существует.';
                                }
                            } else {
                                $errors[] = 'Имя, фамилия и отчество не должно содержать более 100 символов.';
                            }
                        } else {
                            $errors[] = 'Неверный формат email.';
                        }
                    } else {
                        $errors[] = 'Вы не заполнили все поля.';
                    }
                } else {
                    $errors[] = 'Доступно только администрации.';
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

    // To block account
    public function actionBlockaccount()
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();
        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                // Если польхователь авторизирован и является с ролью admin
                if ($data['auth_data'] != null && $data['auth_data']['role'] == 'admin') {
                    if (isset(Yii::$app->request->post()['email'])) {
                        if (Yii::$app->request->post()['email'] != $data['auth_data']['email']) {
                            $acc = $db->getAccountAsArrayByEmail(Yii::$app->request->post()['email']);
                            if (!empty($acc)) {
                                if (!$acc['blocked']) {
                                    if ($db->blockAccount($acc['id'])) {
                                        // Успешная блокировка
                                        return ['status' => 'OK'];
                                    } else {
                                        $errors[] = 'Ошибка блокировки аккаунта.';
                                    }
                                } else {
                                    $errors[] = 'Пользователь уже заблокированный.';
                                }
                            } else {
                                $errors[] = 'Пользователя с таким email несуществует.';
                            }
                        } else {
                            $errors[] = 'Блокировать себя запрещено.';
                        }
                    } else {
                        $errors[] = 'Вы не заполнили все поля.';
                    }
                } else {
                    $errors[] = 'Доступно только администрации.';
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

    // To unblock account
    public function actionUnblockaccount()
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();
        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                // Если польхователь авторизирован и является с ролью admin
                if ($data['auth_data'] != null && $data['auth_data']['role'] == 'admin') {
                    if (isset(Yii::$app->request->post()['email'])) {
                        if (Yii::$app->request->post()['email'] != $data['auth_data']['email']) {
                            $acc = $db->getAccountAsArrayByEmail(Yii::$app->request->post()['email']);
                            if (!empty($acc)) {
                                if ($acc['blocked']) {
                                    if ($db->unblockAccount($acc['id'])) {
                                        // Успешная разблокировка
                                        return ['status' => 'OK'];
                                    } else {
                                        $errors[] = 'Ошибка разблокировки аккаунта.';
                                    }
                                } else {
                                    $errors[] = 'Пользователь уже разблокирован.';
                                }
                            } else {
                                $errors[] = 'Пользователя с таким email несуществует.';
                            }
                        } else {
                            $errors[] = 'Разблокировать себя запрещено.';
                        }
                    } else {
                        $errors[] = 'Вы не заполнили все поля.';
                    }
                } else {
                    $errors[] = 'Доступно только администрации.';
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

    // Создать группу
    public function actionCreategroup()
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();
        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                // Если пользователь авторизирован и является с ролью admin
                if ($data['auth_data'] != null && $data['auth_data']['role'] == 'admin') {
                    if (isset(Yii::$app->request->post()['name'])) {
                        $groupName = htmlspecialchars(Yii::$app->request->post()['name']);
                        if (!empty($groupName)) {
                            if (!$db->groupExists($groupName)) {
                                if ($db->createGroup($groupName)) {
                                    return ['status' => 'OK'];
                                } else {
                                    $errors[] = 'Ошибка создания группы.';
                                }
                            } else {
                                $errors[] = 'Группа с таким именем уже существует.';
                            }
                        } else {
                            $errors[] = 'Пустое имя.';
                        }
                    } else {
                        $errors[] = 'Вы не заполнили все поля.';
                    }
                } else {
                    $errors[] = 'Доступно только администрации.';
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

    // Переименовать группу
    public function actionRenamegroup()
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();
        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                // Если пользователь авторизирован и является с ролью admin
                if ($data['auth_data'] != null && $data['auth_data']['role'] == 'admin') {
                    if (isset(Yii::$app->request->post()['oldName']) && isset(Yii::$app->request->post()['newName'])) {
                        $oldName = htmlspecialchars(Yii::$app->request->post()['oldName']);
                        $newName = htmlspecialchars(Yii::$app->request->post()['newName']);
                        if (!empty($oldName) && !empty($newName)) {
                            if ($db->groupExists($oldName)) {
                                if ($db->renameGroup($oldName, $newName)) {
                                    return ['status' => 'OK'];
                                } else {
                                    $errors[] = 'Ошибка переименования группы.';
                                }
                            } else {
                                $errors[] = 'Группа не найдена.';
                            }
                        } else {
                            $errors[] = 'Пустое имя группы.';
                        }
                    } else {
                        $errors[] = 'Вы не заполнили все поля.';
                    }
                } else {
                    $errors[] = 'Доступно только администрации.';
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

    // Переместить пользователя в другую группу
    public function actionMoveusertodiferentgroup()
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();
        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                // Если пользователь авторизирован и является с ролью admin
                if ($data['auth_data'] != null && $data['auth_data']['role'] == 'admin') {
                    if (isset(Yii::$app->request->post()['userId']) && isset(Yii::$app->request->post()['nameGroup'])) {
                        $userId = htmlspecialchars(Yii::$app->request->post()['userId']);
                        $nameGroup = htmlspecialchars(Yii::$app->request->post()['nameGroup']);
                        if (!empty($userId) && !empty($nameGroup)) {
                            if ($db->accountIdExists($userId) && $db->groupExists($nameGroup)) {
                                if ($db->moveUserToDiferentGroup($userId, $nameGroup)) {
                                    return ['status' => 'OK'];
                                } else {
                                    $errors[] = 'Ошибка переименования группы.';
                                }
                            } else {
                                $errors[] = 'Группа или пользователь не найдены.';
                            }
                        } else {
                            $errors[] = 'Пустое имя группы или id пользователя.';
                        }
                    } else {
                        $errors[] = 'Вы не заполнили все поля.';
                    }
                } else {
                    $errors[] = 'Доступно только администрации.';
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