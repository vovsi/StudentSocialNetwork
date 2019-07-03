<?php

use yii\helpers\Url;

if (isset($refresh)) {
    if ($refresh) {
        header('Location: /' . $profile['id']);
        exit();
    }
}

// Получить читабельный вид размера файла (байты -> кбайты/мбайты/гбайты...)
function formatFileSize($bytes, $precision = 2)
{
    $units = array('байт', 'Кбайт', 'Мбайт', 'Гбайт', 'Тбайт');
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

?>

<link rel="stylesheet" href="<?php echo Url::to('@web/css/general/emotions/jquery.emotions.css'); ?>">
<title>Профиль</title>

<?php
if (isset($errors)) {
    if (!empty($errors)) {
        echo "<ul>";
        foreach ($errors as $key => $value) {
            echo "<li>$value</li>";
        }
        echo "</ul>";
    }
}
?>
<link rel="stylesheet" href="<?php echo Url::to('@web/css/views/main/profile.css'); ?>">
<input id="myId" type="hidden" value="<?php echo $auth_data['id']; ?>"/>
<div class="text-center tab-content border">
    <div class="jumbotron">
        <h3 style="color: #737570"><?php echo $profile['first_name'] . ' ' . $profile['patronymic'] . ' ' . $profile['last_name']; ?><?php if ($profile['status_visit'] == "online") {
                echo "<span name='status_visit' title='Этот пользователь находится сейчас на этом сайте.' class=\"badge badge-pill badge-success\">online</span>";
            } else {
                echo "<span name='status_visit' title='Этого пользователя сейчас нет на этом сайте.' class=\"badge badge-pill badge-danger\">offline</span>";
            } ?></h3>
        <div id="MainProfile" class="form-row">
            <div id="MenuProfile" style="margin: 15px;" class="col">
                <?php
                // Если это не мой профиль
                if ($auth_data['id'] != $_GET['id'] && !$profile['blocked']) {
                    $id = $_GET['id'];
                    $firstName = $profile['first_name'];
                    $lastName = $profile['last_name'];
                    echo "<button data-toggle=\"modal\" data-target=\"#sendMessageModal\" class=\"btn btn-info btn-rounded\" style=\"width: 220px;margin: 5px;background-color: #36BEC3;border-color: #36BEC3;\"><i class='fa fa-envelope-o'></i> Написать сообщение</button><br/>";


                    echo "<button id='btn_removeFromFavorites$id' onclick='removeFromFavorites($id)' type='button' role=\"button\" class=\"btn btn-info btn-rounded\" style=\"width: 220px;margin: 5px;background-color: #36BEC3;border-color: #36BEC3;display: ";
                    if ($profile['is_favorite']) {
                        echo "inline";
                    } else {
                        echo "none";
                    }
                    echo "\"><i class='fa fa-star'></i> Удалить из избранных</button>";
                    echo "<button id='btn_addToFavorites$id' onclick='addToFavorites($id)' type='button' role=\"button\" class=\"btn btn-info btn-rounded\" style=\"width: 220px;margin: 5px;background-color: #36BEC3;border-color: #36BEC3;display: ";
                    if ($profile['is_favorite']) {
                        echo "none";
                    } else {
                        echo "inline";
                    }
                    echo "\"><i class='fa fa-star-o'></i> Добавить в избранные</button>";


                    echo "<br/><div class=\"modal fade\" id=\"sendMessageModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"exampleModalCenterTitle\" aria-hidden=\"true\">
                          <div class=\"modal-dialog modal-dialog-centered\" role=\"document\">
                            <div class=\"modal-content\">
                              <div class=\"modal-header\">
                                <h5 class=\"modal-title\" id=\"sendMessageModal\">Отправка сообщения</h5>
                                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                                  <span aria-hidden=\"true\">&times;</span>
                                </button>
                              </div>
                              <form method='post' onsubmit='return validateSendMessage()' action='".Url::to(['/messages/send'])."' enctype=\"multipart/form-data\">
                                <input type=\"hidden\" name=\"_csrf\" value=\"" . Yii::$app->request->getCsrfToken() . "\" />
                                  <div id='parentOfTextbox' class=\"modal-body\">
                                    <label style='color: gray;'>Кому:</label>
                                    <label>$firstName $lastName</label>
                                    <input type=\"hidden\" name=\"account_to_id\" id=\"account_to_id\" value=\"" . $_GET['id'] . "\">
                                    <textarea id=\"message\" name=\"message\" class=\"form-control\" rows='8' placeholder='Сообщение...'></textarea>
                                    <div class=\"input-group mb-3\" style='margin-top: 10px'>
                                            <div class=\"custom-file\">
                                                <input type=\"file\" class=\"custom-file-input\" id=\"att_photo_newMessage\" name=\"att_photo_newMessage\" aria-describedby=\"att_photo_newMessage_addon\">
                                                <label class=\"custom-file-label\" for=\"att_photo_newMessage_addon\">Загрузить изображение...</label>
                                            </div>
                                    </div>
                                  </div>
                                  <div class=\"modal-footer\">
                                    <button type=\"button\" class=\"btn btn-secondary btn-rounded\" data-dismiss=\"modal\">Отменить</button>
                                    <input type=\"submit\" class=\"btn btn-info btn-rounded\" style=\"background-color: #36BEC3;border-color: #36BEC3;\" value='Отправить'>
                                  </div>
                                </form>                                
                            </div>
                          </div>
                        </div>";
                }
                $id = $_GET['id'];

                // Если аккаунт не заблокирован
                if (!$profile['blocked']) {
                    echo "<a href=\"" . Url::to(['/album?id=' . $id]) . "\" role=\"button\" class=\"btn btn-info btn-rounded\" style=\"width: 220px;margin: 5px;background-color: #36BEC3;border-color: #36BEC3;\"><i class='fa fa-picture-o'></i> Альбом</a><br/>";
                    if ($auth_data['id'] != $_GET['id']) {
                        echo "<button id='btn_removeFromBlackList$id' onclick='removeFromBlackList($id)' role=\"button\" class=\"btn btn-dark btn-rounded\" style=\"width: 220px;margin: 5px;color:white;display: ";
                        if ($profile['black_list']) {
                            echo "inline";
                        } else {
                            echo "none";
                        }
                        echo "\"><i class='fa fa-ban'></i> Удалить из ЧС</button>";
                        echo "<button id='btn_addToBlackList$id' onclick='addToBlackList($id)' role=\"button\" class=\"btn btn-dark btn-rounded\" style=\"width: 220px;margin: 5px;color:white;display: ";
                        if ($profile['black_list']) {
                            echo "none";
                        } else {
                            echo "inline";
                        }
                        echo "\"><i class='fa fa-ban'></i> Добавить в ЧС</button>";
                    } else {
                        echo "<a href=\"" . Url::to(['/files']) . "\" role=\"button\" class=\"btn btn-info btn-rounded\" style=\"width: 220px;margin: 5px;background-color: #36BEC3;border-color: #36BEC3;\"><i class='fa fa-file-o'></i> Файлы</a><br/>";
                    }
                } else {
                    echo "<div class=\"alert alert-danger\" role=\"alert\" style='margin-top: 20%'>
                          <label style='font-size: 20px'>Пользователь заблокирован.</label>
                        </div>";
                }
                ?>
            </div>
            <div id="PhotoProfile" class="col" style="margin: 15px">
                <a href="" data-toggle="modal" data-target="#photo_profile">
                    <img id="img_photo_profile" class="rounded-circle" src="<?php echo $profile['photo_path']; ?>"
                         height="200px" width="200px"/>
                </a>
                <div class="modal fade" id="photo_profile" tabindex="-1" role="dialog" aria-labelledby="photo_profile"
                     aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                        <div class="modal-content btn-rounded">
                            <div class="modal-header">
                                <h5 class="modal-title" id="photo_profile">Основная фотография пользователя</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <img src="<?php echo $profile['photo_path']; ?>" width="100%"/>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                if ($auth_data['id'] == $_GET['id']) {
                    echo "<button id=\"btn_uodatePhoto\" style='margin-top: 10px;background-color: #36BEC3;border-color: #36BEC3;' type=\"button\" class=\"btn btn-info btn-rounded\" data-toggle=\"modal\" data-target=\"#updatePhotoModal\">
                Обновить фотографию
            </button>";
                    echo "<div class=\"modal fade\" id=\"updatePhotoModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"updatePhotoModalLabel\" aria-hidden=\"true\">
                <div class=\"modal-dialog\" role=\"document\">
                    <div class=\"modal-content\">
                        <div class=\"modal-header\">
                            <h5 class=\"modal-title\" id=\"exampleModalLabel\">Обновление фотографии</h5>
                            <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                                <span aria-hidden=\"true\">&times;</span>
                            </button>
                        </div>
                        <form class=\"needs-validation\" novalidate method=\"post\" action=\"".Url::to(['/main/updatephoto'])."\" enctype=\"multipart/form-data\">
                            <div class=\"modal-body\">
                                <input type=\"hidden\" name=\"_csrf\" value=\"" . Yii::$app->request->getCsrfToken() . "\" />
                                <input type=\"hidden\" name=\"account_to_id\" id=\"account_to_id\" value=\"" . $_GET['id'] . "\">
                                <div class=\"custom-file\">
                                    <input required type=\"file\" class=\"custom-file-input\" id=\"update_photo\" name=\"update_photo\" aria-describedby=\"update_photo_addon\">
                                    <label class=\"custom-file-label\" for=\"update_photo_addon\">Загрузить изображение...</label>
                                    <div class=\"invalid-feedback\">
                                        Заполните это поле.
                                    </div>
                                </div>
                            </div>
                            <div class=\"modal-footer\">
                                <button type=\"button\" class=\"btn btn-secondary btn-rounded\" data-dismiss=\"modal\">Отменить</button>
                                <button type=\"submit\" class=\"btn btn-info btn-rounded\" style=\"background-color: #36BEC3;border-color: #36BEC3;\">Сохранить</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>";
                }
                ?>


            </div>

            <div id="InfoProfile" class="col btn-rounded border" style="margin: 15px;background-color: #f7f7f7;">
                <div class="alert text-left" role="alert">
                    <span style="color: #888a85">Группа: </span>
                    <?php echo $profile['group']; ?>
                    <br/>
                    <span style="color: #888a85;">Дата рождения: </span>
                    <?php echo date_create($profile['date_birthday']['date'])->Format('Y-m-d'); ?>
                    <br/>
                    <span style="color: #888a85;">Пол: </span>
                    <?php echo $profile['gender']; ?>

                    <p>

                        <a href="<?php echo Url::to(['']); ?>" data-toggle="collapse" data-target="#collapseInfo"
                           aria-expanded="false"
                           aria-controls="collapseInfo">
                            Показать подробную информацию
                        </a>
                    </p>
                    <div class="collapse" id="collapseInfo">
                        <div class="alert" role="alert">
                            <?php
                            $tmp = $profile['phone_number'];
                            echo "<span style=\"color: #888a85;\">Телефон: </span>$tmp<br />";
                            $tmp = $profile['activities'];
                            echo "<span style=\"color: #888a85;\">Деятельность: </span>$tmp<br />";
                            $tmp = $profile['interests'];
                            echo "<span style=\"color: #888a85;\">Интересы: </span>$tmp<br />";
                            $tmp = $profile['about_me'];
                            echo "<span style=\"color: #888a85;\">О себе: </span>$tmp";
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="PostsProfile" class="tab-content border" style="visibility: <?php if ($profile['blocked']) {
        echo "collapse";
    } ?>">
        <h5 class="fa fa-bars" style="font-size: 20px"> Записи на странице</h5>
        <div class="card">
            <?php
            if ($auth_data['id'] == $_GET['id'] || ($privacy['write_post'] != 'nobody') && !$profile['blocked']) {
                echo "<div id=\"NewPost\" class=\"card-body\" >
                    <div id='parentOfTextbox' class=\"form-group\">
                        <input type=\"hidden\" name=\"_csrf\" value=\"" . Yii::$app->request->getCsrfToken() . "\" />
                        <input type=\"hidden\" name=\"account_to_id\" id=\"account_to_id\" value=\"" . $_GET['id'] . "\">
                        <div contentEditable=\"true\" data-text=\"Введите своё сообщение\" data-target=\"insert\" class=\"write_msg textarea\" autofocus id=\"text\" name=\"text\" style=\"width: 100%;height: 70px;text-align: left;margin-bottom: 10px\"></div>
                        <div class=\"input-group mb-3\">
                                <div class=\"btn-group dropup\">
                                    <button id=\"att\" type=\"button\" style=\"height: 35px;\" class=\"btn btn-outline-dark btn-rounded dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
                                        <i class='fa fa-paperclip'></i> Прикрепить
                                    </button>
                                    <div class=\"dropdown-menu\">
                                        <button id='attPhoto' class=\"dropdown-item\" data-toggle=\"modal\" data-target=\"#attPhotoModal\"><i class=\"fas fa-image\"></i> Изображение</button>
                                        <button id='attVideoYT' class=\"dropdown-item\" data-toggle=\"modal\" data-target=\"#attVideoYTModal\"><i class=\"fab fa-youtube\"></i> Видео YouTube</button>
                                        <button id='attFile' class=\"dropdown-item\" data-toggle=\"modal\" data-target=\"#attFileModal\"><i class=\"far fa-file\"></i> Файл</button>
                                        <button id='attPoll' class=\"dropdown-item\" data-toggle=\"modal\" data-target=\"#attPollModal\"><i class=\"fas fa-poll-h\"></i> Опрос</button>
                                    </div>
                                </div>
                                <div id=\"atts\" style=\"margin-top: 5px; margin-left: 10px\">
                                    <i id=\"attPhotoIcon\" style=\"display: none\" class='far fa-image' data-toggle=\"modal\" data-target=\"#attPhotoModal\"></i>
                                    <i id=\"attVideoYTIcon\" style=\"display: none\" class='fab fa-youtube' data-toggle=\"modal\" data-target=\"#attVideoYTModal\"></i>
                                    <i id=\"attFileIcon\" style=\"display: none\" class='far fa-file' data-toggle=\"modal\" data-target=\"#attFileModal\"></i>
                                    <i id=\"attPollIcon\" style=\"display: none\" class='fas fa-poll-h' data-toggle=\"modal\" data-target=\"#attPollModal\"></i>
                                </div>
                                <i class=\"far fa-smile\" style=\"margin-left: 10px;margin-top: 8px;font-size: 1.3em;\" id=\"smilesBtn\" data-toggle=\"modal\" data-target=\"#attSmilesModal\"></i>
                        </div>
                    </div>
                    <label id='errorAddPost' style='color: red;display: none'>Нужно заполнить хотя бы одно поле.</label>
                    <input onclick='validateAddPost()' type=\"button\" class=\"btn btn-info btn-xs btn-rounded float-right\" value=\"Отправить\" style=\"width:100px;background-color: #36BEC3;border-color: #36BEC3;\">
            </div>
            <!-- MODAL DIALOGS -->

                <!-- photo -->
                <div class=\"modal fade\" id=\"attPhotoModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"attPhotoModal\" aria-hidden=\"true\">
                    <div class=\"modal-dialog\" role=\"document\">
                        <div class=\"modal-content\">
                            <div class=\"modal-header\">
                                <h5 class=\"modal-title\" id=\"exampleModalLabel\">Прикрепить фотографию</h5>
                                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                                    <span aria-hidden=\"true\">&times;</span>
                                </button>
                            </div>
                            <div class=\"modal-body\">
                                <div class=\"custom-file\">
                                    <input required type=\"file\" class=\"custom-file-input\" id=\"att_photoCreatePost\" name=\"att_photoCreatePost\" aria-describedby=\"update_photo_addon\">
                                    <label class=\"custom-file-label\" for=\"update_photo_addon\">Загрузить изображение...</label>
                                    <div class=\"invalid-feedback\">
                                        Заполните это поле.
                                    </div>
                                </div>
                            </div>
                            <div class=\"modal-footer\">
                                <button type=\"button\" class=\"btn btn-secondary btn-rounded\" data-dismiss=\"modal\">Скрыть</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- video youtube -->
                <div class=\"modal fade\" id=\"attVideoYTModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"attVideoYTModal\" aria-hidden=\"true\">
                    <div class=\"modal-dialog\" role=\"document\">
                        <div class=\"modal-content\">
                            <div class=\"modal-header\">
                                <h5 class=\"modal-title\" id=\"exampleModalLabel\">Прикрепить YouTube видео</h5>
                                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                                    <span aria-hidden=\"true\">&times;</span>
                                </button>
                            </div>
                            <div class=\"modal-body\">
                                <div class=\"input-group mb-3\">
                                    <div class=\"border\" style=\"background-color: #e9ecef\">
                                        <a href='http://www.youtube.com'><img src=\"" . Url::to('@web/resources/OtherIcons/youtube.png') . "\" style=\"height: 40px;margin-right: 5px;margin-left: 5px\" /></a>
                                    </div>
                                    <span class=\"input-group-text\" id=\"basic-addon3\">https://www.youtube.com/watch?v=</span>
                                    <input type=\"text\" class=\"form-control\" id=\"video_link\" name=\"video_link\" aria-describedby=\"basic-addon3\">
                                    <button id=\"att_video_link_help\" class=\"btn input-group-text\" title=\"О внедрении YouTube-видео подробно написано во вкладке 'Помощь' разделе 'Добавление видео из YouTube'\" type=\"button\"><i class=\"fa fa-question\" aria-hidden=\"true\" style=\"color: #9d9d9d;\"></i></button>
                                </div>
                            </div>
                            <div class=\"modal-footer\">
                                <button type=\"button\" class=\"btn btn-secondary btn-rounded\" data-dismiss=\"modal\">Скрыть</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- file -->
                <div class=\"modal fade\" id=\"attFileModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"attFileModal\" aria-hidden=\"true\">
                    <div class=\"modal-dialog\" role=\"document\">
                        <div class=\"modal-content\">
                            <div class=\"modal-header\">
                                <h5 class=\"modal-title\" id=\"exampleModalLabel\">Прикрепить файл</h5>
                                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                                    <span aria-hidden=\"true\">&times;</span>
                                </button>
                            </div>
                            <div class=\"modal-body\">
                                <a href=\"" . Url::to(['/files']) . "\">Мои файлы</a>
                                <div id=\"filesList\" style=\"text-align: left\"></div>
                            </div>
                            <div class=\"modal-footer\">
                                <button type=\"button\" class=\"btn btn-secondary btn-rounded\" data-dismiss=\"modal\">Скрыть</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- smiles -->
                <div class=\"modal fade\" id=\"attSmilesModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"attSmilesModal\" aria-hidden=\"true\">
                    <div class=\"modal-dialog\" role=\"document\">
                        <div class=\"modal-content\">
                            <div class=\"modal-header\">
                                <h5 class=\"modal-title\" id=\"exampleModalLabel\">Выберите смайлик</h5>
                                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                                    <span aria-hidden=\"true\">&times;</span>
                                </button>
                            </div>
                            <div id=\"smilesList\" class=\"modal-body\">
                                <!-- Место для всех смайликов (кодов) -->
                            </div>
                            <div class=\"modal-footer\">
                                <button type=\"button\" class=\"btn btn-secondary btn-rounded\" data-dismiss=\"modal\">Скрыть</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- poll -->
                <div class=\"modal fade\" id=\"attPollModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"attPollModal\" aria-hidden=\"true\">
                    <div class=\"modal-dialog\" role=\"document\">
                        <div class=\"modal-content\">
                            <div class=\"modal-header\">
                                <h5 class=\"modal-title\" id=\"exampleModalLabel\">Добавление опроса</h5>
                                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                                    <span aria-hidden=\"true\">&times;</span>
                                </button>
                            </div>
                            <div class=\"modal-body\">
                                <div class=\"form-group row\" style=\"margin-top: 20px\">
                                    <label class=\"col-sm-2 col-form-label\">Тема</label>
                                    <div class=\"col-sm-10\">
                                        <input type=\"text\" class=\"form-control\" id=\"themePoll\" name=\"themePoll\" placeholder=\"Введите здесь название темы...\" style=\"width: 100%\">
                                    </div>
                                </div>
                                <button onclick='addChoice()' type=\"button\" style='margin-bottom: 10px' class=\"btn btn-success btn-sm btn-rounded\"><i class=\"fas fa-plus\"></i></button>
                                <button onclick='removeLastChoice()' type=\"button\" style='margin-bottom: 10px;margin-left: 10px' class=\"btn btn-danger btn-sm btn-rounded\"><i class=\"fas fa-minus\"></i></button>
                                <div id='answerChoices'>
                                    <input type=\"text\" class=\"form-control\" name=\"answerChoice\" placeholder=\"Введите вариант ответа\" style=\"width: 100%\">
                                </div>
                                <div style='margin-top: 15px;margin-bottom: -15px'>
                                    <input id='addPollAnon' type=\"checkbox\" aria-label=\"Анонимный опрос\">
                                    <label for='addPollAnon'>Анонимный опрос</label>
                                </div>
                            </div>
                            <div class=\"modal-footer\">
                                <button onclick='removePoll()' type=\"button\" class=\"btn btn-outline-danger btn-rounded float-left\" data-dismiss=\"modal\">Отменить опрос</button>
                                <button type=\"button\" class=\"btn btn-secondary btn-rounded\" data-dismiss=\"modal\">Скрыть</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- list poll voted -->
                <div class=\"modal fade\" id=\"pollVotedModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"pollVotedModal\" aria-hidden=\"true\">
                    <div class=\"modal-dialog\" role=\"document\">
                        <div class=\"modal-content\">
                            <div class=\"modal-header\">
                                <h5 class=\"modal-title\" id=\"exampleModalLabel\">Проголосовавшие</h5>
                                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                                    <span aria-hidden=\"true\">&times;</span>
                                </button>
                            </div>
                            <div class=\"modal-body\">
                                <img id=\"load_anim_poll_option_voted\" style=\"color: grey;display: none;margin-top: 20px\" src=\"" . Url::to('@web/resources/load_anim.svg') . "\">
                                <ul id=\"pollOptionVoted\" class=\"list-group\">
                                </ul>
                            </div>
                            <div class=\"modal-footer\">
                                <button type=\"button\" class=\"btn btn-secondary btn-rounded\" data-dismiss=\"modal\">Скрыть</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- END -->";
            }
            ?>

            <div id="Posts" class="text-left" style="margin-left: 10%;margin-bottom: 20px;width: 80%">
                <?php
                if (isset($posts) && !$profile['blocked']) {
                    foreach ($posts as $key => $value) {
                        $id = $value['id'];
                        $idFrom = $value['id_FROM'];
                        $firstNameFrom = $value['first_name_FROM'];
                        $lastNameFrom = $value['last_name_FROM'];
                        $photoFrom = $value['photo_FROM'];
                        $datetimeAdd = $value['datetime_add'];
                        $message = $value['message'];
                        $pathToImage = $value['path_to_image'];
                        $idTo = $value['id_TO'];
                        $statusVisitFromHtml = "";
                        $videoLink = $value['video_link'];
                        $files = $value['files'];
                        $poll = $value['poll'];
                        $pollVoted = $value['poll_voted'];

                        /*                        // Заменяем ссылки на гипертекстовые
                                                $message = preg_replace('/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i', "<a href=\"\\0\" target=\"_blank\">\\0</a>", $message);*/

                        if ($value['status_visit_FROM'] == "online") {
                            $statusVisitFromHtml = "<span name='status_visit' title='Этот пользователь находится сейчас на этом сайте.' class=\"badge badge-pill badge-success\">online</span>";
                        } else {
                            $statusVisitFromHtml = "<span name='status_visit' title='Этого пользователя сейчас нет на этом сайте.' class=\"badge badge-pill badge-danger\">offline</span>";
                        }

                        echo "<div id='post$id' class=\"tab-content border-top\" style=\"padding-top: 10px;\">
                            <a href='" . Url::to(['/' . $idFrom]) . "'>
                                <img src=\"$photoFrom\" class='rounded-circle' height='50px' width=\"50px\"></a>
                            <a href='" . Url::to(['/' . $idFrom]) . "'>
                                <label>$firstNameFrom $lastNameFrom $statusVisitFromHtml</label></a>
                            <a href='" . Url::to(['/' . $idFrom]) . "'>
                                <label class=\"text-left\" style=\"color: gray;\">$datetimeAdd</label></a>";

                        if ($auth_data['id'] == $idTo || $auth_data['id'] == $idFrom) {
                            echo "<button name='remove_post' onclick='removePost($id)' role='button' aria-label=\"Close\" class=\"close\"><span aria-hidden=\"true\">&times;</span></button>";
                        }

                        echo "<br />
                            <p class='post-message' style=\"word-wrap: break-word;white-space: pre-wrap;text-align: left;margin-top: 5px\">$message</p>";
                        if (!empty($pathToImage)) {
                            echo "<img src=\"$pathToImage\" style='width:50%' class='btn-rounded' >";
                        }
                        if (!empty($videoLink)) {
                            echo "<iframe name=\"video\" style='margin-bottom: 5px;margin-top: 5px' width=\"520px\" height=\"300px\" src=\"https://www.youtube.com/embed/$videoLink\" frameborder=\"0\" allow=\"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>";
                        }
                        // Вывод файлов к сообщению
                        if (!empty($files)) {
                            echo "<br />";
                            foreach ($files as $keyF => $valueF) {
                                if (!empty($valueF)) {
                                    echo "<a href='" . $valueF['path'] . "' target='_blank'>" . $valueF['file_name'] . " (" . formatFileSize($valueF['file_size_bytes']) . ")</a><br />";
                                }
                            }
                        }
                        // Вывод опроса
                        if (!empty($poll)) {

                            // Проверяем, анонимный ли опрос, если да, то формируем html строку с уведомлением
                            $anonTextHtml = '';
                            if ($poll['anon'] == 1) {
                                $anonTextHtml = "<label style='color: gray;font-size: small'>(Анонимный опрос)</label>";
                            }

                            $countAnswers = count($poll['answers']);
                            $summVotes = 0;
                            foreach ($poll['answers'] as $keyP => $valueP) {
                                $summVotes = $summVotes + $valueP['votes'];
                            }
                            echo "<div class=\"jumbotron\" style='padding: 2rem 2rem;'>";
                            echo $anonTextHtml;
                            echo "<div id='postPoll" . $id . "' class=\"span6\">";

                            echo "<h5>" . $poll['theme'] . "</h5>";
                            $imVoted = false;
                            foreach ($poll['answers'] as $keyP => $valueP) {

                                // Check user on voted in poll
                                $voted = false;
                                foreach ($pollVoted as $keyV => $valueV) {
                                    if ($valueV['account_id'] == $auth_data['id']) {
                                        $voted = true;
                                        break;
                                    }
                                }
                                // If not voted
                                if ($voted == false) {
                                    echo "<button onclick='onVoteInPost(" . $id . ", " . $valueP['id'] . ")' class='btn btn-link'>" . $valueP['answer'] . "</button><br />";
                                } else {
                                    // If voted
                                    $imVoted = true;
                                    $percent = 0;
                                    if ($valueP['votes'] > 0) {
                                        $percent = round($valueP['votes'] / $summVotes * 100);
                                    }
                                    echo "<strong class='show-poll-voted' title='Нажмите, чтобы посмотреть проголосовавших.' data-toggle=\"modal\" data-target=\"#pollVotedModal\" onclick='showPollOptionVoted(" . $valueP['id'] . ")'>" . $valueP['answer'] . "</strong><span class=\"float-right\">" . $percent . "% (" . $valueP['votes'] . ")</span>";

                                    echo "<div class=\"progress show-poll-voted\" data-toggle=\"modal\" data-target=\"#pollVotedModal\" onclick='showPollOptionVoted(" . $valueP['id'] . ")'>
                                          <div class=\"progress-bar bg-info\" role=\"progressbar\" style=\"width: " . $percent . "%\" aria-valuenow=\"" . $percent . "\" aria-valuemin=\"0\" aria-valuemax=\"100\"></div>
                                      </div>";
                                }
                            }
                            if ($imVoted) {
                                echo "<button class='btn btn-link float-right' onclick='cancelVoteInPollPost(" . $id . ")'>Отменить голос</button>";
                            }
                            echo "</div>";
                            echo "</div>";
                        }

                        echo "</div>";
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php
if (isset($posts) && isset($is_there_more_posts)) {
    if (count($posts) > 0 && $is_there_more_posts) {
        echo "<button id=\"show_more\" onclick=\"showMore()\" style=\"display: inline-block;margin-top: 20px\" type=\"button\"
                class=\"btn btn-light border btn-rounded\">Показать ещё
            </button>";
    }
}
?>

<img id="load_anim" style="color: grey;display: none;margin-top: 20px"
     src="<?php echo Url::to('@web/resources/load_anim.svg'); ?>">
<a href="#" title="Вернуться к началу" class="topbutton"><i class="fa fa-chevron-up"></i></a>

<script src="<?php echo Url::to('@web/js/general/emotions/jquery.emotions.js'); ?>"></script>
<script type="text/javascript" src="<?php echo Url::to('@web/js/views/main/profile.js'); ?>"></script>