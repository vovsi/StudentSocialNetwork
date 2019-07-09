<?php

namespace app\modules\v1\controllers;

use app\models\ControlsAPI;
use app\config\ConfigDataDB;
use app\models\DBHelper;
use Yii;
use yii\rest\ActiveController;

class AlbumController extends ActiveController
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

    // Получить все фото альбома пользователя
    public function actionGetalbum($id, $limit = 10, $offset = 0)
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();

        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($id)) {
                $dataOut['photos'] = $db->getPhotoOfUserLimitAndOffset($id, $limit, $offset);
            } else {
                $errors[] = 'Аккаунт заблокирован.';
            }
        } else {
            $errors[] = 'Необходима авторизация.';
        }

        $dataOut['errors'] = $errors;
        return $dataOut;
    }

    // Загрузить фото в свой альбом
    public function actionAdd()
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();

        $description = htmlspecialchars(Yii::$app->request->post('description'));
        $file = Yii::$app->request->post('file');

        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                if (iconv_strlen($description) <= ConfigDataDB::LIMIT_SYMBOLS_PHOTO_DESCRIPTION) {
                    if (!empty($file)) {
                        $type = explode(';', $file)[0];
                        $type = explode('/', $type)[1];
                        if ($db->checkFileImage($type)) {
                            if ($db->addPhotoToAlbum($data['auth_data']['id'], $file, $description)) {
                                // УСПЕХ
                                return ['status' => 'OK'];
                            } else {
                                $errors[] = 'Ошибка загрузки фото.';
                            }
                        } else {
                            $errors[] = 'Ошибка загрузки изображения. Убедитесь что файл является изображением. 
                                Допустимые форматы: ' . implode(", ", ConfigDataDB::ALLOWS_IMAGE_EXTENSION);
                        }
                    } else {
                        $errors[] = 'Файл не найден.';
                    }
                } else {
                    $errors[] = 'Кол-во символов в описании к фотографии должно быть не больше ' .
                        ConfigDataDB::LIMIT_SYMBOLS_PHOTO_DESCRIPTION;
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

    // Удаление фото из альбома
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
                    $photo = $db->getPhotoAsArray($id);
                    if ($photo['account_id'] == $data['auth_data']['id']) {
                        if ($db->removePhotoFromAlbum($id, $data['auth_data']['id'])) {
                            // Успешное удаление
                            return ['status' => 'OK'];
                        } else {
                            $errors[] = 'Ошибка удаления фотографии.';
                        }
                    } else {
                        $errors[] = 'Вы не можете удалить не своё фото.';
                    }
                } else {
                    $errors[] = 'id не найден.';
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