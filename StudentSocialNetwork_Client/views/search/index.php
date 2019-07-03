<?php

use yii\helpers\Url;

if (isset($refresh)) {
    if ($refresh) {
        header('Location: /search');
        exit();
    }
}
?>

<title>Поиск по пользователям</title>

<ul id="searchResult" class="list-group">
    <li class="list-group-item">Результаты поиска по пользователям (пустое поле выдаст всех пользователей)</li>
    <li class="list-group-item">
        <form class="form-inline" method="post" action="<?php echo Url::to(['/search']); ?>">
            <input type="hidden" name="_csrf" value="<?php echo Yii::$app->request->getCsrfToken(); ?>"/>
            <div class="input-group" style="width: 100%">
                <input id="query_text" name="query_text" type="text" class="form-control btn-rounded"
                       placeholder="Поиск..." aria-label="Поиск..." aria-describedby="button-addon2"
                       value="<?php if (isset($search_text)) {
                           echo $search_text;
                       } ?>"/>
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary btn-rounded" type="submit" id="button-addon2">Найти
                    </button>
                </div>
            </div>
        </form>
    </li>
    <?php
    if (isset($search_text) && isset($result_search)) {
        if (count($result_search) > 0) {
            foreach ($result_search as $key => $value) {
                $userId = $value['id'];
                $firstName = $value['first_name'];
                $lastName = $value['last_name'];
                $patronymic = $value['patronymic'];
                $photoUser = $value['photo_path'];
                $group = $value['group'];
                $statusVisitHtml = "";
                if ($value['status_visit'] == "online") {
                    $statusVisitHtml = "<span name='status_visit' title='Этот пользователь находится сейчас на этом сайте.' class=\"badge badge-pill badge-success\">online</span> ";
                } else {
                    $statusVisitHtml = "<span name='status_visit' title='Этого пользователя сейчас нет на этом сайте.' class=\"badge badge-pill badge-danger\">offline</span> ";
                }
                echo "<li class=\"list-group-item\">
                        <div class='form-row'>
                            <a href='" . Url::to(['/' . $userId]) . "' class='col'>
                                <img src=\"$photoUser\" class='rounded-circle' height='50px' width=\"50px\"></a>
                            <a href='" . Url::to(['/' . $userId]) . "' class='col'><div class='col'> $firstName $patronymic $lastName <label style='color: gray;'>&nbsp;($group)</label></div></a>
                            <div class='col'>
                                $statusVisitHtml
                            </div>
                        </div>
                        </li>";
            }
        } else {
            echo "<br/><h4 style='color: gray;'>Никого не найдено</h4>";
        }
    } else {
        echo "<br/><h4 style='color: gray;'>Никого не найдено.</h4>";
    }
    ?>
</ul>

<button id="show_more" hidden onclick="showMore()" style="display: inline-block;margin-top: 20px" type="button"
        class="btn btn-light border btn-rounded">Показать ещё
</button>
<img id="load_anim" hidden style="color: grey;display: none;margin-top: 20px"
     src="<?php echo Url::to('@web/resources/load_anim.svg'); ?>">
<a href="#" title="Вернуться к началу" class="topbutton"><i class="fa fa-chevron-up"></i></a>

<script type="text/javascript" src="<?php echo Url::to('@web/js/views/search/index.js'); ?>"></script>