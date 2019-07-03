<?php

use yii\helpers\Url;

if (isset($refresh)) {
    if ($refresh) {
        Yii::$app->response->redirect(Url::to('files/index'));
        exit();
    }
}

// Получить читабельный вид размера файла (байты -> кбайты/мбайты/гбайты...)
function formatFileSize($bytes, $precision = 2)
{
    $units = array('байт', 'Кбайт', 'Мбайт', 'Гбайт', 'Тбайт');
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

// Получить путь к иконке для файла (по рассширению фалйа)
function getPathToIconFile($ext)
{
    $res = "/resources/FilesIcons/";
    if ($ext == "pdf") {
        $res .= "pdf.png";
    } else {
        if ($ext == "ppt" || $ext == "pptx") {
            $res .= "ppt.png";
        } else {
            if ($ext == "rar") {
                $res .= "rar.png";
            } else {
                if ($ext == "txt") {
                    $res .= "txt.png";
                } else {
                    if ($ext == "doc" || $ext == "docx" || $ext == "dot" || $ext == "docm" || $ext == "dotx" || $ext == "dotm" || $ext == "docb") {
                        $res .= "word.png";
                    } else {
                        if ($ext == "xls" || $ext == "xlt" || $ext == "xlm" || $ext == "xlsx" || $ext == "xlsm" || $ext == "xltx" || $ext == "xltm") {
                            $res .= "xls.png";
                        } else {
                            if ($ext == "zip") {
                                $res .= "zip.png";
                            }
                        }
                    }
                }
            }
        }
    }
    return $res;
}

?>

<link href="<?php echo Url::to('@web/css/views/files/index.css'); ?>" rel="stylesheet">
<link href="<?php echo Url::to('@web/css/general/buttonAdd/buttonAdd.css'); ?>" rel="stylesheet">
<title>Мои файлы</title>

<li class="list-group-item">Мои файлы</li>
<div class="container" style="margin-top: 20px">
    <div class="row justify-content-center">

        <?php
        if (isset($files)) {
            if (count($files) > 0) {
                foreach ($files as $key => $value) {
                    echo "<div id='" . $value['id'] . "'>
                                    <div class=\"our-team-main\"> 
                                    <div class=\"team-front\">";

                    // Получить расширенную информцию по имени файла
                    $fileInfo = new SplFileInfo($value['file_name']);

                    // Изображение файла
                    echo "<img src=\"" . Url::to('@web' . getPathToIconFile($fileInfo->getExtension())) . "\" />";
                    // Имя файла
                    echo "<h3>" . $value['file_name'] . "</h3>";
                    // Дата-время добавления и размер файла
                    echo " <p>" . $value['datetime_add'] . " (" . formatFileSize($value['file_size_bytes']) . ")</p>";

                    echo "</div>
                                    <div class=\"team-back text-center\">
                                    <button class=\"btn btn-download\" onclick='downloadFile(\"" . $value['path'] . "\")'>Скачать</button>
                                    <button class=\"btn btn-delete\" onclick='removeFile(\"" . $value['file_name'] . "\", " . $value['id'] . ")'>Удалить</button>
                              </div>
                        </div>
                     </div>";
                }
            } else {
                echo "<br/><h4 style='color: gray;'>У вас нет файлов</h4>";
            }
        }
        ?>
    </div>
</div>

<a id="btn_add" data-toggle="modal" data-target="#loadFileModal">
    <div class="img-circle" style="transform-origin: center;">
        <div class="img-circleblock" style="transform-origin: center;"></div>
    </div>
</a>

<div class="modal fade" id="loadFileModal" tabindex="-1" role="dialog" aria-labelledby="loadFileModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Загрузка файла</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="needs-validation" novalidate method="post" action="<?php echo Url::to(['/files/load']); ?>"
                  enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="_csrf" value="<?php echo Yii::$app->request->getCsrfToken(); ?>"/>
                    <div class="custom-file">
                        <input required type="file" class="custom-file-input" id="load_file" name="load_file"
                               aria-describedby="load_file_addon">
                        <label class="custom-file-label" for="load_file_addon">Загрузить файл...</label>
                        <div class="invalid-feedback">
                            Заполните это поле.
                        </div>
                    </div>
                    <label>Доступные расширения: 'pdf',
                        'ppt',
                        'pptx',
                        'rar',
                        'txt',
                        'doc',
                        'docx',
                        'dot',
                        'docm',
                        'dotx',
                        'dotm',
                        'docb',
                        'xls',
                        'xlt',
                        'xlm',
                        'xlsx',
                        'xlsm',
                        'xltx',
                        'xltm',
                        'zip'</label>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-rounded" data-dismiss="modal">Отменить</button>
                    <button id="btnLoad" type="submit" class="btn btn-info btn-rounded" disabled
                            style="background-color: #36BEC3;border-color: #36BEC3;">Загрузить
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<a href="#" title="Вернуться к началу" class="topbutton"><i class="fa fa-chevron-up"></i></a>

<script type="text/javascript" src="<?php echo Url::to('@web/js/views/files/index.js'); ?>"></script>