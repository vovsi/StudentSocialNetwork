<?php

namespace app\modules\v1\controllers;

use app\config\ConfigDataDB;
use Yii;
use yii\rest\ActiveController;
use app\models\ControlsAPI;
use app\models\DBHelper;

class MainController extends ActiveController
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
                'Access-Control-Allow-Origin' => ['*'],
            ],

        ];
        $behaviors['contentNegotiator'] = [
            'class' => \yii\filters\ContentNegotiator::className(),
            'formats' => [
                'application/json' => \yii\web\Response::FORMAT_JSON,
            ],
        ];
        //$behaviors['rateLimiter']['enableRateLimitHeaders'] = false;
        return $behaviors;
    }

    // Проверить авторизацию пользователя
    public function actionCheckauth()
    {
        return ControlsAPI::checkAuth();
    }

    // Авторизация (когда пользователь отправляет данные авторизации с формы)
    public function actionAuth()
    {
        $email = Yii::$app->request->post('email');
        $password = Yii::$app->request->post('password');

        $errors = array();
        if (!empty($email) && !empty($password)) {
            $db = new DBHelper();
            $password = md5(md5($password));
            $res = $db->auth($email, $password);
            if ($res != null) {
                if (!$db->checkBlockAccount($res['id'])) {
                    // Привязываем ip к аккаунту (если ещё не привязан)
                    if (!$db->checkIpExists($res['id'], $_SERVER['REMOTE_ADDR'])) {
                        $db->addIpToAccount($res['id'], $_SERVER['REMOTE_ADDR']);
                    }

                    // Записать данные авторизации (если они есть)
                    $resultArray['auth_data'] = $db->checkHashPasswords($email, $password);
                    $resultArray['profile'] = $db->getProfile($email);
                    $countNewDialogsMsgs = $db->getCountNotViewedDialogsMessages($resultArray['auth_data']['id']);
                    $countNewConversationsMsgs = $db->getCountNotViewedConversationsMessages($resultArray['auth_data']['id']);
                    $resultArray['count_new_group_msgs'] = $countNewDialogsMsgs + $countNewConversationsMsgs;
                    $db->refreshVisit($res['id']);
                    return $resultArray;
                } else {
                    $errors[] = "Аккаунт заблокирован.";
                    return ['errors' => $errors];
                }
            } else {
                // Неверный email или пароль
                $errors[] = "Неверный email или пароль.";
                return ['errors' => $errors];
            }
        } else {
            $errors[] = "Неверные данные авторизации.";
            return ['errors' => $errors];
        }
    }

    // Получить кол-во непрочитанных групп сообщений (диалоги, беседы)
    public function actionGetcountnotviewedgroupmsgs()
    {
        $errors = array();
        $db = new DBHelper();
        $authData = ControlsAPI::checkAuth();
        if ($authData != null) {
            if (!$db->checkBlockAccount($authData['auth_data']['id'])) {
                $resultArray['count_new_dialogs_msgs'] = $db->getCountNotViewedDialogsMessages($authData['auth_data']['id']);
                $resultArray['count_new_conversations_msgs'] = $db->getCountNotViewedConversationsMessages($authData['auth_data']['id']);
                return $resultArray;
            } else {
                $errors[] = "Аккаунт заблокирован.";
                return ['errors' => $errors];
            }
        } else {
            // Неверный email или пароль
            $errors[] = "Неверный email или пароль.";
            return ['errors' => $errors];
        }
    }

    // Отменить голос в опросе
    public function actionCancelvotepoll()
    {
        $db = new DBHelper();
        $authData = ControlsAPI::checkAuth();
        $errors = array();

        $postId = Yii::$app->request->post('post_id');

        if (!empty($authData['auth_data'])) {
            if (!$db->checkBlockAccount($authData['auth_data']['id'])) {
                if ($db->getPost($postId) != null) {
                    $post = $db->getPost($postId);
                    if ($post->poll_id != null) {
                        if ($db->checkVotedPoll($authData['auth_data']['id'], $post->poll_id)) {
                            if ($db->cancelVotePoll($authData['auth_data']['id'], $post->poll_id)) {
                                return [
                                    'status' => 'OK',
                                    'poll' => $db->getPollById($post->poll_id)
                                ];
                            } else {
                                $errors[] = 'Ошибка отмены голоса.';
                            }
                        } else {
                            $errors[] = 'Вы не проголосовали в этом опросе.';
                        }
                    } else {
                        $errors[] = 'Запись не найдена.';
                    }
                } else {
                    $errors[] = 'Запись не найдена.';
                }
            } else {
                $errors[] = 'Аккаунт заблокирован.';
            }
        } else {
            $errors[] = 'Необходима авторизация.';
        }

        return ['errors' => $errors];
    }

    // Проголосовать в опросе
    public function actionVotepoll()
    {
        $db = new DBHelper();
        $authData = ControlsAPI::checkAuth();
        $errors = array();

        $postId = Yii::$app->request->post('post_id');
        $answerId = Yii::$app->request->post('answer_id');

        if (!empty($authData['auth_data'])) {
            if (!$db->checkBlockAccount($authData['auth_data']['id'])) {
                if ($db->getPost($postId) != null) {
                    $post = $db->getPost($postId);
                    if ($db->checkAnswerOfPollExistsInPost($postId, $answerId) != null) {
                        if (!$db->checkVotedPoll($authData['auth_data']['id'], $post->poll_id)) {
                            if ($db->votePollInPost($authData['auth_data']['id'], $postId, $answerId)) {
                                $poll = $db->getPollById($post->poll_id);

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
                    $errors[] = 'Запись не найдена.';
                }
            } else {
                $errors[] = 'Аккаунт заблокирован.';
            }
        } else {
            $errors[] = 'Необходима авторизация.';
        }

        return ['errors' => $errors];
    }

    // Получить записи профиля
    public function actionGetposts($id = 0, $limit = 10, $offset = 0)
    {
        $db = new DBHelper();
        $authData = ControlsAPI::checkAuth();
        $errors = array();
        if (!empty($authData['auth_data'])) {
            if (!$db->checkBlockAccount($authData['auth_data']['id'])) {
                // Существует ли такой id?
                if ($db->accountIdExists($id)) {
                    return $db->getPostsUser($id, $limit, $offset);
                } else {
                    $errors[] = 'Такого пользователя не существует!';
                }
            } else {
                $errors[] = 'Аккаунт заблокирован.';
            }
        } else {
            $errors[] = 'Необходима авторизация.';
        }

        return ['errors' => $errors];
    }

    // Получить данные профиля определенному пользователю
    public function actionGetprofiletouser($id = 0)
    {
        $db = new DBHelper();
        $authData = ControlsAPI::checkAuth();
        $errors = array();
        if (!empty($authData['auth_data'])) {
            if (!$db->checkBlockAccount($authData['auth_data']['id'])) {
                // Существует ли такой id?
                if ($db->accountIdExists($id)) {
                    // Записать данные профиля
                    $resultArray['profile'] = $db->getProfileById($id);

                    // Проверить что это не текущий аккаунт
                    if ($id != $authData['auth_data']['id']) {
                        // Проверить на наличие в списке друзей
                        $resultArray['profile']['is_favorite'] = $db->checkExistsFavorite($authData['auth_data']['id'],
                            $id);

                        // Проверить аккаунт на наличие в ЧС
                        $resultArray['profile']['black_list'] = $db->checkBlackListUser($authData['auth_data']['id'],
                            $id);

                        // Добавить информацию приватности
                        $resultArray['privacy'] = $db->getPrivacyOfUser($id);

                        // Добавить информацию приватности (ЧС) может ли авторизированный польз. просматривать
                        // профиль. true - запретить просмотр
                        $blackListView = $db->checkBlackListUser($id, $authData['auth_data']['id']);
                        if ($blackListView) {
                            $errors[] = 'Пользователь добавил вас в чёрный список. Просмотр профиля запрещен.';
                            return ['errors' => $errors];
                        }
                    }
                    // Выводим профиль этого аккаунта
                    return $resultArray;
                } else {
                    $errors[] = 'Такого пользователя не существует!';
                }
            } else {
                $errors[] = 'Аккаунт заблокирован.';
            }
        } else {
            $errors[] = 'Необходима авторизация.';
        }
        return ['errors' => $errors];
    }

    // Добавить новую запись на страницу
    public function actionAddpost()
    {
        $accountToId = Yii::$app->request->post('account_to_id');
        $message = Yii::$app->request->post('message');
        $image = Yii::$app->request->post('image');
        $videoLink = htmlspecialchars(Yii::$app->request->post('video_link'));
        $files = htmlspecialchars(Yii::$app->request->post('files'));
        $pollTheme = htmlspecialchars(Yii::$app->request->post('poll_theme'));
        $pollAnswers = Yii::$app->request->post('poll_answers');
        $pollAnon = Yii::$app->request->post('poll_anon');
        if ($pollAnswers != null) {
            if (count($pollAnswers) > 0) {
                for ($i = 0; $i < count($pollAnswers); $i++) {
                    $pollAnswers[$i] = htmlspecialchars($pollAnswers[$i]);
                }
            }
        }
        $db = new DBHelper();
        $resultArray = ControlsAPI::checkAuth();
        $errors = array();
        if (!empty($resultArray['auth_data'])) {
            if (isset($accountToId) && isset($message)) {
                if (!empty($accountToId)) {
                    if (!$db->checkBlockAccount($resultArray['auth_data']['id']) && !$db->checkBlockAccount($accountToId)) {
                        $messageExist = false;
                        $imageExist = false;
                        $videoLinkExist = false;
                        $filesExist = false;
                        $pollExist = false;

                        // Проверяем наличие сообщения
                        if (isset($message)) {
                            if (!empty($message)) {
                                $messageExist = true;
                                $message = htmlspecialchars($message);
                            }
                        }

                        // Проверяем наличие файла(ов)
                        if (isset($files)) {
                            if (!empty($files)) {
                                $filesExist = true;
                                $files = htmlspecialchars($files);

                                $filesParts = explode("|", $files);
                                $countSelectFiles = count($filesParts) - 1;
                                if ($countSelectFiles > ConfigDataDB::LIMIT_SELECTED_FILES) {
                                    $errors[] = 'Выбрано слишком много файлов. Доступно выбрать максимум ' . ConfigDataDB::LIMIT_SELECTED_FILES . ' файлов.';
                                    $dataOut['errors'] = $errors;
                                    return $dataOut;
                                }
                            }
                        }

                        // Проверяем наличие картинки
                        if (isset($image)) {
                            if (!empty($image)) {
                                $type = explode(';', $image)[0];
                                $type = explode('/', $type)[1];
                                if ($db->checkFileImage($type)) {
                                    $imageExist = true;
                                } else {
                                    $errors[] = 'Ошибка загрузки изображения. Убедитесь что файл является изображением. Допустимые форматы: ' . implode(", ",
                                            ConfigDataDB::ALLOWS_IMAGE_EXTENSION);
                                    return ['errors' => $errors];
                                }
                            }
                        }

                        if (!empty($videoLink)) {
                            $videoLinkExist = true;
                        }

                        if (!empty($pollTheme) && !empty($pollAnswers)) {
                            if (count($pollAnswers) > 0) {
                                $pollExist = true;
                            }
                        }

                        // Если опрос есть, то проверяем размер текста в теме и ответах
                        if ($pollExist) {
                            if (iconv_strlen($pollTheme) > ConfigDataDB::LIMIT_SYMBOLS_POLL_THEME) {
                                $errors[] = 'Тема не должна превышать ' . ConfigDataDB::LIMIT_SYMBOLS_POLL_THEME . ' символов.';
                                return ['errors' => $errors];
                            }
                            for ($i = 0; $i < count($pollAnswers); $i++) {
                                if (iconv_strlen($pollAnswers[$i]) > ConfigDataDB::LIMIT_SYMBOLS_POLL_ANSWER) {
                                    $errors[] = 'Ответ опроса не должен превышать ' . ConfigDataDB::LIMIT_SYMBOLS_POLL_ANSWER . ' символов.';
                                    return ['errors' => $errors];
                                }
                            }
                        }

                        // Если сообщение есть, то проверяем что оно не длинее Config_dataDB::LIMIT_SYMBOLS_POST символов
                        if ($messageExist) {
                            if (iconv_strlen($message) > ConfigDataDB::LIMIT_SYMBOLS_POST) {
                                $errors[] = 'Текст запси не должен превышать ' . ConfigDataDB::LIMIT_SYMBOLS_POST . ' символов.';
                                return ['errors' => $errors];
                            }
                        }

                        // Если сообщение или файл или видео заданы
                        if ($messageExist || $imageExist || $videoLinkExist || $filesExist || $pollExist) {
                            // Отправляем...
                            $post = $db->addPost($resultArray['auth_data']['id'],
                                $accountToId,
                                ($imageExist) ? $image : null,
                                ($messageExist) ? $message : null,
                                (empty($videoLink) ? null : $videoLink),
                                ($filesExist) ? $files : null,
                                ($pollExist) ? $pollTheme : null,
                                ($pollExist) ? $pollAnswers : null,
                                ($pollExist) ? $pollAnon : null);
                            if ($post != null) {
                                // УСПЕХ
                                $postArray = $db->getPostAsArray($post->id);
                                $fromUser = $db->getProfileById($resultArray['auth_data']['id']);
                                $postArray['first_name_fromUser'] = $fromUser['first_name'];
                                $postArray['last_name_fromUser'] = $fromUser['last_name'];
                                $postArray['photo_path_fromUser'] = $fromUser['photo_path'];
                                return [
                                    'status' => 'OK',
                                    'post' => $postArray
                                ];
                            } else {
                                $errors[] = 'Ошибка в отправляемых данных, проверьте все поля ввода.';
                            }
                        } else {
                            $errors[] = 'Ошибка отправки записи. Проверьте поля ввода.';
                        }
                    } else {
                        $errors[] = 'Аккаунт заблокирован.';
                    }
                } else {
                    $errors[] = 'Введенные данные имеют неверный формат.';
                }
            } else {
                $errors[] = 'Введенные данные имеют неверный формат.';
            }
        } else {
            $errors[] = 'Чтобы оставить запись, нужно авторизоваться.';
        }

        return ['errors' => $errors];
    }

    // Удалить запись на странице
    public function actionRemovepost()
    {
        $authData = ControlsAPI::checkAuth();
        if (!empty($authData)) {
            $idPost = Yii::$app->request->post('idPost');
            $errors = array();
            $db = new DBHelper();
            if (!empty($idPost)) {
                $post = $db->getPost($idPost);
                if ($post->account_from_id == $authData['auth_data']['id']) {
                    if (!$db->checkBlockAccount($post->account_to_id) && !$db->checkBlockAccount($authData['auth_data']['id'])) {
                        if ($db->removePost($idPost)) {
                            return ['status' => 'OK'];
                        } else {
                            $errors[] = 'Ошибка удаления записи. Проверьте правильность введенного id.';
                        }
                    } else {
                        $errors[] = 'Аккаунт заблокирован.';
                    }
                } else {
                    $errors[] = 'Указаннвя запись принадлежит не вам.';
                }
            } else {
                $errors[] = 'Пустое значение id';
            }
        } else {
            $errors[] = 'Необходима авторизация.';
        }
        return ['errors' => $errors];
    }

    // Обновить фото профиля
    public function actionUpdatephoto()
    {
        $authData = ControlsAPI::checkAuth();
        $db = new DBHelper();
        $file = Yii::$app->request->post('file');
        if (!empty($authData['auth_data'])) {
            if (!$db->checkBlockAccount($authData['auth_data']['id'])) {
                if (!empty($file)) {
                    $type = explode(';', $file)[0];
                    $type = explode('/', $type)[1];
                    if ($db->checkFileImage($type)) {
                        if ($db->updateProfilePhoto($file, $authData['auth_data']['id'])) {
                            // Успешное обновление произошло
                            return ['status' => "OK"];
                        } else {
                            $errors[] = 'Ошибка обновления фотографии.';
                        }
                    } else {
                        $errors[] = "Неверный формат файла. Допустимые форматы: " . implode(", ",
                                ConfigDataDB::ALLOWS_IMAGE_EXTENSION);
                    }
                } else {
                    $errors[] = 'Файл не найден.';
                }
            } else {
                $errors[] = 'Аккаунт заблокирован.';
            }
        } else {
            $errors[] = 'Необходима авторизация.';
        }
        return ['errors' => $errors];
    }

    // Добавить в чёрный список пользователя
    public function actionAddblacklist()
    {
        $authData = ControlsAPI::checkAuth();
        $db = new DBHelper();
        $id = Yii::$app->request->post('id');
        if (!empty($authData['auth_data'])) {
            if (!$db->checkBlockAccount($authData['auth_data']['id'])) {
                if (isset($id)) {
                    if (!empty($id)) {
                        // Если пользователь не добавляет сам себя в чс
                        if ($id != $authData['auth_data']['id']) {
                            // Проверить что пользователя нет в чёрном списке
                            if (!$db->checkBlackListUser($authData['auth_data']['id'], $id)) {
                                if ($db->addUserToBlackList($authData['auth_data']['id'], $id)) {
                                    // Пользователь успешно добавлен в чёрный список
                                    return ['status' => 'OK'];
                                } else {
                                    $errors[] = 'Ошибка добавления пользователя в чёрный список.';
                                }
                            } else {
                                $errors[] = 'Пользователь уже содержится в вашем чёрном списке.';
                            }
                        } else {
                            $errors[] = 'Вы не можете добавить себя в чёрный список.';
                        }
                    } else {
                        $errors[] = 'Неверный формат id пользователя.';
                    }
                } else {
                    $errors[] = 'Неверный формат id пользователя.';
                }
            } else {
                $errors[] = 'Аккаунт заблокирован.';
            }
        } else {
            $errors[] = 'Необходима авторизация.';
        }
        return ['errors' => $errors];
    }

    // Удалить пользователя из чёрного списка
    public function actionRemoveblacklist()
    {
        $authData = ControlsAPI::checkAuth();
        $db = new DBHelper();
        $id = Yii::$app->request->post('id');
        if (!empty($authData['auth_data'])) {
            if (!$db->checkBlockAccount($authData['auth_data']['id'])) {
                if (isset($id)) {
                    if (!empty($id)) {
                        // Проверить что пользователь есть в чёрном списке
                        if ($db->checkBlackListUser($authData['auth_data']['id'], $id)) {
                            if ($db->removeUserFromBlackList($authData['auth_data']['id'], $id)) {
                                // Пользователь успешно удален из чёрного списока
                                return ['status' => 'OK'];
                            } else {
                                $errors[] = 'Ошибка удаления пользователя из чёрного списока.';
                            }
                        } else {
                            $errors[] = 'Пользователя нет в чёрном списке.';
                        }
                    } else {
                        $errors[] = 'Неверный формат id пользователя.';
                    }
                } else {
                    $errors[] = 'Неверный формат id пользователя.';
                }
            } else {
                $errors[] = 'Аккаунт заблокирован.';
            }
        } else {
            $errors[] = 'Необходима авторизация.';
        }
        return ['errors' => $errors];
    }

    // Получить Base64 строку из url на изображение !!! ДОБАВИТЬ В ДОКУМЕНТАЦИЮ
    public function actionGetbase64fromurlimage($url)
    {
        $res = "";
        if (!empty($url)) {
            $res = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($url));
        }
        return $res;
    }

    // Получить массив проголосовавших пользователей за вариант ответа в опросе
    public function actionGetpollanswervoted($pollAnswerId)
    {
        $db = new DBHelper();
        $authData = ControlsAPI::checkAuth();
        $errors = array();
        if (!empty($authData['auth_data'])) {
            if (!$db->checkBlockAccount($authData['auth_data']['id'])) {
                // Существует ли такой id?
                if ($db->getPollAnswer($pollAnswerId) != null) {
                    $answer = $db->getPollAnswer($pollAnswerId);
                    $poll = $db->getPollById($answer->poll_id);
                    // Если опрос анонимный, то отправляем сообщение об ошибке
                    if ($poll['anon'] == 1) {
                        $errors[] = 'Этот опрос анонимный! Доступ к проголосовавшим запрещен.';
                        return ['errors' => $errors];
                    }
                    $voted = $db->getPollAnswerVoted($pollAnswerId);
                    return [
                        'status' => 'OK',
                        'votedAccounts' => $voted
                    ];
                } else {
                    $errors[] = 'Такого варианта ответа не существует!';
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
