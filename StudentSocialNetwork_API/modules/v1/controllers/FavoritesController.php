<?php

namespace app\modules\v1\controllers;

use app\models\ControlsAPI;
use app\models\DBHelper;
use Yii;
use yii\rest\ActiveController;

class FavoritesController extends ActiveController
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

    public function actions()
    {
        return [
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
        ];
    }

    // Получить список избранных пользователей
    public function actionGetmyfavorites($limit = 10, $offset = 0)
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();
        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
               return $db->getFavoritesForHtml($data['auth_data']['id'], $limit, $offset);
            } else {
                $errors[] = 'Аккаунт заблокирован.';
            }
        } else {
            $errors[] = 'Необходима авторизация.';
        }

        $dataOut['errors'] = $errors;
        return $dataOut;
    }

    // Удалить из избранных
    public function actionRemove()
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();
        $id = Yii::$app->request->post('id');
        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                if (!empty($id)) {
                    // Проверить что пользователь есть в списке друзей
                    if ($db->checkExistsFavorite($data['auth_data']['id'], $id)) {
                        if ($db->removeUserFromFavorites($data['auth_data']['id'], $id)) {
                            // Пользователь успешно удален из списка избранных
                            return ['status' => 'OK'];
                        } else {
                            $errors[] = 'Ошибка удаления пользователя из списка избранных.';
                        }
                    } else {
                        $errors[] = 'Пользователя нет в списке избранных.';
                    }
                } else {
                    $errors[] = 'id пользователя не найден.';
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

    // Добавить в избранные
    public function actionAdd()
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();
        $id = Yii::$app->request->post('id');
        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                if (!empty($id)) {
                    // Если пользователь не добавляет сам себя в друзья
                    if ($id != $data['auth_data']['id']) {
                        // Проверить что пользователя нет в друзьях
                        if (!$db->checkExistsFavorite($data['auth_data']['id'], $id)) {
                            if ($db->addUserToFavorite($data['auth_data']['id'], $id)) {
                                // Пользователь успешно добавлен в список избранных
                                return ['status' => 'OK'];
                            } else {
                                $errors[] = 'Ошибка добавления пользователя в список избранных.';
                            }
                        } else {
                            $errors[] = 'Пользователь уже содержится в вашем списке избранных.';
                        }
                    } else {
                        $errors[] = 'Вы не можете добавить себя в список избранных.';
                    }
                } else {
                    $errors[] = 'id пользователя не найден.';
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