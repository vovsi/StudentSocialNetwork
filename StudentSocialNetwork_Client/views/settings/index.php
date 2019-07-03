<?php

use yii\helpers\Url;

if (isset($refresh)) {
    if ($refresh) {
        header('Location: /settings');
        exit();
    }
}
?>

<link rel="stylesheet" href="<?php echo Url::to('@web/css/views/settings/index.css'); ?>">
<title>Настройки</title>

<ul class="nav nav-tabs mb-3" id="pills-tab" role="tablist" style="margin-top: 10px">
    <li class="nav-item">
        <a class="nav-link active" id="pills-my_data-tab" data-toggle="pill" href="#pills-my_data" role="tab"
           aria-controls="pills-my_data" aria-selected="true"><i class="fa fa-address-card"></i> Личные данные</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="pills-privacy-tab" data-toggle="pill" href="#pills-privacy" role="tab"
           aria-controls="pills-privacy" aria-selected="false"><i class="fa fa-eye"></i> Приватность</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="pills-security-tab" data-toggle="pill" href="#pills-security" role="tab"
           aria-controls="pills-security" aria-selected="false"><i class="fa fa-lock"></i> Безопасность</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="pills-black_list-tab" data-toggle="pill" href="#pills-black_list" role="tab"
           aria-controls="pills-black_list" aria-selected="false"><i class="fa fa-ban"></i> Чёрный список</a>
    </li>
