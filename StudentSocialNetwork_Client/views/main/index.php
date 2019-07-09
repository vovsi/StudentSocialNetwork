<?php

use yii\helpers\Url;

if (isset($refresh)) {
    if ($refresh) {
        Yii::$app->response->redirect(Url::to('main/index'));
        exit();
    }
}
?>

<link rel="stylesheet" href="<?php echo Url::to('@web/css/views/main/index.css'); ?>">
<title>Главная</title>

<div class="jumbotron">
    <h1 id="headerText" class="display-4">Добро пожаловать на сайт SocialNetwork!</h1>
    <p class="lead">Данный сайт является социальной сетью для студентов университета.</p>
    <hr class="my-4">
    <p>Если у вас уже есть аккаунт, укажите данные в поле авторизации (входа). Если нет, то перейдите по <a
                style="display: inline-block" class="font-weight-light" href="<?= Url::toRoute(['help/register']); ?>">этой
            ссылке</a>.</p>
</div>

