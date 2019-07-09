<?php

namespace app\controllers;

use app\config\ConfigAPI;
use app\services\Utils;
use Yii;

class NewsController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $data = array();
        if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $ut = new Utils();
            $authData = $ut->checkAuth();
            $data = $authData;
            $this->view->params['data'] = $authData;

            $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/news/getnews?limit=10&offset=0&ip="
                . $_SERVER['REMOTE_ADDR']);
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

            if (isset($dataResp['news'])) {
                for ($i = 0; $i < count($dataResp['news']); $i++) {
                    if ($dataResp['news'][$i]['image_path'] != null) {
                        $dataResp['news'][$i]['image_path'] = 'data:image/jpeg;base64,' .
                            base64_encode(file_get_contents($dataResp['news'][$i]['image_path']));
                    }
                }
            }

            $data['news'] = $dataResp['news'];
            $data['is_there_more'] = $dataResp['is_there_more'];
        } else {
            $_SESSION['errors'] = ['' => 'Необходима авторизация.'];
        }

        return $this->render('index', $data);
    }

    public function actionAddnewspage()
    {
        if (isset($_COOKIE['email']) && $_COOKIE['password']) {
            $ut = new Utils();
            $this->view->params['data'] = $ut->checkAuth();
        } else {
            $_SESSION['errors'] = ['' => 'Необходима авторизация.'];
        }

        return $this->render('addnewspage');
    }

    public function actionAdd()
    {
        $errors = array();
        $data['refresh'] = true;
        if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $ut = new Utils();
            $authData = $ut->checkAuth();
            $this->view->params['data'] = $authData;

            if ($authData['auth_data']['role'] == "admin") {
                if (isset(Yii::$app->request->post()['theme']) && isset(Yii::$app->request->post()['description'])) {
                    if (!empty(Yii::$app->request->post()['theme']) && !empty(Yii::$app->request->post()['description'])) {
                        if (iconv_strlen(Yii::$app->request->post()['theme']) < 1500 &&
                            iconv_strlen(Yii::$app->request->post()['description']) < 5000) {
                            // Проверяем дату события и место события (если есть)
                            $dateEvent = null;
                            $eventDescription = null;
                            if (isset(Yii::$app->request->post()['date_event']) &&
                                isset(Yii::$app->request->post()['event_description'])) {
                                if (!empty(Yii::$app->request->post()['date_event'])) {
                                    if (!empty(Yii::$app->request->post()['event_description'])) {
                                        if (iconv_strlen(Yii::$app->request->post()['event_description']) < 200) {

                                            $eventDescription = Yii::$app->request->post()['event_description'];
                                        } else {
                                            $errors[] = 'Место события не должно превышать 200 символов.';
                                            $_SESSION['errors'] = $errors;
                                            return $this->render('addnewspage');
                                        }
                                    }
                                    $dateEvent = Yii::$app->request->post()['date_event'];
                                }
                                if (empty(Yii::$app->request->post()['date_event']) &&
                                    !empty(Yii::$app->request->post()['event_description'])) {
                                    $errors[] = 'Укажите дату события.';
                                    $_SESSION['errors'] = $errors;
                                    return $this->render('addnewspage');
                                }
                            }

                            // Если новость с картинкой
                            $base64Encode = null;
                            if (isset($_FILES["image"])) {
                                if (!empty($_FILES["image"]['name'])) {
                                    if (Utils::checkFileImage($_FILES["image"]["name"])) {
                                        move_uploaded_file($_FILES["image"]["tmp_name"], $_FILES["image"]["name"]);

                                        $binString = file_get_contents($_FILES["image"]["name"]);
                                        $hexString = base64_encode($binString);
                                        $type = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
                                        $base64Encode = 'data:image/' . $type . ';base64,' . $hexString;
                                        unlink($_FILES["image"]["name"]);
                                    } else {
                                        $errors['image'] = "Ошибка загрузки изображения. Убедитесь что файл является 
                                        изображением. Допустимые форматы: " .
                                            implode(", ", Utils::ALLOW_IMAGE_TYPES);
                                    }
                                }
                            }

                            // Проверяем ссылку на видео
                            $videoLink = "";
                            if (isset(Yii::$app->request->post()['video_link'])) {
                                if (!empty(isset(Yii::$app->request->post()['video_link']))) {
                                    $videoLink = Yii::$app->request->post()['video_link'];
                                }
                            }

                            // Проверяем опрос
                            $poll = null;
                            if (isset(Yii::$app->request->post()['answerChoice'])) {
                                if (isset(Yii::$app->request->post()['themePoll'])) {
                                    if (isset(Yii::$app->request->post()['addPollAnon'])) {
                                        if (!empty(Yii::$app->request->post()['addPollAnon'])) {
                                            if (!empty(Yii::$app->request->post()['themePoll'])) {
                                                if (count(Yii::$app->request->post()['answerChoice']) > 0) {
                                                    foreach (Yii::$app->request->post()['answerChoice'] as $key => $value) {
                                                        if (empty($value)) {
                                                            $errors['poll'] = "Варианты ответов не должны быть пустыми!";
                                                            break;
                                                        }
                                                    }
                                                } else {
                                                    $errors['poll'] = "Ответов на опрос должно быть больше нуля.";
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            // Отправляем новость
                            if (!isset($errors['image']) || !isset($errors['poll'])) {
                                $theme = Yii::$app->request->post()['theme'];
                                $description = Yii::$app->request->post()['description'];
                                $pollTheme = Yii::$app->request->post()['themePoll'];
                                $pollAnswers = implode('~@g', Yii::$app->request->post()['answerChoice']);
                                $pollAnon = (isset(Yii::$app->request->post()['addPollAnon'])) ? 'true' : 'false';

                                $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/news/add?ip="
                                    . $_SERVER['REMOTE_ADDR']);
                                curl_setopt($ch, CURLOPT_POST, 1);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                    'Cookie: email=' . $_COOKIE['email'] . '; password=' . $_COOKIE['password'] . ''
                                ));
                                curl_setopt($ch, CURLOPT_POSTFIELDS,
                                    array(
                                        'theme' => $theme,
                                        'description' => $description,
                                        'image' => (!empty($base64Encode)) ? $base64Encode : null,
                                        'video_link' => $videoLink,
                                        'event_date' => $dateEvent,
                                        'event_description' => $eventDescription,
                                        'pollTheme' => $pollTheme,
                                        'pollAnswers' => $pollAnswers,
                                        'pollAnon' => $pollAnon
                                    ));
                                $response = curl_exec($ch);
                                $dataResult = json_decode($response, true);
                                curl_close($ch);

                                if (isset($dataResult['errors'])) {
                                    $errors = $dataResult['errors'];
                                }
                                if (isset($dataResult['status'])) {
                                    if ($dataResult['status'] == "OK") {
                                        return $this->render('index', $data);
                                    }
                                }
                            }
                        } else {
                            $errors[] = 'Длина темы или описания больше допустимого значения. Допускается: 
                            Тема (до 1500 символов) и Описание (до 5000 символов).';
                        }
                    } else {
                        $errors[] = 'Ошибка введенных данных. Проверьте что тема и описание задано.';
                    }
                } else {
                    $errors[] = 'Ошибка введенных данных. Проверьте что тема и описание задано.';
                }
            } else {
                $errors[] = "Добавлять новости разрешено только администрации.";
            }
        } else {
            $errors[] = 'Необходима авторизация.';
        }

        if (count($errors) > 0) {
            $_SESSION['errors'] = $errors;
        }

        return $this->render('addnewspage');
    }

    public function actionRemovenews()
    {
        $errors = array();
        $data['refresh'] = true;
        if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
            $ut = new Utils();
            $authData = $ut->checkAuth();
            $this->view->params['data'] = $authData;

            if ($authData['auth_data']['role'] == "admin") {
                if (isset($_GET['id'])) {
                    if (!empty($_GET['id'])) {
                        $ch = curl_init("http://" . ConfigAPI::HOST_API . "/v1/news/remove?ip=" . $_SERVER['REMOTE_ADDR']);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            'Cookie: email=' . $_COOKIE['email'] . '; password=' . $_COOKIE['password'] . ''
                        ));
                        curl_setopt($ch, CURLOPT_POSTFIELDS,
                            array(
                                'id' => $_GET['id'],
                            ));
                        $response = curl_exec($ch);
                        $dataResult = json_decode($response, true);
                        curl_close($ch);

                        if (isset($dataResult['errors'])) {
                            $errors = $dataResult['errors'];
                        }

                    } else {
                        $errors[] = "ID записи не найден.";
                    }
                } else {
                    $errors[] = "ID записи не найден.";
                }
            } else {
                $errors[] = "Удалять новости разрешено только администрации.";
            }
        } else {
            $errors[] = "Необходима авторизация.";
        }

        if (count($errors) > 0) {
            $_SESSION['errors'] = $errors;
        }

        return $this->render('index', $data);
    }

}
