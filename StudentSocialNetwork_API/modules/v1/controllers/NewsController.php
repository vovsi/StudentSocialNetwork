<?php

namespace app\modules\v1\controllers;

use app\models\ControlsAPI;
use app\config\ConfigDataDB;
use app\models\DBHelper;
use Yii;
use yii\rest\ActiveController;

class NewsController extends ActiveController
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

    // Получить список новостей
    public function actionGetnews($limit = 10, $offset = 0)
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();
        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                $dataOut = $db->getAllNews($limit, $offset);
            } else {
                $errors[] = 'Аккаунт заблокирован.';
            }
        } else {
            $errors[] = 'Необходима авторизация.';
        }
        $dataOut['errors'] = $errors;
        return $dataOut;
    }

    // Получить одну новость (по ид)
    public function actionGetonenews($id)
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();
        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                $news = $db->getOneNews($id);
                if ($news != null) {
                    $dataOut['news'] = $news;
                } else {
                    $errors[] = 'Ошибка получения новости.';
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

    // Получить список новостей-событий (id и event_date)
    public function actionGetevents()
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();
        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                $events = $db->getAllEvents();
                $dataOut['events'] = $events;
            } else {
                $errors[] = 'Аккаунт заблокирован.';
            }
        } else {
            $errors[] = 'Необходима авторизация.';
        }
        $dataOut['errors'] = $errors;
        return $dataOut;
    }

    // Добавить новость
    public function actionAdd()
    {
        $theme = Yii::$app->request->post('theme');
        $description = Yii::$app->request->post('description');
        $image = Yii::$app->request->post('image');
        $videoLink = Yii::$app->request->post('video_link');
        $pollTheme = Yii::$app->request->post('pollTheme');
        $pollAnswers = explode("~@g", Yii::$app->request->post('pollAnswers'));
        $pollAnon = Yii::$app->request->post('pollAnon');

        $db = new DBHelper();
        $resultArray = ControlsAPI::checkAuth();
        $errors = array();

        if (!empty($resultArray['auth_data'])) {
            if (!$db->checkBlockAccount($resultArray['auth_data']['id'])) {
                if (isset($theme) && isset($description)) {
                    if (!empty($theme) && !empty($description)) {
                        if (iconv_strlen($theme) < ConfigDataDB::LIMIT_SYMBOLS_NEWS_THEME &&
                            iconv_strlen($description) < ConfigDataDB::LIMIT_SYMBOLS_NEWS_DESCRIPTION &&
                            iconv_strlen($videoLink) < ConfigDataDB::LIMIT_SYMBOLS_VIDEO_YOUTUBE) {
                            $theme = htmlspecialchars($theme);
                            $description = htmlspecialchars($description);
                            $videoLink = htmlspecialchars($videoLink);

                            // Если видео нет, то null
                            if (empty($videoLink)) {
                                $videoLink = null;
                            }

                            // Проверяем дату события и место события (если есть)
                            $eventDate = null;
                            $eventDescription = null;
                            if (isset(Yii::$app->request->post()['event_date']) &&
                                isset(Yii::$app->request->post()['event_description'])) {
                                if (!empty(Yii::$app->request->post()['event_date'])) {
                                    if (!empty(Yii::$app->request->post()['event_description'])) {
                                        if (iconv_strlen(Yii::$app->request->post()['event_description']) <
                                            ConfigDataDB::LIMIT_SYMBOLS_NEWS_EVENT_DESCRIPTION) {
                                            $eventDescription = htmlspecialchars(Yii::$app->request->post()['event_description']);
                                        } else {
                                            $errors[] = 'Место события не должно превышать ' .
                                                ConfigDataDB::LIMIT_SYMBOLS_NEWS_EVENT_DESCRIPTION . ' символов.';
                                            return ['errors' => $errors];
                                        }
                                    }
                                    $eventDate = htmlspecialchars(Yii::$app->request->post()['event_date']);
                                }
                                if (empty(Yii::$app->request->post()['event_date']) &&
                                    !empty(Yii::$app->request->post()['event_description'])) {
                                    $errors[] = 'Укажите дату события.';
                                    return ['errors' => $errors];
                                }
                            }

                            // Проверка картинки
                            if (!empty($image)) {
                                $type = explode(';', $image)[0];
                                $type = explode('/', $type)[1];
                                if (!$db->checkFileImage($type)) {
                                    $errors[] = 'Ошибка загрузки изображения. Убедитесь что файл является изображением.
                                     Допустимые форматы: ' . implode(", ",
                                            ConfigDataDB::ALLOWS_IMAGE_EXTENSION);
                                    return ['errors' => $errors];
                                }
                            }

                            // Проверяем опрос
                            $pollExists = false;
                            if (!empty($pollTheme) && !empty($pollAnswers) && !empty($pollAnon)) {
                                if (count($pollAnswers) > 0 && count($pollAnswers) <= ConfigDataDB::LIMIT_COUNT_POLL_ANSWERS) {
                                    if ($pollAnon == 'true' || $pollAnon == 'false') {
                                        if (iconv_strlen($pollTheme) <= ConfigDataDB::LIMIT_SYMBOLS_POLL_THEME) {
                                            foreach ($pollAnswers as $key => $value) {
                                                if (!empty($value)) {
                                                    if (iconv_strlen($value) > ConfigDataDB::LIMIT_SYMBOLS_POLL_ANSWER) {
                                                        $errors[] = 'Количество символов в варианте ответа к опросу не 
                                                        должно превышать ' . ConfigDataDB::LIMIT_SYMBOLS_POLL_ANSWER;
                                                        return ['errors' => $errors];
                                                    }
                                                    $pollExists = true;
                                                } else {
                                                    $errors[] = "Варианты ответов не должны быть пустыми!";
                                                    return ['errors' => $errors];
                                                }
                                            }
                                        } else {
                                            $errors[] = 'Количество символов в теме опроса не должно превышать ' .
                                                ConfigDataDB::LIMIT_SYMBOLS_POLL_THEME;
                                        }
                                    } else {
                                        $errors[] = 'Значение PollAnon должно быть true либо false.';
                                        return ['errors' => $errors];
                                    }
                                } else {
                                    $errors[] = 'Количество ответов в опросе должно быть больше 0 и не больше ' .
                                        ConfigDataDB::LIMIT_COUNT_POLL_ANSWERS;
                                    return ['errors' => $errors];
                                }
                            }

                            if ($db->addNews($resultArray['auth_data']['id'],
                                $theme,
                                $description,
                                $image,
                                $videoLink,
                                $eventDate,
                                $eventDescription,
                                ($pollExists) ? $pollTheme : null,
                                ($pollExists) ? $pollAnswers : null,
                                ($pollExists) ? $pollAnon : null)) {
                                return ['status' => "OK"];
                            } else {
                                $errors[] = 'Ошибка добавления новости.';
                            }
                        } else {
                            $errors[] = 'Длина темы или описания больше допустимого значения. Допускается: Тема (до ' .
                                ConfigDataDB::LIMIT_SYMBOLS_NEWS_THEME . ' символов) Описание (до ' .
                                ConfigDataDB::LIMIT_SYMBOLS_NEWS_DESCRIPTION . ' символов) Видео YouTube (до ' .
                                ConfigDataDB::LIMIT_SYMBOLS_VIDEO_YOUTUBE . ' символов).';
                        }
                    } else {
                        $errors[] = 'Введенные данные имеют неверный формат.';
                    }
                } else {
                    $errors[] = 'Введенные данные имеют неверный формат.';
                }
            } else {
                $errors[] = 'Аккаунт заблокирован.';
            }
        } else {
            $errors[] = 'Чтобы оставить запись, нужно авторизоваться.';
        }

        return ['errors' => $errors];
    }

    // Удалить новость
    public function actionRemove()
    {
        $id = Yii::$app->request->post('id');

        $db = new DBHelper();
        $resultArray = ControlsAPI::checkAuth();
        $errors = array();

        if (!empty($resultArray['auth_data'])) {
            if (!$db->checkBlockAccount($resultArray['auth_data']['id'])) {
                if (isset($id)) {
                    if (!empty($id)) {
                        if ($resultArray['auth_data']['role'] == "admin") {
                            if ($db->removeNews($id)) {
                                // Успешное удаление
                                return ['status' => "OK"];
                            } else {
                                $errors[] = 'Ошибка удаления новости. Проверьте её id.';
                            }
                        } else {
                            $errors[] = 'Удаление разрешено только администрации.';
                        }
                    } else {
                        $errors[] = 'Введенные данные имеют неверный формат.';
                    }
                } else {
                    $errors[] = 'Введенные данные имеют неверный формат.';
                }
            } else {
                $errors[] = 'Аккаунт заблокирован.';
            }
        } else {
            $errors[] = 'Чтобы оставить запись, нужно авторизоваться.';
        }
    }

    // Проголосовать в опросе
    public function actionVotepoll()
    {
        $db = new DBHelper();
        $authData = ControlsAPI::checkAuth();
        $errors = array();

        $newsId = Yii::$app->request->post('news_id');
        $answerId = Yii::$app->request->post('answer_id');

        if (!empty($authData['auth_data'])) {
            if (!$db->checkBlockAccount($authData['auth_data']['id'])) {
                if ($db->getNews($newsId) != null) {
                    $news = $db->getNews($newsId);
                    if ($db->checkAnswerOfPollExistsInNews($newsId, $answerId) != null) {
                        if (!$db->checkVotedPoll($authData['auth_data']['id'], $news['poll_id'])) {
                            if ($db->votePollInNews($authData['auth_data']['id'], $newsId, $answerId)) {
                                $poll = $db->getPollById($news['poll_id']);

                                $result = array();
                                $result['status'] = "OK";
                                $result['poll'] = $poll;
                                return $result;
                            } else {
                                $errors[] = 'Ошибка голосования.';
                            }
                        } else {
                            $errors[] = 'Вы уже голосовали в этом опросе.';
                        }
                    } else {
                        $errors[] = 'Вариант ответа не найден.';
                    }
                } else {
                    $errors[] = 'Новость не найдена.';
                }
            } else {
                $errors[] = 'Аккаунт заблокирован.';
            }
        } else {
            $errors[] = 'Необходима авторизация.';
        }

        return ['errors' => $errors];
    }

    // Отменить голос в опросе
    public function actionCancelvotepoll()
    {
        $db = new DBHelper();
        $authData = ControlsAPI::checkAuth();
        $errors = array();

        $newsId = Yii::$app->request->post('news_id');

        if (!empty($authData['auth_data'])) {
            if (!$db->checkBlockAccount($authData['auth_data']['id'])) {
                if ($db->getNews($newsId) != null) {
                    $news = $db->getNews($newsId);
                    if ($news['poll_id'] != null) {
                        if ($db->checkVotedPoll($authData['auth_data']['id'], $news['poll_id'])) {
                            if ($db->cancelVotePoll($authData['auth_data']['id'], $news['poll_id'])) {
                                return [
                                    'status' => 'OK',
                                    'poll' => $db->getPollById($news['poll_id'])
                                ];
                            } else {
                                $errors[] = 'Ошибка отмены голоса.';
                            }
                        } else {
                            $errors[] = 'Вы не проголосовали в этом опросе.';
                        }
                    } else {
                        $errors[] = 'Новость не найдена.';
                    }
                } else {
                    $errors[] = 'Новость не найдена.';
                }
            } else {
                $errors[] = 'Аккаунт заблокирован.';
            }
        } else {
            $errors[] = 'Необходима авторизация.';
        }

        return ['errors' => $errors];
    }
}