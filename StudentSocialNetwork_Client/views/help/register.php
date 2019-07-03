<?php

use yii\helpers\Url;

?>

<title>Помощь по регистрации</title>

<link rel="stylesheet" href="<?php echo Url::to('@web/css/views/help/register.css'); ?>">
<div class="jumbotron">
    <h1 id="headerText" class="display-4" style="font-size: 350%">Кто может получить аккаунт на нашем сайте?</h1>
    <ul>
        <li>Учащийся студент в нашем университете</li>
        <li>Администратор данной соц. сети</li>
        <li>Преподаватель нашего университета</li>
    </ul>
    <p class="lead" id="underHeaderText" style="font-size: 100%">Если хоть одно условие из списка выше актуально в вашем
        случае, но доступ к аккаунту вы не имеете, то пишите на почту
        student.social.network.service@gmail.com</p>
</div>

<script type="text/javascript" src="<?php echo Url::to('@web/js/views/help/register.js'); ?>"></script>