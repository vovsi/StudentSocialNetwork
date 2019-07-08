<?php

namespace app\controllers;

use app\config\ConfigAPI;
use app\services\Utils;
use Yii;

class MessagesController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $data = array();
        if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $ut = new Utils();
            $authData = $ut->checkAuth();
            if (!empty($authData['auth_data'])) {
                $data = $authData;
                $this->view->params['data'] = $authData;

                // Получаем диалоги (10 шт.)
                $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/messages/getdialogs?limit=10&offset=0&ip="
                    .$_SERVER['REMOTE_ADDR']);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Cookie: email=' . $_COOKIE['email'] . '; password=' . $_COOKIE['password'] . ''
                ));
                $response = curl_exec($ch);
                $dataResp = json_decode($response, true);
                curl_close($ch);

                if (isset($dataResp['errors'])) {
                    if (!empty($dataResp['errors'])) {
                        $_SESSION['errors'] = $dataResp['errors'];
                    }
                }

                if (isset($dataResp['dialogs'])) {
                    for ($i = 0; $i < count($dataResp['dialogs']); $i++) {
                        $dataResp['dialogs'][$i]['interlocutor_image'] = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($dataResp['dialogs'][$i]['interlocutor_image']));
                        if ($dataResp['dialogs'][$i]['last_message_photo'] != null) {
                            $dataResp['dialogs'][$i]['last_message_photo'] = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($dataResp['dialogs'][$i]['last_message_photo']));
                        }
                    }
                }
                $data['dialogs'] = $dataResp['dialogs'];
                $data['is_there_more_dialogs'] = $dataResp['is_there_more'];

                // Получаем беседы (10 шт.)
                $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/messages/getconversations?limit=10&offset=0&ip="
                    .$_SERVER['REMOTE_ADDR']);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Cookie: email=' . $_COOKIE['email'] . '; password=' . $_COOKIE['password'] . ''
                ));
                $response = curl_exec($ch);
                $dataResp = json_decode($response, true);
                curl_close($ch);

                if (isset($dataResp['errors'])) {
                    if (!empty($dataResp['errors'])) {
                        $_SESSION['errors'] = $dataResp['errors'];
                    }
                }

                if (isset($dataResp['conversations'])) {
                    for ($i = 0; $i < count($dataResp['conversations']); $i++) {
                        $dataResp['conversations'][$i]['conversation_photo'] = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($dataResp['conversations'][$i]['conversation_photo']));
                        if ($dataResp['conversations'][$i]['last_message_photo'] != null) {
                            $dataResp['conversations'][$i]['last_message_photo'] = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($dataResp['conversations'][$i]['last_message_photo']));
                        }
                    }
                }
                $data['conversations'] = $dataResp['conversations'];
                $data['is_there_more_conversations'] = $dataResp['is_there_more'];
            } else {
                $_SESSION['errors'] = ['' => 'Необходима авторизация.'];
            }
        } else {
            $_SESSION['errors'] = ['' => 'Необходима авторизация.'];
        }
        return $this->render('index', $data);
    }

    public function actionSend()
    {
        $data = array();
        if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $ut = new Utils();
            $authData = $ut->checkAuth();
            if (!empty($authData['auth_data'])) {
                $data = $authData;
                $data['refresh'] = true;
                $this->view->params['data'] = $authData;

                if (isset(Yii::$app->request->post()['account_to_id'])) {
                    if (!empty(Yii::$app->request->post()['account_to_id'])) {

                        $accountToId = Yii::$app->request->post()['account_to_id'];
                        $message = null;
                        if (isset(Yii::$app->request->post()['message'])) {
                            if (!empty(Yii::$app->request->post()['message'])) {
                                if (iconv_strlen(Yii::$app->request->post()['message']) < 3000) {
                                    $message = Yii::$app->request->post()['message'];
                                } else {
                                    $_SESSION['errors'] = ['' => 'Длина сообщения больше допустимого значения. Допускается: 3000 символов.'];
                                    return $this->render('index', $data);
                                }
                            }
                        }

                        // Если сообщение с картинкой
                        $base64Encode = null;
                        if (isset($_FILES["att_photo_newMessage"])) {
                            if (!empty($_FILES["att_photo_newMessage"]['name'])) {
                                if (Utils::checkFileImage($_FILES["att_photo_newMessage"]["name"])) {
                                    move_uploaded_file($_FILES["att_photo_newMessage"]["tmp_name"],
                                        $_FILES["att_photo_newMessage"]["name"]);

                                    $binString = file_get_contents($_FILES["att_photo_newMessage"]["name"]);
                                    $hexString = base64_encode($binString);
                                    $type = pathinfo($_FILES["att_photo_newMessage"]["name"], PATHINFO_EXTENSION);
                                    $base64Encode = 'data:image/' . $type . ';base64,' . $hexString;
                                    unlink($_FILES["att_photo_newMessage"]["name"]);
                                } else {
                                    $_SESSION['errors']['file'] = "Ошибка загрузки изображения. Убедитесь что файл является изображением. Допустимые форматы: gif, png, jpg, jpeg";
                                }
                            }
                        }

                        // Send...
                        if (!isset($_SESSION['errors']['file'])) {
                            $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/messages/sendtodialog?ip="
                                .$_SERVER['REMOTE_ADDR']);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                'Cookie: email=' . $_COOKIE['email'] . '; password=' . $_COOKIE['password'] . ''
                            ));
                            curl_setopt($ch, CURLOPT_POSTFIELDS, //тут переменные которые будут переданы методом POST
                                array(
                                    'account_to_id' => $accountToId,
                                    'message' => $message,
                                    'image' => (!empty($base64Encode)) ? $base64Encode : null,
                                    'files' => null,
                                    'videoYT' => null
                                ));
                            $response = curl_exec($ch);
                            $dataResult = json_decode($response, true);
                            curl_close($ch);

                            if (isset($dataResult['errors'])) {
                                if (!empty($dataResult['errors'])) {
                                    $_SESSION['errors'] = $dataResult['errors'];
                                }
                            }

                            if (isset($dataResult['status']) && isset($dataResult['dialog_id'])) {
                                if ($dataResult['status'] == "OK") {
                                    if (!empty($dataResult['dialog_id'])) {
                                        // Сообщение успешно отправилось
                                        header("Location: /messages/dialog?id=" . $dataResult['dialog_id']);
                                        exit;
                                    }
                                }
                            }
                        }
                    } else {
                        $_SESSION['errors'] = ['' => 'Ошибка введенных данных. Проверьте что id задано.'];
                    }
                } else {
                    $_SESSION['errors'] = ['' => 'Ошибка введенных данных. Проверьте что id задано.'];
                }
            } else {
                $_SESSION['errors'] = ['' => 'Необходима авторизация.'];
            }
        } else {
            $_SESSION['errors'] = ['' => 'Необходима авторизация.'];
        }

        return $this->render('index', $data);
    }

    public function actionDialog()
    {
        $data = array();
        if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $ut = new Utils();
            $authData = $ut->checkAuth();
            if (!empty($authData['auth_data'])) {
                $data = $authData;
                $this->view->params['data'] = $authData;

                if (isset($_GET['id'])) {
                    if (!empty($_GET['id'])) {
                        $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/messages/getdialog?id="
                            . $_GET['id'] . "&limit=10&offset=0&ip=".$_SERVER['REMOTE_ADDR']);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            'Cookie: email=' . $_COOKIE['email'] . '; password=' . $_COOKIE['password'] . ''
                        ));
                        $response = curl_exec($ch);
                        $dataResp = json_decode($response, true);
                        curl_close($ch);

                        if (isset($dataResp['errors'])) {
                            if (!empty($dataResp['errors'])) {
                                $_SESSION['errors'] = $dataResp['errors'];
                            }
                        }

                        $dataResp['recipient_photo_path'] = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($dataResp['recipient_photo_path']));
                        if (isset($dataResp['messages'])) {
                            for ($i = 0; $i < count($dataResp['messages']); $i++) {
                                $dataResp['messages'][$i]['sender_photo'] = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($dataResp['messages'][$i]['sender_photo']));
                                if ($dataResp['messages'][$i]['message_photo_path'] != null) {
                                    $dataResp['messages'][$i]['message_photo_path'] = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($dataResp['messages'][$i]['message_photo_path']));
                                }
                            }
                        }

                        $data = $dataResp;
                    } else {
                        $_SESSION['errors'] = ['' => 'Не задан id диалога.'];
                    }
                } else {
                    $_SESSION['errors'] = ['' => 'Не задан id диалога.'];
                }
            } else {
                $_SESSION['errors'] = ['' => 'Необходима авторизация.'];
            }
        } else {
            $_SESSION['errors'] = ['' => 'Необходима авторизация.'];
        }
        return $this->render('dialog', $data);
    }

    public function actionConversation()
    {
        $data = array();
        if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $ut = new Utils();
            $authData = $ut->checkAuth();
            if (!empty($authData['auth_data'])) {
                $data = $authData;
                $this->view->params['data'] = $authData;

                if (isset($_GET['id'])) {
                    if (!empty($_GET['id'])) {
                        $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/messages/getconversation?id="
                            . $_GET['id'] . "&limit=10&offset=0&ip=".$_SERVER['REMOTE_ADDR']);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            'Cookie: email=' . $_COOKIE['email'] . '; password=' . $_COOKIE['password'] . ''
                        ));
                        $response = curl_exec($ch);
                        $dataResp = json_decode($response, true);
                        curl_close($ch);

                        if (isset($dataResp['errors'])) {
                            if (!empty($dataResp['errors'])) {
                                $_SESSION['errors'] = $dataResp['errors'];
                            }
                        }

                        $dataResp['photo_path'] = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($dataResp['photo_path']));
                        if (isset($dataResp['messages'])) {
                            for ($i = 0; $i < count($dataResp['messages']); $i++) {
                                $dataResp['messages'][$i]['sender_photo'] = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($dataResp['messages'][$i]['sender_photo']));
                                if ($dataResp['messages'][$i]['message_photo_path'] != null) {
                                    $dataResp['messages'][$i]['message_photo_path'] = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($dataResp['messages'][$i]['message_photo_path']));
                                }
                            }
                        }

                        $data = $dataResp;
                    } else {
                        $_SESSION['errors'] = ['' => 'Не задан id беседы.'];
                    }
                } else {
                    $_SESSION['errors'] = ['' => 'Не задан id беседы.'];
                }
                $data['auth_data'] = $authData['auth_data'];
            } else {
                $_SESSION['errors'] = ['' => 'Необходима авторизация.'];
            }
        } else {
            $_SESSION['errors'] = ['' => 'Необходима авторизация.'];
        }
        return $this->render('conversation', $data);
    }
}
