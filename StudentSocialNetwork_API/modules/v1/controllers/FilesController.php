<?php

namespace app\modules\v1\controllers;

use app\models\ControlsAPI;
use app\config\ConfigDataDB;
use app\models\DBHelper;
use Yii;
use yii\rest\ActiveController;

class FilesController extends ActiveController
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

    // Получить файлы пользователя
    public function actionGetfiles()
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();
        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                $dataOut['files'] = $db->getFilesOfUserAsArray($data['auth_data']['id']);
            } else {
                $errors[] = 'Аккаунт заблокирован.';
            }
        } else {
            $errors[] = 'Необходима авторизация.';
        }

        $dataOut['errors'] = $errors;
        return $dataOut;
    }

    // Загрузить файл
    public function actionLoad()
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();

        $file = Yii::$app->request->post('file');
        $fileName = Yii::$app->request->post('fileName');

        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                if (!empty($file) && !empty($fileName)) {
                    $type = explode(';', $file)[0];
                    $type = explode('/', $type)[1];
                    if ($db->checkFile($type)) {
                        if (!$db->checkFileExists($data['auth_data']['id'], $fileName)) {
                            if (count($db->getFilesOfUserAsArray($data['auth_data']['id'])) < ConfigDataDB::LIMIT_FILES) {
                                if ($db->loadFile($data['auth_data']['id'], $file, $fileName, $type)) {
                                    // УСПЕХ
                                    return ['status' => 'OK'];
                                } else {
                                    $errors[] = 'Ошибка загрузки файла.';
                                }
                            } else {
                                $errors[] = 'Вы превысили лимит файлов! Допустимо: ' . ConfigDataDB::LIMIT_FILES . ' файла(ов).';
                            }
                        } else {
                            $errors[] = 'Файл с таким именем уже существует! Пожалуйста переименуйте его.';
                        }
                    } else {
                        $errors[] = 'Ошибка загрузки файла. Допустимые форматы: ' . implode(", ",
                                ConfigDataDB::ALLOWS_FILE_EXTENSION);
                    }
                } else {
                    $errors[] = 'Данные файла пусты. Проверьте передаваемые параметры.';
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

    // Удалить файл
    public function actionRemove()
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();

        $fileName = Yii::$app->request->post('fileName');

        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                if (!empty($fileName)) {
                    if ($db->checkFileExists($data['auth_data']['id'], $fileName)) {
                        if ($db->removeFile($data['auth_data']['id'], $fileName)) {
                            // УСПЕХ
                            return ['status' => 'OK'];
                        } else {
                            $errors[] = 'Ошибка удаления файла.';
                        }
                    } else {
                        $errors[] = 'Файла с таким именем не существует!';
                    }
                } else {
                    $errors[] = 'Данные файла пусты. Проверьте передаваемые параметры.';
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