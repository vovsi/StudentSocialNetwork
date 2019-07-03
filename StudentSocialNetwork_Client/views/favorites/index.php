<title>Избранные</title>

<ul id="favoritesUsers" class="list-group">
    <li class="list-group-item">Избранные</li>
    <?php

    use yii\helpers\Url;

    if (isset($favorites)) {
        if (count($favorites) > 0) {
            foreach ($favorites as $key => $value) {
                $userFavoriteId = $value['user_favorite_id'];
                $firstName = $value['first_name'];
                $lastName = $value['last_name'];
                $photoUser = $value['photo_path'];
                $statusVisitHtml = "";
                if ($value['status_visit'] == "online") {
                    $statusVisitHtml = "<span name='status_visit' title='Этот пользователь находится сейчас на этом сайте.' class=\"badge badge-pill badge-success\">online</span> ";
                } else {
                    $statusVisitHtml = "<span name='status_visit' title='Этого пользователя сейчас нет на этом сайте.' class=\"badge badge-pill badge-danger\">offline</span> ";
                }
                echo "<li id='btn_removeFromFavorites$userFavoriteId' class=\"list-group-item\">
                        <div class='form-row'>
                            <a href='" . Url::to(['/' . $userFavoriteId]) . "' class='col'>
                                <img src=\"$photoUser\" class='rounded-circle' height='50px' width=\"50px\"></a>
                            <div class='col'> $statusVisitHtml $firstName $lastName</div>
                            <div class='col'>";
                echo "<button onclick='removeFromFavorites($userFavoriteId)' role='button' class=\"btn btn-info btn-rounded\" style=\"background-color: #36BEC3;border-color: #36BEC3;display: inline\">Убрать</button>";
                echo "</div>
                        </div>
                        </li>";
            }
        } else {
            echo "<br/><h4 style='color: gray;'>Пусто</h4>";
        }
    } else {
        echo "<br/><h4 style='color: gray;'>Список избранных не найден.</h4>";
    }
    ?>
</ul>
<?php
if (isset($favorites) && isset($is_there_more_favorites)) {
    if (count($favorites) > 0 && $is_there_more_favorites) {
        echo "<button id=\"show_more\" onclick=\"showMore()\" style=\"display: inline-block;margin-top: 20px\" type=\"button\"
                class=\"btn btn-light border btn-rounded\">Показать ещё
            </button>";
    }
}
?>
<img id="load_anim" style="color: grey;display: none;margin-top: 20px"
     src="<?php echo Url::to('@web/resources/load_anim.svg'); ?>">
<a href="#" title="Вернуться к началу" class="topbutton"><i class="fa fa-chevron-up"></i></a>

<script type="text/javascript" src="<?php echo Url::to('@web/js/views/favorites/index.js'); ?>"></script>