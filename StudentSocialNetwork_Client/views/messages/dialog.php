<?php

use yii\helpers\Url;

if (isset($refresh)) {
    if ($refresh) {
        header('Location: /messages/dialog');
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

<link rel="stylesheet" href="<?php echo Url::to('@web/css/views/messages/dialog.css'); ?>">
<link href="<?php echo Url::to('@web/css/general/dialogList/styleMessages.css'); ?>" rel="stylesheet">
<link rel="stylesheet" href="<?php echo Url::to('@web/css/general/emotions/jquery.emotions.css'); ?>">
<title>Диалог</title>

<?php
if (isset($recipient_id) && isset($recipient_photo_path) && isset($recipient_first_name) && isset($recipient_last_name) && isset($messages)) {
    echo "<div class=\"container\">";
    if ($recipient_status_visit == "online") {
        echo "<span name='status_visit' title='Этот пользователь находится сейчас на этом сайте.' class=\"badge badge-pill badge-success\">online</span> ";
    } else {
        echo "<span name='status_visit' title='Этого пользователя сейчас нет на этом сайте.' class=\"badge badge-pill badge-danger\">offline</span> ";
    }
    echo "<input type='hidden' id='dialog_id' value='" . $_GET['id'] . "' />";
    echo "<a href=\"" . Url::to(['/' . $recipient_id]) . "\"><label style=\"font-weight: bold; color: #5e5e5e\">$recipient_first_name $recipient_last_name</label></a>
               <div class=\"btn-group dropleft\" style='margin-bottom: 3px;margin-left: 20px'>
                  <button type=\"button\" class=\"btn btn-outline-dark btn-sm dropdown-toggle btn-rounded\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
                    Действия
                  </button>
                  <div class=\"dropdown-menu\">";

    echo "<a href='" . Url::to(['/' . $recipient_id]) . "' id='openProfile' class=\"dropdown-item\">Открыть профиль</a>";
    echo "<a href='" . Url::to(['/album?id=' . $recipient_id]) . "' id='openAlbum' class=\"dropdown-item\">Открыть альбом</a>";

    echo "<button id='btn_removeFromBlackList$recipient_id' onclick='removeFromBlackList($recipient_id)' role='button' class=\"dropdown-item\" style=\"display: ";
    if ($black_list) {
        echo "inline";
    } else {
        echo "none";
    }
    echo "\">Удалить из ЧС</button>";
    echo "<button id='btn_addToBlackList$recipient_id' onclick='addToBlackList($recipient_id)' role='button' class=\"dropdown-item\" style=\"display: ";
    if ($black_list) {
        echo "none";
    } else {
        echo "inline";
    }
    echo "\">Добавить в ЧС</button>";

    echo "<button id='btn_removeFromFavorites$recipient_id' onclick='removeFromFavorites($recipient_id)' role='button' class=\"dropdown-item\" style=\"display: ";
    if ($favorite) {
        echo "inline";
    } else {
        echo "none";
    }
    echo "\">Удалить из избранных</button>";
    echo "<button id='btn_addToFavorites$recipient_id' onclick='addToFavorites($recipient_id)' role='button' class=\"dropdown-item\" style=\"display: ";
    if ($favorite) {
        echo "none";
    } else {
        echo "inline";
    }
    echo "\">Добавить в избранные</button>";


    echo "
                  </div>
            </div>
        </div>";
}
?>

<div class="container" style="height: 80%;">
    <div class="messaging">
        <div class="inbox_msg">
            <div class="mesgs">
                <?php
                if (isset($messages) && isset($is_there_more)) {
                    if (count($messages) > 0 && $is_there_more) {
                        echo "<button id=\"show_more\" onclick=\"showMore()\" style=\"display: none;margin-bottom: 20px\" type=\"button\"
                                class=\"btn btn-light border btn-rounded\">Показать ещё
                            </button>";
                    }
                }
                ?>

                <img id="load_anim" style="color: grey;display: none;margin-bottom: 20px"
                     src="<?php echo Url::to('@web/resources/load_anim.svg'); ?>" alt="img">
                <div class="msg_history" id="messages">
                    <?php
                    if (isset($recipient_id) && isset($recipient_photo_path) && isset($recipient_first_name) && isset($recipient_last_name) && isset($messages)) {
                        foreach ($messages as $key => $value) {
                            $senderId = $value['sender_id'];
                            $senderPhoto = $value['sender_photo'];
                            $senderFirstName = $value['sender_first_name'];
                            $senderLastName = $value['sender_last_name'];
                            $messageText = $value['message_text'];
                            $messagePhotoPath = $value['message_photo_path'];
                            $dateSend = $value['date_send'];
                            $files = $value['files'];
                            $videoYT = $value['videoYT'];

                            // Если отправитель - тот, с кем ведется диалог
                            if ($senderId == $recipient_id) {
                                echo "<div class=\"incoming_msg\">";
                                echo "<div class=\"incoming_msg_img\"> <a href='" . Url::to(['/' . $senderId]) . "'><img class='rounded-circle' style='width: 50px;height: 50px;' src=\"$senderPhoto\" alt=\"sunil\"></a> </div>";
                                echo "<div class=\"received_msg\">";
                                echo "<div class=\"received_withd_msg\">";
                            } else {
                                echo "<div class=\"outgoing_msg\">";
                                echo "<div class=\"sent_msg\">";
                            }

                            echo "<p>$messageText";
                            // Вывод фото к сообщению
                            if (!empty($messagePhotoPath)) {
                                echo "<br/><a href='' data-toggle=\"modal\" data-target=\"#photo\" data-whatever=\"$messagePhotoPath\"><img id=\"img_photo_profile\" alt='img' class='btn-rounded' width=\"50%\" src=\"$messagePhotoPath\" style='margin-bottom: 10px'/></a>";
                            }
                            // Вывод видео YT к сообщению
                            if (!empty($videoYT)) {
                                echo "<br/><iframe id=\"video\" name=\"video\" style='margin-bottom: 5px' width=\"100%\" src=\"https://www.youtube.com/embed/" . $videoYT . "\" frameborder=\"0\" allow=\"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>";
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
                            echo "</p>";
                            echo "<span class=\"time_date\">$dateSend</span>";

                            if ($senderId == $recipient_id) {
                                echo "</div></div></div>";
                            } else {
                                echo "</div></div>";
                            }
                        }
                    }
                    ?>

                    <div class="modal fade" id="photo" tabindex="-1" role="dialog" aria-labelledby="photo"
                         aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                            <div class="modal-content btn-rounded">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="photo_profile">Фотография</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <img id="modal_photo_fullsize" src="" alt="img"/>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="type_msg">
                    <div id="parentOfTextbox" class="input_msg_write">
                        <input type="hidden" id="send_mess_csrf" name="_csrf"
                               value="<?php echo Yii::$app->request->getCsrfToken(); ?>"/>
                        <input type="hidden" name="account_to_id" id="account_to_id"
                               value="<?php echo $recipient_id; ?>">
                        <div contentEditable="true" data-text="Введите своё сообщение" data-target="insert"
                             class="write_msg textarea" autofocus id="message" name="message"
                             style="width: 100%;height: 70px;text-align: left;margin-bottom: 10px"></div>
                        <button id="send_mess_submit" onclick="validateSendMessage()" class="msg_send_btn"
                                title="Отправить сообщение" type="button"><i class="far fa-paper-plane"></i></button>
                        <div class="input-group mb-3">
                            <div class="btn-group dropup">
                                <button id="att" type="button" style="height: 35px;"
                                        class="btn btn-outline-dark btn-rounded dropdown-toggle" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                    <i class='fa fa-paperclip'></i> Прикрепить
                                </button>
                                <div class="dropdown-menu">
                                    <button id='attPhoto' class="dropdown-item" data-toggle="modal"
                                            data-target="#attPhotoModal"><i class="fas fa-image"></i> Изображение
                                    </button>
                                    <button id='attVideoYT' class="dropdown-item" data-toggle="modal"
                                            data-target="#attVideoYTModal"><i class="fab fa-youtube"></i> Видео YouTube
                                    </button>
                                    <button id='attFile' class="dropdown-item" data-toggle="modal"
                                            data-target="#attFileModal"><i class="far fa-file"></i> Файл
                                    </button>
                                </div>
                            </div>
                            <div id="atts" style="margin-top: 5px; margin-left: 10px">
                                <i id="attPhotoIcon" style="display: none" class='far fa-image' data-toggle="modal"
                                   data-target="#attPhotoModal"></i>
                                <i id="attVideoYTIcon" style="display: none" class='fab fa-youtube' data-toggle="modal"
                                   data-target="#attVideoYTModal"></i>
                                <i id="attFileIcon" style="display: none" class='far fa-file' data-toggle="modal"
                                   data-target="#attFileModal"></i>
                            </div>
                            <i class="far fa-smile" style="margin-left: 10px;margin-top: 8px;font-size: 1.3em;"
                               id="smilesBtn" data-toggle="modal" data-target="#attSmilesModal"></i>
                        </div>
                    </div>
                </div>

                <!-- MODAL DIALOGS -->

                <!-- photo -->
                <div class="modal fade" id="attPhotoModal" tabindex="-1" role="dialog" aria-labelledby="attPhotoModal"
                     aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Прикрепить фотографию</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="custom-file">
                                    <input required type="file" class="custom-file-input" id="att_photo_newMessage"
                                           name="att_photo_newMessage" aria-describedby="update_photo_addon">
                                    <label class="custom-file-label" for="update_photo_addon">Загрузить
                                        изображение...</label>
                                    <div class="invalid-feedback">
                                        Заполните это поле.
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-rounded" data-dismiss="modal">
                                    Скрыть
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- video youtube -->
                <div class="modal fade" id="attVideoYTModal" tabindex="-1" role="dialog"
                     aria-labelledby="attVideoYTModal" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Прикрепить YouTube видео</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="input-group mb-3">
                                    <div class="border" style="background-color: #e9ecef">
                                        <a href='http://www.youtube.com'><img
                                                    src="<?php echo Url::to('@web/resources/OtherIcons/youtube.png'); ?>"
                                                    style="height: 40px;margin-right: 5px;margin-left: 5px" alt="img"/></a>
                                    </div>
                                    <span class="input-group-text"
                                          id="basic-addon3">https://www.youtube.com/watch?v=</span>
                                    <input type="text" class="form-control" id="att_video_link" name="att_video_link"
                                           aria-describedby="basic-addon3">
                                    <button id="att_video_link_help" class="btn input-group-text"
                                            title="О внедрении YouTube-видео подробно написано во вкладке 'Помощь' разделе 'Добавление видео из YouTube'"
                                            type="button"><i class="fa fa-question" aria-hidden="true"
                                                             style="color: #9d9d9d;"></i></button>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-rounded" data-dismiss="modal">
                                    Скрыть
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- file -->
                <div class="modal fade" id="attFileModal" tabindex="-1" role="dialog" aria-labelledby="attFileModal"
                     aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Прикрепить файл</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <a href="<?php echo Url::to(['/files']); ?>">Мои файлы</a>
                                <div id="filesList" style="text-align: left"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-rounded" data-dismiss="modal">
                                    Скрыть
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- smiles -->
                <div class="modal fade" id="attSmilesModal" tabindex="-1" role="dialog" aria-labelledby="attSmilesModal"
                     aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Выберите смайлик</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div id="smilesList" class="modal-body">
                                <!-- Место для всех смайликов (кодов) -->
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-rounded" data-dismiss="modal">
                                    Скрыть
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- END -->
            </div>
        </div>
    </div>

    <script src="<?php echo Url::to('@web/js/general/emotions/jquery.emotions.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo Url::to('@web/js/views/messages/dialog.js'); ?>"></script>