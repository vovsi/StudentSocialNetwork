<?php

use yii\helpers\Url;

if (isset($refresh)) {
    if ($refresh) {
        header('Location: /messages');
        exit();
    }
}
?>

<link rel="stylesheet" href="<?php echo Url::to('@web/css/general/emotions/jquery.emotions.css'); ?>">
<title>Диалоги</title>

<ul class="nav nav-tabs" id="menuMessages" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="dialogs-tab" data-toggle="tab" href="#dialogsTab" role="tab"
           aria-controls="dialogsTab" aria-selected="true">Диалоги <span id="count_new_dialogs"
                                                                         style='margin-left: 10px'
                                                                         class='badge badge-info'></span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="conversations-tab" data-toggle="tab" href="#conversationsTab" role="tab"
           aria-controls="conversationsTab" aria-selected="false">Беседы <span id="count_new_conversations"
                                                                               style='margin-left: 10px'
                                                                               class='badge badge-info'></span></a>
    </li>
</ul>
<div id="tip_newMessages" style="display: none;width: 100%" class="list-group-item">
    <a href="<?php echo Url::to(['/messages']); ?>"
       style='font-size: medium;word-wrap: break-word;display: inline;text-decoration: none'>Есть новые
        сообщения. Обновить?</a>
</div>
<div class="tab-content" id="menuMessagesContent">
    <div class="tab-pane fade show active" id="dialogsTab" role="tabpanel" aria-labelledby="dialogs-tab">
        <!-- TAB DIALOGS -->
        <div id="Dialogs" class="list-group">
            <li class="list-group-item">
                <div class="dropdown float-left" style="display: inline-block;">
                    <button class="btn btn-outline-dark btn-sm dropdown-toggle btn-rounded" style="width: 120px"
                            type="button" id="dialogOptions" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                        Опции
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dialogOptions">
                        <a class="dropdown-item disabled" href="#">Показать только непрочитанные</a>
                        <a class="dropdown-item disabled" href="#">Поиск по сообщениям</a>
                        <a class="dropdown-item disabled" href="#">Диалоги по группе</a>
                        <a class="dropdown-item disabled" href="#">Сообщения на дату</a>
                    </div>
                </div>
                <label>Диалоги</label>
                <a href="<?php echo Url::to(['/search']); ?>" class="btn btn-primary btn-rounded btn-sm float-right"
                   style="width: 140px">Найти
                    собеседника</a>
            </li>
            <?php
            if (isset($dialogs)) {
                if (count($dialogs) > 0) {
                    foreach ($dialogs as $key => $value) {
                        $countNewMessages = '';
                        if ($value['count_new_messages'] > 0) {
                            $countNewMessages = "<span style='margin-left: 10px' class=\"badge badge-info\">+" . $value['count_new_messages'] . "</span>";
                        }

                        $dateSend = $value["date_change"];
                        echo "<a href=\"" . Url::to(['/messages/dialog?id=' . $value['dialog_id']]) . "\" class='list-group-item list-group-item-action flex-column align-items-start'>
                            <div class=\"d-flex w-100 justify-content-between\">
                                    <img src=\"" . $value['interlocutor_image'] . "\" class='rounded-circle' height='50px' width=\"50px\">
                                    <label class='text-center' style='font-weight: bold'>" . $value['interlocutor_first_name'] . ' ' . $value['interlocutor_last_name'] . "</label>";

                        echo "<label style='color: grey;font-size: small'>(" . $value['interlocutor_group'] . ")</label>";

                        if ($value['interlocutor_status_visit'] == "online") {
                            echo " <span name='status_visit' style='height: 20px' title='Этот пользователь находится сейчас на этом сайте.' class=\"badge badge-pill badge-success\">online</span>";
                        } else {
                            echo " <span name='status_visit' style='height: 20px' title='Этого пользователя сейчас нет на этом сайте.' class=\"badge badge-pill badge-danger\">offline</span>";
                        }
                        echo "</div>";

                        if (!empty($value['last_message'])) {
                            // Если длина сообщения больше 60 символов, то сокращаем, для вывода
                            if (strlen($value['last_message']) > 60) {
                                $cutStr = substr($value['last_message'], 0, 59);
                                $resMess = $cutStr . '...';
                            } else {
                                $resMess = $value['last_message'];
                            }

                            echo "
                                <p style='display: inline-block;' class='mb-1'><label style='color: gray;'>" . $value['sender_point'] . "</label>" . $resMess . $countNewMessages . "<span style='color: #888a85;display: inline-block;margin-left: 5%;font-size: small'>" . $dateSend . "</span></p>";
                        } else {
                            if (!empty($value['last_message_photo'])) {
                                echo "
                                <p style='display: inline-block;' class='mb-1'><label style='color: gray;'>" . $value['sender_point'] . "</label>(Фотография)<span style='color: #888a85;display: inline-block;margin-left: 5%;font-size: small'>" . $dateSend . "</span></p>";
                            } else {
                                if (!empty($value['last_message_files'])) {
                                    echo "
                                <p style='display: inline-block;' class='mb-1'><label style='color: gray;'>" . $value['sender_point'] . "</label>(Файл)<span style='color: #888a85;display: inline-block;margin-left: 5%;font-size: small'>" . $dateSend . "</span></p>";
                                } else {
                                    if (!empty($value['last_message_videoYT'])) {
                                        echo "
                                <p style='display: inline-block;' class='mb-1'><label style='color: gray;'>" . $value['sender_point'] . "</label>(Видео YT)<span style='color: #888a85;display: inline-block;margin-left: 5%;font-size: small'>" . $dateSend . "</span></p>";
                                    }
                                }
                            }
                        }
                        echo "</a>";
                    }
                } else {
                    echo "<br/><h4 style='color: gray;'>У вас нет диалогов</h4><h5 style='color: gray;'><a href='" . Url::to(['/search']) . "'>Хотите найти собеседника?</a></h5>";
                }
            }
            ?>
        </div>
        <?php
        if (isset($dialogs) && isset($is_there_more_dialogs)) {
            if (count($dialogs) > 0 && $is_there_more_dialogs) {
                echo "<button id=\"show_more_dialogs\" onclick=\"showMoreDialogs()\" style=\"display: inline-block;margin-top: 20px\"
                        type=\"button\" class=\"btn btn-light border btn-rounded\">Показать ещё
                        </q></button>";
            }
        }
        ?>

    </div>
    <div class="tab-pane fade" id="conversationsTab" role="tabpanel" aria-labelledby="conversations-tab">
        <!-- TAB CONVERSATIONS -->
        <div id="Conversations" class="list-group">
            <li class="list-group-item">
                <div class="dropdown float-left" style="display: inline-block;">
                    <button class="btn btn-outline-dark btn-sm dropdown-toggle btn-rounded" style="width: 120px"
                            type="button" id="conversationOptions" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                        Опции
                    </button>
                    <div class="dropdown-menu" aria-labelledby="conversationOptions">
                        <a class="dropdown-item disabled" href="#">Показать только непрочитанные</a>
                        <a class="dropdown-item disabled" href="#">Поиск по сообщениям</a>
                        <a class="dropdown-item disabled" href="#">Сообщения на дату</a>
                    </div>
                </div>
                <label>Беседы</label>
                <button type="button" class="btn btn-primary btn-rounded btn-sm float-right" style="width: 120px"
                        data-toggle="modal" data-target="#addConversationModal">Создать беседу
                </button>
            </li>
            <?php
            if (isset($conversations)) {
                if (count($conversations) > 0) {
                    foreach ($conversations as $key => $value) {
                        $countNewMessages = '';
                        if ($value['count_new_messages'] > 0) {
                            $countNewMessages = "<span style='margin-left: 10px' class=\"badge badge-info\">+" . $value['count_new_messages'] . "</span>";
                        }

                        $dateSend = $value["date_change"];
                        echo "<a href=\"" . Url::to(['/messages/conversation?id=' . $value['conversation_id']]) . "\" class='list-group-item list-group-item-action flex-column align-items-start'>
                            <div class=\"d-flex w-100 justify-content-between\">
                                    <img alt='img' src=\"" . $value['conversation_photo'] . "\" class='rounded-circle' height='50px' width=\"50px\">
                                    <label style='font-weight: bold;margin-top:10px;margin-right: 50px'>" . $value['conversation_name'] . "</label>
                                    <label></label>";
                        echo "</div>";

                        if (!empty($value['last_message'])) {
                            // Если длина сообщения больше 60 символов, то сокращаем, для вывода
                            if (strlen($value['last_message']) > 60) {
                                $cutStr = substr($value['last_message'], 0, 59);
                                $resMess = $cutStr . '...';
                            } else {
                                $resMess = $value['last_message'];
                            }

                            echo "
                                <p style='display: inline-block;' class='mb-1'><label style='color: gray;'>" . $value['sender_point'] . "</label>" . $resMess . $countNewMessages . "<span style='color: #888a85;display: inline-block;margin-left: 5%;font-size: small'>" . $dateSend . "</span></p>";
                        } else {
                            if (!empty($value['last_message_photo'])) {
                                echo "
                                <p style='display: inline-block;' class='mb-1'><label style='color: gray;'>" . $value['sender_point'] . "</label>(Фотография)<span style='color: #888a85;display: inline-block;margin-left: 5%;font-size: small'>" . $dateSend . "</span></p>";
                            } else {
                                if (!empty($value['last_message_files'])) {
                                    echo "
                                <p style='display: inline-block;' class='mb-1'><label style='color: gray;'>" . $value['sender_point'] . "</label>(Файл)<span style='color: #888a85;display: inline-block;margin-left: 5%;font-size: small'>" . $dateSend . "</span></p>";
                                } else {
                                    if (!empty($value['last_message_videoYT'])) {
                                        echo "
                                <p style='display: inline-block;' class='mb-1'><label style='color: gray;'>" . $value['sender_point'] . "</label>(Видео YT)<span style='color: #888a85;display: inline-block;margin-left: 5%;font-size: small'>" . $dateSend . "</span></p>";
                                    }
                                }
                            }
                        }
                        echo "</a>";
                    }
                } else {
                    echo "<br/><h4 style='color: gray;'>Вы не состоите ни в одной беседе</h4><h5 style='color: gray;'><button class=\"btn btn-link\" data-toggle=\"modal\" data-target=\"#addConversationModal\">Хотите создать беседу?</button></h5>";
                }
            }
            ?>
        </div>
        <?php
        if (isset($conversations) && isset($isThereMoreConversations)) {
            if (count($conversations) > 0 && $isThereMoreConversations) {
                echo "<button id=\"show_more_conversations\" onclick=\"showMoreConversations()\"
                                style=\"display: inline-block;margin-top: 20px\" type=\"button\" class=\"btn btn-light border btn-rounded\">
                            Показать ещё
                        </button>";
            }
        }
        ?>
    </div>
