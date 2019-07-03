<?php

use yii\helpers\Url;

if (isset($refresh)) {
    if ($refresh) {
        header('Location: /album');
        exit();
    }
}
?>

<link href="<?php echo Url::to('@web/css/views/album/index.css'); ?>" rel="stylesheet">
<title>Альбом</title>

<li class="list-group-item">Альбом
    <?php
    if (isset($photos)) {
        echo "<text style='height: 20px' class=\"badge badge-pill badge-secondary text-right\">" . count($photos) . "</text>";
    }
    ?>
</li>

<?php
// Если это не мой профиль, то не выводить кнопку добавления фото
if ($auth_data['id'] == $_GET['id'] || empty($_GET['id'])) {
    echo "<div id=\"addPhoto\">
    <br />
    <a href=\"" . Url::to(['/album/addphoto']) . "\" class=\"btn btn-outline-success btn-lg btn-block btn-rounded\"><i style='margin: 5px' class=\"fa fa-plus\"></i>Добавить</a>
    
</div>";
}

?>

<input id="idUser" type="hidden" value="<?php echo $_GET['id']; ?>"/>

<div id="gallery" class="masonry">

    <?php
    if (isset($photos)) {
        if (count($photos) > 0) {
            foreach ($photos as $key => $value) {
                if (!empty($value)) {
                    $toHtml = "<a id='photo" . $value['id'] . "' href='" . Url::to(['']) . "' data-toggle=\"modal\" data-target=\"#photoModal\" 
                            data-description='" . $value['description'] . "' 
                            data-id='" . $value['id'] . "' 
                            data-datetime='" . $value['datetime_add'] . "' 
                            data-path=\"" . $value['path'] . "\">
                            <img id=\"img_photo_profile\" class='btn-rounded' src=\"" . $value['path'] . "\"/>
                            </a>";

                    echo "<div class=\"item\">
                    $toHtml
                    </div>";
                }
            }
        } else {
            echo "</div><br/><h4 style='color: gray;'>Пусто</h4>";
        }
    } else {
        echo "</div><br/><h4 style='color: gray;'>Пусто</h4>";
    }
    ?>
    <div class="modal fade" id="photoModal" tabindex="-1" role="dialog" aria-labelledby="photoModal" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content btn-rounded">
                <div class="modal-header">
                    <h5 class="modal-title" id="photo_profile">Фото пользователя</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <img id="modal_photo_fullsize" src="" width="100%" alt="img"/>
                    <br/>
                    <p id="description" style="word-wrap: break-word;white-space: pre-wrap;text-align: left"></p>
                </div>
                <label id="datetime_add" style="color: #9d9d9d;"></label>
                <div class="modal-footer">
                    <?php
                    if ($auth_data['id'] == $_GET['id'] || empty($_GET['id'])) {
                        echo "<button id='removePhoto' onclick='' role='button' aria-label=\"Close\" class=\"btn btn-outline-danger btn-rounded\"> <i class=\"fa fa-trash\"></i> Удалить</button>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<a href="#" title="Вернуться к началу" class="topbutton"><i class="fa fa-chevron-up"></i></a>

<script type="text/javascript" src="<?php echo Url::to('@web/js/views/album/index.js'); ?>"></script>