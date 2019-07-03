<?php

namespace app\modules\v1\controllers;

use app\config\ConfigDB;
use app\models\ControlsAPI;
use app\config\ConfigDataDB;
use app\models\DBHelper;
use Yii;
use yii\rest\ActiveController;

class MessagesController extends ActiveController
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

    // Получить список диалогов
    public function actionGetdialogs($limit = 10, $offset = 0)
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();

        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                $dialogs = $db->getIdsDialogsUserLimitAndOffset($data['auth_data']['id'], $limit, $offset);
                $dialogsShowHtml = array();
                foreach ($dialogs['result'] as $key => $value) {
                    $dialog = $db->getDialogById($value);
                    // Получаем массив аккаунта собеседника
                    if ($data['auth_data']['id'] == $dialog->to_id) {
                        $userTo = $db->getAccountAsArray($dialog->from_id);

                    } else {
                        if ($data['auth_data']['id'] == $dialog->from_id) {
                            $userTo = $db->getAccountAsArray($dialog->to_id);
                        }
                    }
                    $userToPersonalInfo = $db->getPersonalInfoById($userTo['personal_info_id']);
                    $lastMessageId = $db->getLastMessageIdOfDialog($dialog->id);

                    $groupUserTo = $db->getGroupById($userTo['group_id']);

                    $lastMessage = $db->getMessageAsArray($lastMessageId);
                    $messageText = $lastMessage['text']; // Текст сообщения
                    $messageAttPhoto = $lastMessage['photo_path']; // Прикрепленное фото
                    $messageAttFiles = $lastMessage['files']; // Прикрепленные файлы
                    $messageAttVideoYT = $lastMessage['videoYT']; // Прикрепленное видео YT
                    $senderPoint = ''; // Метка сообщения (отправителя)
                    if ($data['auth_data']['id'] == $lastMessage['sender_id']) // Если последнее сообщение от вас - то перед сообщением будет указано 'Вы:'
                    {
                        $senderPoint = 'Вы:&nbsp;';
                    }
                    $dialogsShowHtml[] = [
                        'dialog_id' => $dialog->id,
                        'interlocutor_id' => $userTo['id'],
                        'interlocutor_image' => ControlsAPI::HOST . $userToPersonalInfo->photo_path,
                        'interlocutor_first_name' => $userTo['first_name'],
                        'interlocutor_last_name' => $userTo['last_name'],
                        'interlocutor_status_visit' => $db->getStatusVisit($userTo['id']),
                        'interlocutor_group' => $groupUserTo->name,
                        'last_message' => $messageText,
                        'last_message_photo' => $messageAttPhoto,
                        'last_message_files' => $messageAttFiles,
                        'last_message_videoYT' => $messageAttVideoYT,
                        'last_message_viewed' => $lastMessage['viewed'],
                        'sender_point' => $senderPoint,
                        'date_change' => date_create($dialog->date_change)->Format('Y-m-d H:i'),
                        'count_new_messages' => count($db->getNewMessagesOfDialog($dialog->id,
                            $data['auth_data']['id']))
                    ];
                }
                $data['is_there_more'] = $dialogs['is_there_more'];
                $data['dialogs'] = $dialogsShowHtml;
                return $data;
            } else {
                $errors[] = 'Аккаунт заблокирован.';
            }
        } else {
            $errors[] = 'Необходима авторизация.';
        }

        $dataOut['errors'] = $errors;
        return $dataOut;
    }

    // Получить список бесед
    public function actionGetconversations($limit = 10, $offset = 0)
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();

        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                $conversations = $db->getIdsConversationsUser($data['auth_data']['id']);
                $conversationsShowHtml = array();

                foreach ($conversations as $key => $value) {
                    $conversation = $db->getConversationAsObject($value);
                    $lastMessageId = $db->getLastMessageIdOfConversation($conversation->id);
                    $lastMsg = $db->getMessageAsObject($lastMessageId);
                    $userLastMsg = $db->getAccountAsObject($lastMsg->sender_id);

                    $lastMessage = $db->getMessageAsArray($lastMessageId);
                    $messageText = $lastMessage['text']; // Текст сообщения
                    $messageAttPhoto = $lastMessage['photo_path']; // Прикрепленное фото
                    $messageAttFiles = $lastMessage['files']; // Прикрепленные файлы
                    $messageAttVideoYT = $lastMessage['videoYT']; // Прикрепленное видео YT
                    $senderPoint = ''; // Метка сообщения (отправителя)
                    if ($data['auth_data']['id'] == $lastMessage['sender_id']) // Если последнее сообщение от вас - то перед сообщением будет указано 'Вы:'
                    {
                        $senderPoint = 'Вы:&nbsp;';
                    } else {
                        $senderPoint = $userLastMsg->first_name . ':&nbsp;';
                    }
                    $conversationsShowHtml[] = [
                        'conversation_id' => $conversation->id,
                        'conversation_name' => $conversation->name,
                        'conversation_photo' => DBHelper::HOST . $conversation->photo_path,
                        'sender_id' => $userLastMsg->id,
                        'sender_first_name' => $userLastMsg->first_name,
                        'last_message' => $messageText,
                        'last_message_photo' => $messageAttPhoto,
                        'last_message_files' => $messageAttFiles,
                        'last_message_videoYT' => $messageAttVideoYT,
                        'last_message_viewed' => $lastMessage['viewed'],
                        'sender_point' => $senderPoint,
                        'date_change' => date_create($conversation->date_change)->Format('Y-m-d H:i'),
                        'date_change_full' => $conversation->date_change,
                        'count_new_messages' => count($db->getNewMessagesOfConversation($conversation->id,
                            $data['auth_data']['id']))
                    ];
                }

                // Сортировка списка бесед по убыванию даты-время. Чтобы сначала шли элементы самые актуальные
                uksort($conversationsShowHtml, function ($ak, $bk) use ($conversationsShowHtml) {
                    $a = $conversationsShowHtml[$ak];
                    $b = $conversationsShowHtml[$bk];
                    if ($a['date_change_full'] === $b['date_change_full']) {
                        return $ak - $bk;
                    }
                    return $a['date_change_full'] < $b['date_change_full'] ? 1 : -1;
                });

                // Проверяем, есть ли ещё записи?
                $limit++;
                // Получаем беседы с отступом и лимитом
                $sliceConversations =  array_slice($conversationsShowHtml, $offset, $limit);
                if(count($sliceConversations) == $limit) {
                    $data['is_there_more'] = true;
                    array_pop($sliceConversations);
                }else{
                    $data['is_there_more'] = false;
                }

                $data['conversations'] = $sliceConversations;

                return $data;

            } else {
                $errors[] = 'Аккаунт заблокирован.';
            }
        } else {
            $errors[] = 'Необходима авторизация.';
        }

        $dataOut['errors'] = $errors;
        return $dataOut;
    }

    // Отправить сообщение в диалог
    public function actionSendtodialog()
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();

        $accountToId = Yii::$app->request->post('account_to_id');
        $message = Yii::$app->request->post('message');
        $image = Yii::$app->request->post('image');
        $files = Yii::$app->request->post('files');
        $videoYT = Yii::$app->request->post('videoYT');

        try {
            if (!empty($data['auth_data'])) {
                if (isset($accountToId)) {
                    if (!empty($db->getAccountAsObject($accountToId))) {
                        if (!$db->checkBlockAccount($data['auth_data']['id']) && !$db->checkBlockAccount($accountToId)) {
                            // Если пользователь не добавил отправителя в чс
                            if (!$db->checkBlackListUser($accountToId, $data['auth_data']['id'])) {
                                // Если вы не добавили пользователя в чс
                                if (!$db->checkBlackListUser($data['auth_data']['id'], $accountToId)) {
                                    $accountToId = htmlspecialchars($accountToId);
                                    $messageExist = false;
                                    $imageExist = false;
                                    $filesExist = false;
                                    $videoYTExist = false;

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

                                    // Проверяем наличие YouTube видео
                                    if (isset($videoYT)) {
                                        if (!empty($videoYT)) {
                                            $videoYTExist = true;
                                            $videoYT = htmlspecialchars($videoYT);
                                        }
                                    }

                                    // Проверяем наличие файла
                                    if (isset($image)) {
                                        if (!empty($image)) {
                                            $type = explode(';', $image)[0];
                                            $type = explode('/', $type)[1];
                                            if ($db->checkFileImage($type)) {
                                                $imageExist = true;
                                            } else {
                                                $errors[] = 'Ошибка загрузки изображения. Убедитесь что файл является изображением. Допустимые форматы: ' . implode(", ",
                                                        ConfigDataDB::ALLOWS_IMAGE_EXTENSION);
                                                $dataOut['errors'] = $errors;
                                                return $dataOut;
                                            }
                                        }
                                    }

                                    // Если сообщение есть, то проверяем что оно не длинее 3000 символов
                                    if ($messageExist) {
                                        if (iconv_strlen($message) > ConfigDataDB::LIMIT_SYMBOLS_MESSAGE) {
                                            $errors[] = 'Сообщение не должно превышать ' . ConfigDataDB::LIMIT_SYMBOLS_MESSAGE . ' символов.';
                                            $dataOut['errors'] = $errors;
                                            return $dataOut;
                                        }
                                    }

                                    // Если сообщение или файл(ы) или изображение или видео YT заданы
                                    if ($messageExist || $imageExist || $filesExist || $videoYTExist) {
                                        // Отправляем...
                                        $newMessage = $db->sendMessageToDialog($data['auth_data']['id'],
                                            $accountToId,
                                            ($messageExist) ? $message : null,
                                            ($imageExist) ? $image : null,
                                            ($filesExist) ? $files : null,
                                            ($videoYTExist) ? $videoYT : null);
                                        if ($newMessage != null) {
                                            // УСПЕХ
                                            $messageSend = $db->getMessageAsArray($newMessage->id);
                                            $senderProfile = $db->getProfileById($data['auth_data']['id']);

                                            $filesIds = explode("|", $messageSend['files']);
                                            $filesArr = array();
                                            foreach ($filesIds as $keyF => $valueF) {
                                                if (!empty($valueF)) {
                                                    $filesArr[] = $db->getFileAsArray($valueF);
                                                }
                                            }

                                            if (!empty($messageSend) && !empty($senderProfile)) {
                                                return [
                                                    'status' => 'OK',
                                                    'message' => [
                                                        'id' => $messageSend['id'],
                                                        'dialog_id' => $messageSend['dialog_id'],
                                                        'sender_id' => $messageSend['sender_id'],
                                                        'sender_first_name' => $senderProfile['first_name'],
                                                        'sender_last_name' => $senderProfile['last_name'],
                                                        'sender_photo_path' => $senderProfile['photo_path'],
                                                        'recipient_id' => $messageSend['recipient_id'],
                                                        'text' => $messageSend['text'],
                                                        'photo_path' => $messageSend['photo_path'],
                                                        'files' => $filesArr,
                                                        'videoYT' => $messageSend['videoYT'],
                                                        'date_send' => date_create($messageSend['date_send'])->Format('Y-m-d H:i'),
                                                        'viewed' => $messageSend['viewed']
                                                    ],
                                                ];
                                            } else {
                                                $errors[] = 'Ошибка отправки сообщения.';
                                            }
                                        } else {
                                            $errors[] = 'Ошибка отправки сообщения. Проверьте поля ввода.';
                                        }
                                    } else {
                                        $errors[] = 'Ошибка отправки сообщения. Проверьте поля ввода.';
                                    }
                                } else {
                                    $errors[] = 'Вы не можете писать сообщения пользователю, которого добавили в чёрный список.';
                                }
                            } else {
                                $errors[] = 'Пользователь добавил вас в чёрный список.';
                            }
                        } else {
                            $errors[] = 'Пользователь заблокирован.';
                        }
                    } else {
                        $errors[] = 'Такого пользователя нет.';
                    }
                } else {
                    $errors[] = 'Не указан id получателя.';
                }
            } else {
                $errors[] = 'Необходима авторизация.';
            }
        }catch (\Exception $ex) {
            $errors[] = $ex->getMessage();
        }

        $dataOut['errors'] = $errors;
        return $dataOut;
    }

    // Отправить сообщение в беседу
    public function actionSendtoconversation()
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();

        $conversationId = Yii::$app->request->post('conversation_id');
        $message = Yii::$app->request->post('message');
        $image = Yii::$app->request->post('image');
        $files = Yii::$app->request->post('files');
        $videoYT = Yii::$app->request->post('videoYT');

        if (!empty($data['auth_data'])) {
            if (isset($conversationId)) {
                // Не заблокирован ли пользователь?
                if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                    // Существует ли беседа?
                    if ($db->checkExistsConversation($conversationId)) {
                        // Находится ли пользователь в беседе
                        if ($db->checkConversationUser($data['auth_data']['id'], $conversationId)) {

                            $conversationId = htmlspecialchars($conversationId);
                            $messageExist = false;
                            $imageExist = false;
                            $filesExist = false;
                            $videoYTExist = false;

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

                            // Проверяем наличие YouTube видео
                            if (isset($videoYT)) {
                                if (!empty($videoYT)) {
                                    $videoYTExist = true;
                                    $videoYT = htmlspecialchars($videoYT);
                                }
                            }

                            // Проверяем наличие файла
                            if (isset($image)) {
                                if (!empty($image)) {
                                    $type = explode(';', $image)[0];
                                    $type = explode('/', $type)[1];
                                    if ($db->checkFileImage($type)) {
                                        $imageExist = true;
                                    } else {
                                        $errors[] = 'Ошибка загрузки изображения. Убедитесь что файл является изображением. Допустимые форматы: ' . implode(", ",
                                                ConfigDataDB::ALLOWS_IMAGE_EXTENSION);
                                        $dataOut['errors'] = $errors;
                                        return $dataOut;
                                    }
                                }
                            }

                            // Если сообщение есть, то проверяем что оно не длинее 3000 символов
                            if ($messageExist) {
                                if (iconv_strlen($message) > ConfigDataDB::LIMIT_SYMBOLS_MESSAGE) {
                                    $errors[] = 'Сообщение не должно превышать ' . ConfigDataDB::LIMIT_SYMBOLS_MESSAGE . ' символов.';
                                    $dataOut['errors'] = $errors;
                                    return $dataOut;
                                }
                            }

                            // Если сообщение или файл(ы) или изображение или видео YT заданы
                            if ($messageExist || $imageExist || $filesExist || $videoYTExist) {
                                // Отправляем...
                                $newMessage = $db->sendMessageToConversation($data['auth_data']['id'],
                                    $conversationId,
                                    ($messageExist) ? $message : null,
                                    ($imageExist) ? $image : null,
                                    ($filesExist) ? $files : null,
                                    ($videoYTExist) ? $videoYT : null);
                                if ($newMessage != null) {
                                    // УСПЕХ
                                    $messageSend = $db->getMessageAsArray($newMessage->id);
                                    $senderProfile = $db->getProfileById($data['auth_data']['id']);

                                    $filesIds = explode("|", $messageSend['files']);
                                    $filesArr = array();
                                    foreach ($filesIds as $keyF => $valueF) {
                                        if (!empty($valueF)) {
                                            $filesArr[] = $db->getFileAsArray($valueF);
                                        }
                                    }

                                    if (!empty($messageSend) && !empty($senderProfile)) {
                                        return [
                                            'status' => 'OK',
                                            'message' => [
                                                'id' => $messageSend['id'],
                                                'conversation_id' => $messageSend['conversation_id'],
                                                'sender_id' => $messageSend['sender_id'],
                                                'sender_first_name' => $senderProfile['first_name'],
                                                'sender_last_name' => $senderProfile['last_name'],
                                                'sender_photo_path' => $senderProfile['photo_path'],
                                                'text' => $messageSend['text'],
                                                'photo_path' => $messageSend['photo_path'],
                                                'files' => $filesArr,
                                                'videoYT' => $messageSend['videoYT'],
                                                'date_send' => date_create($messageSend['date_send'])->Format('Y-m-d H:i'),
                                                'viewed' => $messageSend['viewed']
                                            ],
                                        ];
                                    } else {
                                        $errors[] = 'Ошибка отправки сообщения.';
                                    }
                                } else {
                                    $errors[] = 'Ошибка отправки сообщения. Проверьте поля ввода.';
                                }
                            } else {
                                $errors[] = 'Ошибка отправки сообщения. Проверьте поля ввода.';
                            }

                        } else {
                            $errors[] = 'Вы не состоите в этой беседе.';
                        }
                    } else {
                        $errors[] = 'Такой беседы не существует.';
                    }
                } else {
                    $errors[] = 'Пользователь заблокирован.';
                }
            } else {
                $errors[] = 'Не указан id беседы.';
            }
        } else {
            $errors[] = 'Необходима авторизация.';
        }

        $dataOut['errors'] = $errors;
        return $dataOut;
    }

    // Создать беседу
    public function actionCreateconversation()
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();

        $name = htmlspecialchars(Yii::$app->request->post('name'));
        $members = Yii::$app->request->post('members');
        $imageBase64 = Yii::$app->request->post('image_base64');

        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                // Проверяем название беседы
                if (iconv_strlen($name) >= ConfigDataDB::MIN_SYMBOLS_CONVERSATION_NAME &&
                    iconv_strlen($name) <= ConfigDataDB::LIMIT_SYMBOLS_CONVERSATION_NAME) {
                    // Получаем и проверяем на валидность участников беседы
                    if (isset($members)) {
                        if (!empty($members)) {

                            // Если прикреплено изображение, то проверяем его тип
                            if (!empty($imageBase64)) {
                                $type = explode(';', $imageBase64)[0];
                                $type = explode('/', $type)[1];
                                if (!$db->checkFileImage($type)) {
                                    $errors[] = 'Ошибка загрузки изображения. Убедитесь что файл является изображением. Допустимые форматы: ' . implode(", ",
                                            ConfigDataDB::ALLOWS_IMAGE_EXTENSION);
                                    $dataOut['errors'] = $errors;
                                    return $dataOut;
                                }
                            }

                            // Парсим строку с участниками на массив
                            $members = htmlspecialchars($members);
                            $membersParts = explode("|", $members);
                            unset($membersParts[count($membersParts) - 1]); // Удаляем последний элемент (пустой)
                            $countSelectMembers = count($membersParts);

                            // Проверяем кол-во участников
                            if ($countSelectMembers > ConfigDataDB::LIMIT_SELECTED_CONVERSATION_MEMBERS &&
                                $countSelectMembers <= 0) {
                                $errors[] = 'Укажите минимум одного участника, а также не более '
                                    . ConfigDataDB::LIMIT_SELECTED_CONVERSATION_MEMBERS;
                                $dataOut['errors'] = $errors;
                                return $dataOut;
                            }

                            // Проверяем участников
                            foreach ($membersParts as $key => $value) {
                                if (!empty($value)) {
                                    $acc = $db->getAccountAsArray($value);
                                    // Если пользователя не существует, то ошибка
                                    if ($acc == null) {
                                        $errors[] = 'Не найден пользователь с id ' . $value;
                                        $dataOut['errors'] = $errors;
                                        return $dataOut;
                                    } else {
                                        // Если пользователь не добавил создателя беседы в чс
                                        if (!$db->checkBlackListUser($acc['id'], $data['auth_data']['id'])) {
                                            // Если создатель беседы не добавили участника в чс
                                            if (!$db->checkBlackListUser($data['auth_data']['id'], $acc['id'])) {
                                                // Если элемент $membersParts дошёл до этой строки, значит всё ОК
                                            } else {
                                                $errors[] = 'Пользователь с id ' . $value . ' у вас в чёрном списке. Вы не 
                                        можете его добавить в беседу.';
                                                $dataOut['errors'] = $errors;
                                                return $dataOut;
                                            }
                                        } else {
                                            $errors[] = 'Пользователь с id ' . $value . ' добавил вас в чёрный список. Вы не 
                                    можете его добавить в беседу.';
                                            $dataOut['errors'] = $errors;
                                            return $dataOut;
                                        }
                                    }
                                }
                            }

                            // Создаем беседу
                            $res = $db->createConversation($data['auth_data']['id'], $name, $membersParts,
                                $imageBase64);
                            if ($res != null) {
                                return [
                                    'status' => 'OK',
                                    'conversation_id' => $res
                                ];
                            } else {
                                $errors[] = 'Ошибка создания беседы.';
                            }
                        } else {
                            $errors[] = 'Укажите хотя-бы одного участника.';
                        }
                    } else {
                        $errors[] = 'Укажите хотя-бы одного участника.';
                    }
                } else {
                    $errors[] = 'Название беседы должно содержать от ' . ConfigDataDB::MIN_SYMBOLS_CONVERSATION_NAME . ' до ' . ConfigDataDB::LIMIT_SYMBOLS_CONVERSATION_NAME . ' символов.';
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

    // Получить сообщения беседы
    public function actionGetconversation($id, $limit = 10, $offset = 0)
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();

        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                if (isset($id)) {
                    if (!empty($id)) {
                        if ($db->checkExistsConversation($id)) {
                            if ($db->checkConversationUser($data['auth_data']['id'], $id)) {
                                $messagesIds = $db->getMessagesIdsOfConversationLimitAndOffset($id, $limit, $offset);
                                $conv = $db->getConversationAsObject($_GET['id']);

                                $dataOut['id'] = $conv->id;
                                $dataOut['account_author_id'] = $conv->account_author_id;
                                $dataOut['name'] = $conv->name;
                                $dataOut['created'] = date_create($conv->created)->Format('Y-m-d H:i');
                                $dataOut['photo_path'] = ControlsAPI::HOST . $conv->photo_path;

                                // Определяем кем является данный пользователь (создатель/участник)
                                $dataOut['your_status'] = "member";
                                if ($conv->account_author_id == $data['auth_data']['id']) {
                                    $dataOut['your_status'] = "creator";
                                }

                                foreach ($messagesIds['result'] as $key => $value) {
                                    $msg = $db->getMessageAsArray($value);
                                    $senderAcc = $db->getAccountAsArray($msg['sender_id']);
                                    $senderPerInfo = $db->getPersonalInfoById($senderAcc['personal_info_id']);

                                    // Получаем файлы
                                    $filesIds = explode('|', $msg['files']);
                                    $filesArray = array();
                                    for ($i = 0; $i < count($filesIds); $i++) {
                                        if (!empty($filesIds[$i])) {
                                            $fileArr = $db->getFileAsArray($filesIds[$i]);
                                            if(!empty($fileArr)) {
                                                $filesArray[] = $db->getFileUserAsArray($msg['sender_id'],
                                                    $fileArr['file_name']);
                                            }
                                        }
                                    }

                                    $dataOut['messages'][] = [
                                        'sender_id' => $msg['sender_id'],
                                        'sender_photo' => ControlsAPI::HOST . $senderPerInfo->photo_path,
                                        'sender_first_name' => $senderAcc['first_name'],
                                        'sender_last_name' => $senderAcc['last_name'],
                                        'message_text' => $msg['text'],
                                        'message_photo_path' => $msg['photo_path'],
                                        'files' => $filesArray,
                                        'videoYT' => $msg['videoYT'],
                                        'date_send' => date_create($msg['date_send'])->Format('Y-m-d H:i'),
                                        'viewed' => $msg['viewed']
                                    ];
                                }

                                $dataOut['is_there_more'] = $messagesIds['is_there_more'];
                                $db->readAllMessagesConversation($id, $data['auth_data']['id']);

                                return $dataOut;
                            } else {
                                $errors[] = 'Вы не состоите в этой беседе.';
                            }
                        } else {
                            $errors[] = 'Такой беседы несуществует.';
                        }
                    } else {
                        $errors[] = 'Не указан id беседы.';
                    }
                } else {
                    $errors[] = 'Не указан id беседы.';
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

    // Получить сообщения диалога
    public function actionGetdialog($id, $limit = 10, $offset = 0)
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();

        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                if (isset($id)) {
                    if (!empty($id)) {
                        if ($db->checkExistsDialog($id)) {
                            if ($db->checkDialogUser($data['auth_data']['id'], $id)) {
                                $messagesIds = $db->getMessagesIdsOfDialogLimitAndOffset($id, $limit, $offset);
                                $dlg = $db->getDialogById($_GET['id']);
                                // Получаем массив аккаунта собеседника
                                if ($data['auth_data']['id'] == $dlg->to_id) {
                                    $recipient = $db->getAccountAsArray($dlg->from_id);

                                } else {
                                    if ($data['auth_data']['id'] == $dlg->from_id) {
                                        $recipient = $db->getAccountAsArray($dlg->to_id);
                                    }
                                }

                                $recipientPersonalInfo = $db->getPersonalInfoById($recipient['personal_info_id']);

                                $dataOut['favorite'] = $db->checkExistsFavorite($data['auth_data']['id'],
                                    $recipient['id']);
                                $dataOut['black_list'] = $db->checkBlackListUser($data['auth_data']['id'],
                                    $recipient['id']);
                                $dataOut['recipient_id'] = $recipient['id'];
                                $dataOut['recipient_status_visit'] = $db->getStatusVisit($recipient['id']);
                                $dataOut['recipient_photo_path'] = ControlsAPI::HOST . $recipientPersonalInfo->photo_path;
                                $dataOut['recipient_first_name'] = $recipient['first_name'];
                                $dataOut['recipient_last_name'] = $recipient['last_name'];

                                foreach ($messagesIds['result'] as $key => $value) {
                                    $msg = $db->getMessageAsArray($value);
                                    $senderAcc = $db->getAccountAsArray($msg['sender_id']);
                                    $senderPerInfo = $db->getPersonalInfoById($senderAcc['personal_info_id']);

                                    // Получаем файлы
                                    $filesIds = explode('|', $msg['files']);
                                    $filesArray = array();
                                    for ($i = 0; $i < count($filesIds); $i++) {
                                        if (!empty($filesIds[$i])) {
                                            $fileArr = $db->getFileAsArray($filesIds[$i]);
                                            if(!empty($fileArr)) {
                                                $filesArray[] = $db->getFileUserAsArray($msg['sender_id'],
                                                    $fileArr['file_name']);
                                            }
                                        }
                                    }

                                    $dataOut['messages'][] = [
                                        'sender_id' => $msg['sender_id'],
                                        'sender_photo' => ControlsAPI::HOST . $senderPerInfo->photo_path,
                                        'sender_first_name' => $senderAcc['first_name'],
                                        'sender_last_name' => $senderAcc['last_name'],
                                        'message_text' => $msg['text'],
                                        'message_photo_path' => $msg['photo_path'],
                                        'files' => $filesArray,
                                        'videoYT' => $msg['videoYT'],
                                        'date_send' => date_create($msg['date_send'])->Format('Y-m-d H:i'),
                                        'viewed' => $msg['viewed']
                                    ];
                                }

                                $dataOut['is_there_more'] = $messagesIds['is_there_more'];
                                $db->readAllMessagesDialog($id, $data['auth_data']['id']);

                                return $dataOut;
                            } else {
                                $errors[] = 'Диалог не принадлежит вам.';
                            }
                        } else {
                            $errors[] = 'Такого диалога несуществует.';
                        }
                    } else {
                        $errors[] = 'Не указан id диалога.';
                    }
                } else {
                    $errors[] = 'Не указан id диалога.';
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

    // Получить новые (непрочитанные) сообщения диалога
    public function actionGetnewmessagesfromdialog($id)
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();

        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                if (isset($id)) {
                    if (!empty($id)) {
                        if ($db->checkExistsDialog($id)) {
                            if ($db->checkDialogUser($data['auth_data']['id'], $id)) {
                                $messages = $db->getNewMessagesOfDialog($id, $data['auth_data']['id']);
                                $dataOut['new_messages'] = $messages;
                                $dlg = $db->getDialogById($id);
                                // Получаем массив аккаунта собеседника
                                if ($data['auth_data']['id'] == $dlg->to_id) {
                                    $recipient = $db->getAccountAsArray($dlg->from_id);

                                } else {
                                    if ($data['auth_data']['id'] == $dlg->from_id) {
                                        $recipient = $db->getAccountAsArray($dlg->to_id);
                                    }
                                }

                                $recipientPersonalInfo = $db->getPersonalInfoById($recipient['personal_info_id']);

                                $dataOut['favorite'] = $db->checkExistsFavorite($data['auth_data']['id'],
                                    $recipient['id']);
                                $dataOut['black_list'] = $db->checkBlackListUser($data['auth_data']['id'],
                                    $recipient['id']);
                                $dataOut['recipient_id'] = $recipient['id'];
                                $dataOut['recipient_status_visit'] = $db->getStatusVisit($recipient['id']);
                                $dataOut['recipient_photo_path'] = ControlsAPI::HOST . $recipientPersonalInfo->photo_path;
                                $dataOut['recipient_first_name'] = $recipient['first_name'];
                                $dataOut['recipient_last_name'] = $recipient['last_name'];

                                $db->readAllMessagesDialog($id, $data['auth_data']['id']);

                                return $dataOut;
                            } else {
                                $errors[] = 'Диалог не принадлежит вам.';
                            }
                        } else {
                            $errors[] = 'Такого диалога несуществует.';
                        }
                    } else {
                        $errors[] = 'Не указан id диалога.';
                    }
                } else {
                    $errors[] = 'Не указан id диалога.';
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

    // Получить новые (непрочитанные) сообщения беседы
    public function actionGetnewmessagesfromconversation($id)
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();

        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                if (isset($id)) {
                    if (!empty($id)) {
                        if ($db->checkExistsConversation($id)) {
                            if ($db->checkConversationUser($data['auth_data']['id'], $id)) {
                                $messages = $db->getNewMessagesOfConversation($id, $data['auth_data']['id']);
                                $dataOut['new_messages'] = $messages;

                                $db->readAllMessagesConversation($id, $data['auth_data']['id']);

                                return $dataOut;
                            } else {
                                $errors[] = 'Вас нет в этой беседе.';
                            }
                        } else {
                            $errors[] = 'Такой беседы несуществует.';
                        }
                    } else {
                        $errors[] = 'Не указан id беседы.';
                    }
                } else {
                    $errors[] = 'Не указан id беседы.';
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

    // Удалить беседу
    public function actionRemoveconversation()
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();

        $conversationId = Yii::$app->request->post('id');

        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                if (!empty($conversationId)) {
                    if ($db->checkExistsConversation($conversationId)) {
                        if ($db->checkAuthorConversation($data['auth_data']['id'], $conversationId)) {
                            if ($db->removeConversation($conversationId)) {
                                return ['status' => 'OK'];
                            } else {
                                $errors[] = 'Ошибка удаления беседы. Проверьте отправляемые данные.';
                            }
                        } else {
                            $errors[] = 'Нет прав на удаление беседы.';
                        }
                    } else {
                        $errors[] = 'Такой беседы несуществует.';
                    }
                } else {
                    $errors[] = 'Не указан id беседы.';
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

    // Переименовать беседу
    public function actionRenameconversation()
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();

        $id = Yii::$app->request->post('id');
        $name = htmlspecialchars(Yii::$app->request->post('name'));

        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                if (!empty($id)) {
                    if ($db->checkExistsConversation($id)) {
                        if ($db->checkAuthorConversation($data['auth_data']['id'], $id)) {
                            if (iconv_strlen($name) >= ConfigDataDB::MIN_SYMBOLS_CONVERSATION_NAME &&
                                iconv_strlen($name) <= ConfigDataDB::LIMIT_SYMBOLS_CONVERSATION_NAME) {
                                if ($db->renameConversation($id, $name)) {
                                    return ['status' => 'OK'];
                                } else {
                                    $errors[] = 'Ошибка переименования беседы. Проверьте отправляемые данные.';
                                }
                            } else {
                                $errors[] = 'Название беседы должно содержать от ' . ConfigDataDB::MIN_SYMBOLS_CONVERSATION_NAME . ' до ' . ConfigDataDB::LIMIT_SYMBOLS_CONVERSATION_NAME . ' символов.';
                            }
                        } else {
                            $errors[] = 'Нет прав на переименование беседы.';
                        }
                    } else {
                        $errors[] = 'Такой беседы несуществует.';
                    }
                } else {
                    $errors[] = 'Не указан id беседы.';
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

    // Сменить изображение беседы
    public function actionRefreshphotoconversation()
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();

        $id = Yii::$app->request->post('id');
        $photoBase64 = Yii::$app->request->post('photoBase64');

        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                if (!empty($id)) {
                    if ($db->checkExistsConversation($id)) {
                        if ($db->checkAuthorConversation($data['auth_data']['id'], $id)) {
                            $type = explode(';', $photoBase64)[0];
                            $type = explode('/', $type)[1];
                            if ($db->checkFileImage($type)) {
                                if ($db->refreshPhotoConversation($id, $photoBase64)) {
                                    return ['status' => 'OK'];
                                } else {
                                    $errors[] = 'Ошибка обновления фото беседы. Проверьте отправляемые данные.';
                                }
                            } else {
                                $errors[] = "Неверный формат файла. Допустимые форматы: " . implode(", ",
                                        ConfigDataDB::ALLOWS_IMAGE_EXTENSION);
                            }
                        } else {
                            $errors[] = 'Нет прав на переименование беседы.';
                        }
                    } else {
                        $errors[] = 'Такой беседы несуществует.';
                    }
                } else {
                    $errors[] = 'Не указан id беседы.';
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

    // Покинуть беседу
    public function actionLeaveconversation()
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();

        $id = Yii::$app->request->post('id');

        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                if (!empty($id)) {
                    if ($db->checkExistsConversation($id)) {
                        if ($db->checkConversationUser($data['auth_data']['id'], $id)) {
                            $conv = $db->getConversationAsObject($id);
                            if ($conv->account_author_id != $data['auth_data']['id']) {
                                if ($db->toLeaveOfConversation($id, $data['auth_data']['id'])) {
                                    return ['status' => 'OK'];
                                } else {
                                    $errors[] = 'Ошибка выхода из беседы.';
                                }
                            } else {
                                $errors[] = 'Вы не можете выйти со своей же беседы.';
                            }
                        } else {
                            $errors[] = 'Вы не состоите в этой беседе.';
                        }
                    } else {
                        $errors[] = 'Такой беседы несуществует.';
                    }
                } else {
                    $errors[] = 'Не указан id беседы.';
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

    // Получить массив участников беседы
    public function actionGetmembersofconversation($id)
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();

        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                if (!empty($id)) {
                    if ($db->checkExistsConversation($id)) {
                        if ($db->checkConversationUser($data['auth_data']['id'], $id)) {
                            $members = $db->getMembersOfConversationAsArrayIds($id);
                            $membersRes = array();
                            foreach ($members as $member) {
                                $acc = $db->getAccountAsArray($member);
                                if (null != $acc) {
                                    $perInfo = $db->getPersonalInfoByIdAsArray($acc['personal_info_id']);
                                    if (null != $perInfo) {
                                        $conv = $db->getConversationAsObject($id);
                                        // Если это создатель, то делаем пометку
                                        if($acc['id']==$conv->account_author_id) {
                                            $acc['status_member'] = 'creator';
                                        }
                                        // Формируем полную ссылку к фото (аватарке)
                                        $perInfo['photo_path'] = DBHelper::HOST . $perInfo['photo_path'];
                                        // Удаляем id персональной информации (чтобы не было конфликта при слиянии массивов
                                        unset($perInfo['id']);
                                        // Получаем статус посещения
                                        $perInfo['status_visit'] = $db->getStatusVisit($acc['id']);
                                        $membersRes[] = array_merge($acc, $perInfo);
                                    }
                                }
                            }
                            return ['members' => $membersRes];
                        } else {
                            $errors[] = 'Вы не состоите в этой беседе.';
                        }
                    } else {
                        $errors[] = 'Такой беседы несуществует.';
                    }
                } else {
                    $errors[] = 'Не указан id беседы.';
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

    // Изменить список участников беседы
    public function actionChangemembersconversation()
    {
        $db = new DBHelper();
        $data = ControlsAPI::checkAuth();
        $dataOut = array();
        $errors = array();

        $conversationId = Yii::$app->request->post('conversationId');
        $members = Yii::$app->request->post('members');

        if (!empty($data['auth_data'])) {
            if (!$db->checkBlockAccount($data['auth_data']['id'])) {
                if ($db->checkExistsConversation($conversationId)) {
                    if ($db->checkConversationUser($data['auth_data']['id'], $conversationId)) {
                        if ($db->checkAuthorConversation($data['auth_data']['id'], $conversationId)) {
                            // Получаем и проверяем на валидность участников беседы
                            if (!empty($members)) {

                                // Парсим строку с участниками на массив
                                $members = htmlspecialchars($members);
                                $membersParts = explode("|", $members);
                                unset($membersParts[count($membersParts) - 1]); // Удаляем последний элемент (пустой)
                                $countSelectMembers = count($membersParts);

                                // Проверяем кол-во участников
                                if ($countSelectMembers > ConfigDataDB::LIMIT_SELECTED_CONVERSATION_MEMBERS &&
                                    $countSelectMembers <= 0) {
                                    $errors[] = 'Укажите минимум одного участника, а также не более '
                                        . ConfigDataDB::LIMIT_SELECTED_CONVERSATION_MEMBERS;
                                    $dataOut['errors'] = $errors;
                                    return $dataOut;
                                }

                                // Проверяем участников
                                foreach ($membersParts as $key => $value) {
                                    if (!empty($value)) {
                                        $acc = $db->getAccountAsArray($value);
                                        // Если пользователя не существует, то ошибка
                                        if ($acc == null) {
                                            $errors[] = 'Не найден пользователь с id ' . $value;
                                            $dataOut['errors'] = $errors;
                                            return $dataOut;
                                        } else {
                                            // Если пользователь не добавил создателя беседы в чс
                                            if (!$db->checkBlackListUser($acc['id'], $data['auth_data']['id'])) {
                                                // Если создатель беседы не добавили участника в чс
                                                if (!$db->checkBlackListUser($data['auth_data']['id'], $acc['id'])) {
                                                    // Если элемент $membersParts дошёл до этой строки, значит всё ОК
                                                } else {
                                                    $errors[] = 'Пользователь с id ' . $value . ' у вас в чёрном списке. Вы не 
                                        можете его добавить в беседу.';
                                                    $dataOut['errors'] = $errors;
                                                    return $dataOut;
                                                }
                                            } else {
                                                $errors[] = 'Пользователь с id ' . $value . ' добавил вас в чёрный список. Вы не 
                                    можете его добавить в беседу.';
                                                $dataOut['errors'] = $errors;
                                                return $dataOut;
                                            }
                                        }
                                    }
                                }

                                // Добавляем участников в беседу
                                $res = $db->changeMembersConversation($conversationId, $membersParts);
                                if (null != $res) {
                                    return [
                                        'status' => 'OK',
                                        'conversation_id' => $res
                                    ];
                                } else {
                                    $errors[] = 'Ошибка редактирования списка участников беседы.';
                                }
                            } else {
                                $errors[] = 'Укажите хотя-бы одного участника.';
                            }
                        } else {
                            $errors[] = 'Нет прав.';
                        }
                    } else {
                        $errors[] = 'Вы не состоите в этой беседе.';
                    }
                } else {
                    $errors[] = 'Такой беседы не существует.';
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