</div>

<img id="load_anim" style="color: grey;display: none;margin-top: 20px"
     src="<?php echo Url::to('@web/resources/load_anim.svg'); ?>">
<a href="#" title="Вернуться к началу" class="topbutton"><i class="fa fa-chevron-up"></i></a>

<!-- MODALS DIALOG -->
<!-- add conversation -->
<div class="modal fade" id="addConversationModal" tabindex="-1" role="dialog" aria-labelledby="addConversationModal"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Создание беседы</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row" style="margin-top: 20px">
                    <label class="col-sm-2 col-form-label">Название</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="addConversationName" name="addConversationName"
                               placeholder="Введите здесь название беседы..." style="width: 100%">
                    </div>
                </div>
                <div class="form-group row" style="margin-top: 20px">
                    <label class="col-sm-3 col-form-label">Изображение</label>
                    <div class="col-sm-9">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="addConversationImage"
                                   name="addConversationImage" aria-describedby="load_file_addon">
                            <label class="custom-file-label" for="load_file_addon">Загрузить файл...</label>
                        </div>
                    </div>
                </div>
                <div class="form-group row" style="margin-top: 20px">
                    <label class="col-sm-2 col-form-label">Участники</label>
                    <div class="col-sm-10">
                        <span id="spanSelectedMembers" style='height: 20px;margin-bottom: 5px'
                              class="badge badge-pill badge-info"><label>Выбрано: </label> <label
                                    id="countSelectedMembers">0</label></span>
                        <div id="membersFavoritesList" style="text-align: left;height: 250px;overflow-y: scroll;"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-rounded" data-dismiss="modal">Скрыть</button>
                <button id="createConversation" onclick="createConversationForm()" type="submit"
                        class="btn btn-info btn-rounded" style="background-color: #36BEC3;border-color: #36BEC3;">
                    Создать
                </button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo Url::to('@web/js/general/emotions/jquery.emotions.js'); ?>"></script>
<script type="text/javascript" src="<?php echo Url::to('@web/js/views/messages/index.js'); ?>"></script>