</ul>
<div class="tab-content border" id="pills-tabContent" style="padding: 10px">
    <div class="tab-pane fade show active" id="pills-my_data" role="tabpanel" aria-labelledby="pills-my_data-tab">
        <div class="progress" style="margin-top: 10px;margin-bottom: 20px">
            <div id="progressBarFillPrivateData" class="progress-bar progress-bar-striped"
                 title="Процент заполнения личных данных." role="progressbar"
                 style="width: 0%;background-color: #30b0b5" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%
            </div>
        </div>
        <form class="needs-validation" novalidate method="post" action="<?php echo Url::to(['/settings/mydata']); ?>">
            <input type="hidden" name="_csrf" value="<?php echo Yii::$app->request->getCsrfToken(); ?>"/>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label"><p style="color: red;display: inline-block">*</p>Имя</label>
                <div class="col-sm-10">
                    <input required type="text" class="form-control btn-rounded" id="first_name" name="first_name"
                           value="<?php echo $profile['first_name'] ?>">
                    <div class="invalid-feedback">
                        Заполните это поле.
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label"><p style="color: red;display: inline-block">*</p>Фамилия</label>
                <div class="col-sm-10">
                    <input required type="text" class="form-control btn-rounded" id="last_name" name="last_name"
                           value="<?php echo $profile['last_name'] ?>">
                    <div class="invalid-feedback">
                        Заполните это поле.
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label"><p style="color: red;display: inline-block">*</p>Отчество</label>
                <div class="col-sm-10">
                    <input required type="text" class="form-control btn-rounded" id="patronymic" name="patronymic"
                           value="<?php echo $profile['patronymic'] ?>">
                    <div class="invalid-feedback">
                        Заполните это поле.
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label"><p style="color: red;display: inline-block">*</p>Email</label>
                <div class="col-sm-10">
                    <input required type="email" class="form-control btn-rounded" id="email" name="email"
                           placeholder="email@mail.ru" value="<?php echo $profile['email'] ?>">
                    <div class="invalid-feedback">
                        Заполните это поле.
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label"><p style="color: red;display: inline-block">*</p>Пол</label>
                <div class="col-sm-10">
                    <select id="gender" name="gender" class="form-control btn-rounded">
                        <option <?php if ($profile['gender'] == "Мужской") {
                            echo "selected";
                        } ?> >Мужской
                        </option>
                        <option <?php if ($profile['gender'] == "Женский") {
                            echo "selected";
                        } ?> >Женский
                        </option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Номер телефона</label>
                <div class="col-sm-10">
                    <input type="tel" class="form-control btn-rounded" id="phone_number" name="phone_number"
                           placeholder="+380999999999" value="<?php echo $profile['phone_number'] ?>">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Деятельность</label>
                <div class="col-sm-10">
                    <textarea id="activities" name="activities"
                              class="form-control btn-rounded"><?php echo $profile['activities'] ?></textarea>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Интересы</label>
                <div class="col-sm-10">
                    <textarea id="interests" name="interests"
                              class="form-control btn-rounded"><?php echo $profile['interests'] ?></textarea>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">О мне</label>
                <div class="col-sm-10">
                    <textarea id="about_me" name="about_me"
                              class="form-control btn-rounded"><?php echo $profile['about_me'] ?></textarea>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Дата рождения</label>
                <div class="col-sm-10">
                    <input type="date" id="date_birthday" name="date_birthday" class="form-control btn-rounded"
                           min="1900-01-01" max="<?php echo date('Y-m-d'); ?>"
                           value="<?php echo date_create($profile['date_birthday']['date'])->Format('Y-m-d'); ?>">
                </div>
            </div>
            <input class="btn btn-outline-success btn-lg btn-rounded" type="submit" value="Сохранить">
        </form>
    </div>
    <div class="tab-pane fade" id="pills-privacy" role="tabpanel" aria-labelledby="pills-privacy-tab">
        <div class="tab-pane fade show active" id="pills-my_data" role="tabpanel" aria-labelledby="pills-my_data-tab">
            <form method="post" action="<?php echo Url::to(['/settings/privacy']); ?>">
                <input type="hidden" name="_csrf" value="<?php echo Yii::$app->request->getCsrfToken(); ?>"/>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Кто может оставлять записи у меня на странице?</label>
                    <div class="col-sm-10">
                        <select id="write_post" name="write_post" class="form-control btn-rounded">
                            <option <?php if ($privacy['write_post'] == "all") {
                                echo "selected";
                            } ?> >Все
                            </option>
                            <option <?php if ($privacy['write_post'] == "nobody") {
                                echo "selected";
                            } ?> >Никто
                            </option>
                        </select>
                    </div>
                </div>
                <input class="btn btn-outline-success btn-lg btn-rounded" type="submit" value="Сохранить">
            </form>
        </div>
    </div>
    <div class="tab-pane fade" id="pills-security" role="tabpanel" aria-labelledby="pills-security-tab">
        <h4 style="color: #5e5e5e;">Смена пароля</h4>
        <form class="needs-validation" novalidate method="post" action="<?php echo Url::to(['/settings/security']); ?>">
            <input type="hidden" name="_csrf" value="<?php echo Yii::$app->request->getCsrfToken(); ?>"/>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Старый пароль</label>
                <div class="col-sm-10">
                    <input required type="password" class="form-control btn-rounded" id="old_password"
                           name="old_password">
                    <div class="invalid-feedback">
                        Заполните это поле.
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Новый пароль</label>
                <div class="col-sm-10">
                    <input required type="password" class="form-control btn-rounded" id="new_password"
                           name="new_password">
                    <div class="invalid-feedback">
                        Заполните это поле.
                    </div>
                </div>
            </div>
            <input class="btn btn-outline-success btn-lg btn-rounded" type="submit" value="Сохранить">
        </form>
    </div>
    <div class="tab-pane fade" id="pills-black_list" role="tabpanel" aria-labelledby="pills-black_list-tab">
        <ul id="black_list" class="list-group">
            <?php
            if (isset($black_list)) {
                if (count($black_list) > 0) {
                    foreach ($black_list as $key => $value) {
                        $userBlackListId = $value['user_black_list_id'];
                        $firstName = $value['first_name'];
                        $lastName = $value['last_name'];
                        $photoUser = $value['photo_path'];
                        $statusVisitHtml = "";
                        if ($value['status_visit'] == "online") {
                            $statusVisitHtml = "<span name='status_visit' title='Этот пользователь находится сейчас на этом сайте.' class=\"badge badge-pill badge-success\">online</span> ";
                        } else {
                            $statusVisitHtml = "<span name='status_visit' title='Этого пользователя сейчас нет на этом сайте.' class=\"badge badge-pill badge-danger\">offline</span> ";
                        }
                        echo "<li id='btn_removeFromBlackList$userBlackListId' class=\"list-group-item\">
                            <div class='form-row'>
                            <a href='/$userBlackListId' class='col'>
                                <img src=\"$photoUser\" class='rounded-circle' height='50px' width=\"50px\"></a>
                                <div class='col'>$statusVisitHtml $firstName $lastName</div>
                                <div class='col'>";
                        echo "<button onclick='removeFromBlackList($userBlackListId)' role='button' class=\"btn btn-info btn-rounded\" style=\"background-color: #36BEC3;border-color: #36BEC3;display: inline\">Убрать</button>";
                        echo "</div>
                            </div>
                        </li>";
                    }
                } else {
                    echo "<h4 style='color: gray;'>Чёрный список пуст</h4>";
                }
            }
            ?>
        </ul>
        <?php
        if (isset($black_list) && isset($is_there_more_black_list)) {
            if (count($black_list) > 0 && $is_there_more_black_list) {
                echo "<button id=\"show_more\" onclick=\"showMore()\" style=\"display: inline-block;margin-top: 20px\" type=\"button\"
                        class=\"btn btn-light border btn-rounded\">Показать ещё
                    </button>";
            }
        }
        ?>
        <img id="load_anim" style="color: grey;display: none;margin-top: 20px"
             src="<?php echo Url::to('@web/resources/load_anim.svg'); ?>">
    </div>
</div>

<script type="text/javascript" src="<?php echo Url::to('@web/js/views/settings/index.js'); ?>"></script>