<?php

use yii\helpers\Url;

if (isset($refresh)) {
    if ($refresh) {
        header('Location: /news');
        exit();
    }
}
?>

<link href="<?php echo Url::to('@web/css/general/btnCollapse/btnCollapse.css'); ?>" rel="stylesheet">
<title>Новости</title>

<input id="myId" type="hidden" value="<?php echo $auth_data['id']; ?>"/>
<li class="list-group-item">Новости</li>
<?php
// Если это не мой профиль, то не выводить кнопку добавления фото
if (isset($auth_data)) {
    if ($auth_data['role'] == 'admin') {
        echo "<div id=\"add\">
    <br />
    <a href=\"" . Url::to(['/news/addnewspage']) . "\" class=\"btn btn-outline-success btn-lg btn-block btn-rounded\"><i style='margin: 5px' class=\"fa fa-plus\"></i>Добавить</a>
    <br />
</div>";
    }
} ?>

<div class="collapse-content">
    <img id="loadCalendarEvents_anim" style="color: grey;display: inline-block;margin-top: 20px"
         src="<?php echo Url::to('@web/resources/load_anim.svg'); ?>">
    <div id="incContentCalendarEvents" class="text-center"></div>
    <div class="toggle"></div>
</div>
<button style="margin: 10px" type="button" class="btn btn-light border btn-rounded toggle-btn">Раскрыть</button>

<div id="News">
    <?php if (isset($news)) {
        if (count($news) > 0) {
            foreach ($news as $key => $value) {
                if (strlen($value['description']) > 500) {
                    $cutStr = substr($value['description'], 0, 499);
                    $resDesc = $cutStr . '...';
                } else {
                    $resDesc = $value['description'];
                }

                $eventHtml = "";
                $eventDate = $value['event_date'];
                if (!empty($value['event_date'])) {
                    $eventHtml = "<i name=\"icon_event\" class=\"fa fa-bell\" title=\"Эта новость является событием. Подробности на странице с новостью.\" style=\"color: #d41717;width: 20px;\"></i>
                <span name='event_date' title='Дата и время начала события.' class=\"badge border border-danger btn-rounded\" style='margin-right: 5px'>" . $eventDate . "</span>";
                }

                $idNews = $value['id'];
                echo "<div id='news$idNews' class=\"list-group\">
                    <a href=\"\" data-toggle=\"modal\" data-target=\"#fullViewNewsModal\" data-id='" . $value['id']
                    . "' class=\"list-group-item list-group-item-action flex-column align-items-start\">
                        <div class=\"d-flex w-100 justify-content-between\">
                            <h5 class=\"text-left\">" . $eventHtml . $value['theme'] . "</h5>
                            <small name='date_add' title='Дата-время добавления новости.'>" . $value['datetime_add'] . "</small>
                        </div>
                        <p class=\"mb-1\" style=\"word-wrap: break-word;white-space: pre-wrap;text-align: left\">$resDesc</p>
                    </a>
                </div>";
            }
        } else {
            echo "<br/><h4 style='color: gray;'>Пусто</h4>";
        }
    } else {
        echo "<br/><h4 style='color: gray;'>Пусто</h4>";
    }

    ?>
</div>

<button id="showEvent_actionModalBtn" data-toggle="modal" data-target="#fullViewEventModal"
        style="display:none;"></button>
<div id="fullViewEventModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="fullViewEventModal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content btn-rounded">
            <div class="modal-header">
                <h5 class="modal-title" id="themeEvents"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="accordion" id="listEvents">
                </div>
                <img id="loadEvents_anim" style="color: grey;display: none;margin-top: 20px"
                     src="<?php echo Url::to('@web/resources/load_anim.svg'); ?>">
            </div>
        </div>
    </div>
</div>
<div id="fullViewNewsModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="fullViewNewsModal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content btn-rounded">
            <div id="fullViewNewsModalHeader" class="modal-header">
                <h5 class="modal-title" id="theme"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <img id="load_anim_full_size" style="color: grey;display: none;margin-top: 20px"
                 src="<?php echo Url::to('@web/resources/load_anim.svg'); ?>">
            <div id="fullViewNewsModalBody" class="modal-body">
                <div id="modalEventInfo" style="display: block;margin-top: 3px;">
                    <i name="icon_event" class="fa fa-bell" title="Эта новость является событием."
                       style="color: #d41717;font-size: 20px;"></i>
                    <span id="event_date" name='event_date' title='Дата и время начала события.'
                          class="badge border border-danger btn-rounded"
                          style='margin-right: 5px;font-size: 20px'></span>
                    <span id="event_description" name="event_description" class="blockquote-footer"
                          style="margin: 10px"></span>
                </div>
                <p id="description" style="word-wrap: break-word;white-space: pre-wrap;text-align: left"></p>
                <img id="image" src="<?php echo Url::to(['']); ?>" width="100%" style="display: none"/><br/><br/>
                <iframe id="video" name="video" style="display: none;" width="100%" height="400px"
                        src="<?php echo Url::to(['']); ?>"
                        frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen></iframe>
                <div id="poll" style='text-align: left;'>
                    <div class="jumbotron" style='padding: 2rem 2rem;'>
                        <div id="pollContent" class="span6">
                        </div>
                    </div>
                </div>
            </div>
            <label id="datetime_add" style="color: #5e5e5e;font-size: 14px"></label>
            <div class="modal-footer">
                <?php
                if (isset($auth_data)) {
                    if ($auth_data['role'] == 'admin') {
                        echo "<button id='removeNews' onclick='' role='button' aria-label=\"Close\" class=\"btn btn-outline-danger btn-rounded\"> <i class=\"fa fa-trash\"></i> Удалить</button>";
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>
<!-- MODAL DIALOGS -->
<!-- list poll voted -->
<div class="modal fade" id="pollVotedModal" tabindex="-1" role="dialog" aria-labelledby="pollVotedModal"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Проголосовавшие</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <img id="load_anim_poll_option_voted" style="color: grey;display: none;margin-top: 20px"
                     src="<?php echo Url::to('@web/resources/load_anim.svg'); ?>">
                <ul id="pollOptionVoted" class="list-group">
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-rounded" data-dismiss="modal">Скрыть</button>
            </div>
        </div>
    </div>
</div>

<?php
if (isset($news) && isset($is_there_more)) {
    if (count($news) > 0 && $is_there_more) {
        echo "<button id=\"show_more\" onclick=\"showMore()\" style=\"display: inline-block;margin-top: 20px\" type=\"button\"
                    class=\"btn btn-light border btn-rounded\">Показать ещё
                </button>";
    }
}
?>

<img id="load_anim" style="color: grey;display: none;margin-top: 20px"
     src="<?php echo Url::to('@web/resources/load_anim.svg'); ?>">
<a href="#" title="Вернуться к началу" class="topbutton"><i class="fa fa-chevron-up"></i></a>

<script type="text/javascript" src="<?php echo Url::to('@web/js/views/news/index.js'); ?>"></script>