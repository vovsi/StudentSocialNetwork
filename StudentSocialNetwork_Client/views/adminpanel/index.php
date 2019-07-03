<?php

use yii\helpers\Url;

if (isset($refresh)) {
    if ($refresh) {
        header('Location: /adminpanel');
        exit();
    }
}
?>

<link rel="stylesheet" href="<?php echo Url::to('@web/css/views/adminpanel/index.css'); ?>">
<title>Админ-панель</title>

<ul class="nav nav-tabs mb-3" id="pills-tab" role="tablist" style="margin-top: 10px">
    <li class="nav-item">
        <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab"
           aria-controls="pills-home" aria-selected="true"><i class="fa fa-plus"></i> Добавить аккаунт</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab"
           aria-controls="pills-profile" aria-selected="false"><i class="far fa-trash-alt"></i> Блокировка аккаунта</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab"
           aria-controls="pills-contact" aria-selected="false"><i class="fa fa-info-circle"></i> Информация об аккаунтах</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-groups" role="tab"
           aria-controls="pills-groups" aria-selected="false"><i class="fa fa-users"></i> Группы</a>
    </li>
</ul>
<div class="tab-content border" id="pills-tabContent" style="padding: 10px">
    <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
        <div id="RegistrationAccount">
            <form class="needs-validation" novalidate method="post"
                  action="<?php echo Url::to(['adminpanel/registrationaccount']); ?>/">
                <input type="hidden" name="_csrf" value="<?php echo Yii::$app->request->getCsrfToken(); ?>"/>
                <div class="form-row">
                    <div class="col">
                        <input required type="text" class="form-control btn-rounded" name="first_name" id="first_name"
                               placeholder="Имя">
                        <div class="invalid-feedback">
                            Заполните это поле.
                        </div>
                    </div>
                    <div class="col">
                        <input required type="text" class="form-control btn-rounded" name="last_name" id="last_name"
                               placeholder="Фамилия">
                        <div class="invalid-feedback">
                            Заполните это поле.
                        </div>
                    </div>
                    <div class="col">
                        <input required type="text" class="form-control btn-rounded" name="patronymic" id="patronymic"
                               placeholder="Отчество">
                        <div class="invalid-feedback">
                            Заполните это поле.
                        </div>
                    </div>
                </div>
                <br/>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Группа</label>
                        <select id="group" name="group" class="form-control btn-rounded" style="width: 200px">
                            <?php
                            foreach ($groups as $key => $value) {
                                echo "<option>$value</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Роль</label>
                        <select id="role" name="role" class="form-control btn-rounded" style="width: 200px">
                            <option selected>user</option>
                            <option>admin</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Пол</label>
                        <select id="gender" name="gender" class="form-control btn-rounded" style="width: 200px">
                            <option selected>Мужской</option>
                            <option>Женский</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col" style="margin-top: 20px">
                        <input required type="email" class="form-control btn-rounded" name="email" id="email"
                               placeholder="Email" style="width: 200px">
                        <div class="invalid-feedback text-left">
                            Заполните это поле.
                        </div>
                    </div>
                </div>
                <input class="btn btn-outline-success btn-lg btn-rounded" type="submit" value="Зарегистрировать">
            </form>
        </div>
    </div>
    <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
        <div id="BlockAccount">
            <form class="needs-validation" novalidate method="post"
                  action="<?php echo Url::to(['/adminpanel/blockaccount']); ?>">
                <input type="hidden" name="_csrf" value="<?php echo Yii::$app->request->getCsrfToken(); ?>"/>
                <div class="form" style="text-align: center">
                    <div style="display: inline-block">
                        <label>Что сделать?</label>
                        <select id="actionBlock" name="actionBlock" class="form-control btn-rounded"
                                style="width: 200px">
                            <option>Заблокировать</option>
                            <option>Разблокировать</option>
                        </select>
                    </div>
                    <br/><br/>
                    <div class="col" style="display: inline-block">
                        <input required type="email" class="form-control btn-rounded" name="email" id="email"
                               placeholder="Email" style="width: 200px;display: inline-block"><br/>
                        <div class="invalid-feedback">
                            Заполните это поле.
                        </div>
                    </div>
                </div>
                <br/>
                <input class="btn btn-outline-success btn-lg btn-rounded" type="submit" value="Применить">
            </form>
        </div>
    </div>
    <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
        <div id="AccountInfo">
            <p>
                <button class="btn btn-info btn-rounded" type="button" data-toggle="collapse"
                        data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                    Администраторы
                    <?php
                    if (isset($admins)) {
                        echo "<p class=\"badge badge-pill badge-light\" style='margin: 0px'>" . count($admins) . "</p>";
                    }
                    ?>
                </button>
            </p>
            <div class="collapse" id="collapseExample">
                <div class="card card-body">
                    <ul class="list-group">
                        <?php
                        if (isset($admins)) {
                            if (count($admins) > 0) {
                                echo "<li class=\"list-group-item\"><div class='form-row' style='font-weight: bold'><div class='col'>Имя Фамилия</div><div class='col'>E-mail</div><div class='col'>Страница</div></div></li>";
                                foreach ($admins as $key => $value) {
                                    $id = $value['id'];
                                    $firstName = $value['first_name'];
                                    $lastName = $value['last_name'];
                                    $email = $value['email'];
                                    echo "<li class=\"list-group-item\"><div class='form-row'><div class='col'>$firstName $lastName</div><div class='col'>$email</div><div class='col'><a href='" . Url::to(['/' . $id]) . "'>Профиль</a></div></div></li>";
                                }
                            }
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="pills-groups" role="tabpanel" aria-labelledby="pills-groups-tab">
        <ul class="nav nav-tabs mb-3" id="pills-tab" role="tablist" style="margin-top: 10px">
            <li class="nav-item">
                <a class="nav-link active" id="pills-createGroup-tab" data-toggle="pill" href="#pills-createGroup"
                   role="tab" aria-controls="pills-createGroup" aria-selected="true"><i class="fa fa-plus"></i> Создать</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pills-renameGroup-tab" data-toggle="pill" href="#pills-renameGroup" role="tab"
                   aria-controls="pills-renameGroup" aria-selected="true"><i class="fas fa-pencil-alt"></i>
                    Переименовать</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pills-moveUser-tab" data-toggle="pill" href="#pills-moveUser" role="tab"
                   aria-controls="pills-moveUser" aria-selected="true"><i class="fa fa-address-card"></i> Поменять
                    группу пользователю</a>
            </li>
        </ul>
        <div class="tab-content border" id="pills-tabContentGroup" style="padding: 10px">
            <div class="tab-pane fade show active" id="pills-createGroup" role="tabpanel"
                 aria-labelledby="pills-createGroup-tab">
                <form class="needs-validation" novalidate method="post"
                      action="<?php echo Url::to(['/adminpanel/creategroup']); ?>">
                    <input type="hidden" name="_csrf" value="<?php echo Yii::$app->request->getCsrfToken(); ?>"/>
                    <div class="form" style="text-align: center">
                        <div class="col" style="display: inline-block">
                            <input required type="text" class="form-control btn-rounded" name="nameGroup" id="nameGroup"
                                   placeholder="Имя группы" style="width: 200px;display: inline-block"><br/>
                            <div class="invalid-feedback">
                                Заполните это поле.
                            </div>
                        </div>
                    </div>
                    <br/>
                    <input class="btn btn-outline-success btn-lg btn-rounded" type="submit" value="Создать">
                </form>
            </div>
            <div class="tab-pane fade" id="pills-renameGroup" role="tabpanel" aria-labelledby="pills-renameGroup-tab">
                <form class="needs-validation" novalidate method="post"
                      action="<?php echo Url::to(['/adminpanel/renamegroup']); ?>">
                    <input type="hidden" name="_csrf" value="<?php echo Yii::$app->request->getCsrfToken(); ?>"/>
                    <div class="form" style="text-align: center">
                        <div style="display: inline-block">
                            <label>Группа</label>
                            <select id="group" name="group" class="form-control btn-rounded" style="width: 200px">
                                <?php
                                foreach ($groups as $key => $value) {
                                    echo "<option>$value</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <br/><br/>
                        <div style="display: inline-block">
                            <input required type="text" class="form-control btn-rounded" name="newNameGroup"
                                   id="newNameGroup" placeholder="Новое имя" style="width: 200px;display: inline-block"><br/>
                            <div class="invalid-feedback">
                                Заполните это поле.
                            </div>
                        </div>
                    </div>
                    <br/>
                    <input class="btn btn-outline-success btn-lg btn-rounded" type="submit" value="Применить">
                </form>
            </div>
            <div class="tab-pane fade" id="pills-moveUser" role="tabpanel" aria-labelledby="pills-moveUser-tab">
                <form class="needs-validation" novalidate method="post"
                      action="<?php echo Url::to(['adminpanel/moveuser']); ?>/">
                    <input type="hidden" name="_csrf" value="<?php echo Yii::$app->request->getCsrfToken(); ?>"/>
                    <div class="form" style="text-align: center">
                        <div style="display: inline-block">
                            <label>ID пользователя</label>
                            <input required type="text" class="form-control btn-rounded" name="idAccount" id="idAccount"
                                   placeholder="ID" style="width: 200px;display: inline-block"><br/>
                            <div class="invalid-feedback">
                                Заполните это поле.
                            </div>
                        </div>
                        <br/><br/>
                        <div style="display: inline-block">
                            <label>Переместить в группу</label>
                            <select id="group" name="group" class="form-control btn-rounded" style="width: 200px">
                                <?php
                                foreach ($groups as $key => $value) {
                                    echo "<option>$value</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <br/>
                    <input class="btn btn-outline-success btn-lg btn-rounded" type="submit" value="Применить">
                </form>
            </div>
        </div>
    </div>
</div>