<?php

namespace app\modules\v1\controllers;

use app\models\ControlsAPI;
use app\models\DBHelper;
use Yii;
use yii\rest\ActiveController;

class SearchController extends ActiveController
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

    // Поиск по пользователям (имя/фамилия/отчество)
    public function actionUsers()
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();
        $query = Yii::$app->request->post('query');
        $limit = (Yii::$app->request->post('limit')) ? Yii::$app->request->post('limit') : 10;
        $offset = (Yii::$app->request->post('offset')) ? Yii::$app->request->post('offset') : 0;
        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                $res = $db->getResultSearchByUsers($query);
                $dataOut['search_text'] = $query;
                if (count($res) == 0 && empty($query)) {
                    $res = $db->getAccounts();
                }
                $res = array_slice($res, $offset, $limit);
                foreach ($res as $key => $value) {
                    $group = $db->getGroupById($value->group_id);
                    $perInfo = $db->getPersonalInfoById($value->personal_info_id);
                    $dataOut['result_search'][] = [
                        'id' => $value->id,
                        'first_name' => $value->first_name,
                        'last_name' => $value->last_name,
                        'patronymic' => $value->patronymic,
                        'group' => $group->name,
                        'photo_path' => ControlsAPI::HOST . $perInfo->photo_path,
                        'status_visit' => $db->getStatusVisit($value->id)
                    ];
                }
                return $dataOut;
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