<?php

use yii\helpers\Url;

if (isset($refresh)) {
    if ($refresh) {
        Yii::$app->response->redirect(Url::to('news/index'));
        exit();
    }
}
?>

<title>Добавление новости</title>

<div class="tab-content border" id="pills-tabContent" style="padding: 10px">
    <li class="list-group-item">Добавление новости</li>
    <div class="tab-pane fade show active" id="pills-my_data" role="tabpanel" aria-labelledby="pills-my_data-tab">
        <form class="needs-validation" method="post" action="<?php echo Url::to(['/news/add']); ?>"
              enctype="multipart/form-data" novalidate>
            <input type="hidden" name="_csrf" value="<?php echo Yii::$app->request->getCsrfToken(); ?>"/>
            <div class="form-group row" style="margin-top: 20px">
                <label class="col-sm-2 col-form-label"><p style="color: red;display: inline-block">*</p>Тема</label>
                <div class="col-sm-10">
                    <input required type="text" class="form-control" id="theme" name="theme"
                           placeholder="Введите здесь название темы..." style="width: 100%">
                    <div class="invalid-feedback">
                        Заполните это поле.
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label"><p style="color: red;display: inline-block">*</p>Описание</label>
                <div class="col-sm-10">
                    <textarea required id="description" name="description"
                              placeholder="Введите здесь описание темы (новости)" class="form-control"
                              style="width: 100%" rows="5"></textarea>
                    <div class="invalid-feedback">
                        Заполните это поле.
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Изображение</label>
                <div class="col-sm-10">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="image" name="image"
                               aria-describedby="image_addon">
                        <label class="custom-file-label" for="image_addon">Загрузить изображение...</label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Видео из <a href="http://www.youtube.com">YouTube</a></label>
                <div class="input-group col-sm-10">
                    <div class="border" style="background-color: #e9ecef">
                        <img src="<?php echo Url::to('@web/resources/OtherIcons/youtube.png'); ?>"
                             style="height: 40px;margin-right: 5px;margin-left: 5px"/>
                    </div>

                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon3">https://www.youtube.com/watch?v=</span>
                    </div>
                    <input type="text" class="form-control" id="video_link" name="video_link"
                           aria-describedby="basic-addon3">
                    <button id="video_link_help" class="btn input-group-text"
                            title="О внедрении YouTube-видео подробно написано во вкладке 'Помощь' разделе 'Добавление видео из YouTube'"
                            type="button"><i class="fa fa-question" aria-hidden="true" style="color: #9d9d9d;"></i>
                    </button>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Событие</label>
                <div class="input-group col-sm-10">
                    <div class="input-group-prepend">
                        <div class="border" style="background-color: #e9ecef;">
                            <i id="icon_event" class="fa fa-bell"
                               title="Укажите дату-время и место события, если ваша новость связана со встречей/мероприятием и т.п."
                               style="color: #d41717;width: 50px;vertical-align: -webkit-baseline-middle"></i>
                        </div>
                    </div>
                    <input type="datetime-local" min="<?php echo date('Y-m-d\TH:i'); ?>" max="2100-01-01" width="100px"
                           class="form-control" id="date_event" name="date_event" aria-describedby="basic-addon3">
                    <input type="text" class="form-control" id="event_description" name="event_description"
                           placeholder="Где произойдет событие?">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Опрос</label>
                <div class="col-sm-10">
                    <div class="form-group row" style="margin-top: 20px">
                        <label class="col-sm-2 col-form-label">Тема</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="themePoll" name="themePoll"
                                   placeholder="Введите здесь название темы...">
                        </div>
                    </div>
                    <button onclick='addChoice()' type="button" style='margin-bottom: 10px'
                            class="btn btn-success btn-sm btn-rounded"><i class="fas fa-plus"></i></button>
                    <button onclick='removeLastChoice()' type="button" style='margin-bottom: 10px;margin-left: 10px'
                            class="btn btn-danger btn-sm btn-rounded"><i class="fas fa-minus"></i></button>
                    <div id='answerChoices'>
                        <input type="text" class="form-control" name="answerChoice[]"
                               placeholder="Введите вариант ответа" style="width: 100%">
                    </div>
                    <div style='margin-top: 15px;margin-bottom: -15px'>
                        <input id='addPollAnon' name="addPollAnon" type="checkbox" aria-label="Анонимный опрос">
                        <label for='addPollAnon'>Анонимный опрос</label>
                    </div>
                </div>


            </div>
            <input type="submit" value="Готово" class="btn btn-outline-success btn-lg btn-rounded">
        </form>
    </div>
</div>

<script type="text/javascript" src="<?php echo Url::to('@web/js/views/news/addNewsPage.js'); ?>"></script>