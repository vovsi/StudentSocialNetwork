<?php

use yii\helpers\Url;

?>

<title>Добавление фото</title>

<div class="tab-content border" id="pills-tabContent" style="padding: 10px">
    <li class="list-group-item">Добавление фото</li>
    <div class="tab-pane fade show active" id="pills-my_data" role="tabpanel" aria-labelledby="pills-my_data-tab">
        <form class="needs-validation" novalidate method="post" action="<?php echo Url::to(['/album/loadphoto']); ?>"
              enctype="multipart/form-data">
            <input type="hidden" name="_csrf" value="<?php echo Yii::$app->request->getCsrfToken(); ?>"/>
            <div class="form-group row" style="margin-top: 20px">
                <label class="col-sm-2 col-form-label"><p style="color: red;display: inline-block">*</p>Фото</label>
                <div class="col-sm-10">
                    <div class="custom-file">
                        <input required type="file" class="custom-file-input" id="photo" name="photo"
                               aria-describedby="photo_addon">
                        <label class="custom-file-label" for="photo_addon">Загрузить изображение...</label>
                        <div class="invalid-feedback">
                            Заполните это поле.
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Описание</label>
                <div class="col-sm-10">
                    <textarea id="description" name="description" placeholder="Введите здесь описание к изображению..."
                              class="form-control" style="width: 100%" rows="5"></textarea>
                </div>
            </div>
            <input type="submit" value="Добавить" class="btn btn-outline-success btn-rounded btn-lg">
        </form>
    </div>
</div>

<script type="text/javascript" src="<?php echo Url::to('@web/js/views/album/addPhoto.js'); ?>"></script>