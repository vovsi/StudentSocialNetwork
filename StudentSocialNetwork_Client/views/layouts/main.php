<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Expose-Headers: *');
header('Access-Control-Allow-Credentials: true');

use yii\helpers\Html;
use app\assets\AppAsset;
use yii\helpers\Url;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?= Yii::$app->language ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css"
          integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
            integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
            crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
            integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
            crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"
            integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T"
            crossorigin="anonymous"></script>
    <script type="text/javascript" src="<?php echo Url::to('@web/js/views/layout/main.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo Url::to('@web/js/general/ajaxFunctions.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo Url::to('@web/js/general/utils.js'); ?>"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css"
          integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
    <link rel="stylesheet" href="<?php echo Url::to('@web/css/views/layout/main.css'); ?>">
    <link href="<?php echo Url::to('@web/css/general/btnUpper/btnUpper.css'); ?>" rel="stylesheet">
</head>
<body class="text-center" style="background-color: whitesmoke">
<div class="container">
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-dark">
        <a id="home" class="" href="<?php echo Url::to(['main/index']); ?>">
            <img src="<?php echo Url::to('@web/resources/main.png'); ?>" width="40" height="40"
                 class="d-inline-block align-top">
            <label style="color: white;font-size: x-large;font-weight: bold">Step Network</label>
        </a>
        <button
                class="navbar-toggler"
                type="button"
                data-toggle="collapse"
                data-target="#navbarCollapse"
                aria-controls="navbarCollapse"
                aria-expanded="false"
                aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarCollapse">
            <?php
            if (isset($this->params['data'])) {
                $data = $this->params['data'];
                if (isset($data['auth_data'])) {
                    $firstName = $data['auth_data']['first_name'];
                    $lastName = $data['auth_data']['last_name'];
                    if (isset($data['profile'])) {
                        $avatarBase64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($data['profile']['photo_path']));
                    } else {
                        $avatarBase64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($this->params['data']['photo_path']));
                    }

                    $id = $data['auth_data']['id'];
                    $countNewGroupMsgs = $data['count_new_group_msgs'] > 0 ? $data['count_new_group_msgs'] : "";
                    echo "<ul class=\"navbar-nav mr-auto sidenav\" style='text-align: left;' id=\"navAccordion\">
                                 <li class=\"nav-item\" style='text-align: center;background-color: #616161'>
                                    <a class=\"nav-link\" href=\"" . Url::to(['/' . $id]) . "\">
                                        <img src='$avatarBase64' class='rounded-circle' height='100px' width=\"100px\" />
                                        <label style='color: white;'>$firstName $lastName</label>
                                    </a>
                                </li>
                                <li class=\"nav-item\" style='margin-left: 5%'>
                                    <a class=\"nav-link\" href=\"" . Url::to(['/news']) . "\"><i class=\"fas fa-newspaper\"></i> Новости</a>
                                </li>
                                <li class=\"nav-item\" style='margin-left: 5%'>
                                    <a class=\"nav-link\" href=\"" . Url::to(['/' . $id]) . "\"><i class=\"fas fa-user-circle\"></i> Профиль</a>
                                </li>
                                <li class=\"nav-item\" style='margin-left: 5%'>
                                    <a class=\"nav-link\" href=\"" . Url::to(['/messages']) . "\"><i class=\"fas fa-envelope\"></i> Сообщения <span id='count_not_viewed_group_msgs' class=\"badge badge-light\">$countNewGroupMsgs</span></a>
                                </li>
                                <li class=\"nav-item\" style='margin-left: 5%'>
                                    <a class=\"nav-link\" href=\"" . Url::to(['/album?id=' . $id]) . "\"><i class=\"fas fa-images\"></i> Aльбом</a>
                                </li>
                                <li class=\"nav-item\" style='margin-left: 5%'>
                                    <a class=\"nav-link\" href=\"" . Url::to(['/files']) . "\"><i class=\"fas fa-copy\"></i> Файлы</a>
                                </li>
                                <li class=\"nav-item\" style='margin-left: 5%'>
                                    <a class=\"nav-link\" href=\"" . Url::to(['/favorites']) . "\"><i class=\"fas fa-star\"></i> Избранные</a>
                                </li>
                                <li class=\"nav-item\" style='margin-left: 5%'>
                                    <a class=\"nav-link\" href=\"" . Url::to(['/settings']) . "\"><i class=\"fas fa-cog\"></i> Настройки</a>
                                </li>";
                    if ($data['auth_data']['role'] == 'admin') {
                        echo "<li class=\"nav-item\" style='margin-left: 5%'>
                                        <a class=\"nav-link\" href=\"" . Url::to(['/adminpanel']) . "\"><i class=\"fas fa-toolbox\"></i> Админ-панель</a>
                                        </li>";
                    }
                    echo "<li class=\"nav-item\" style='margin-left: 5%'>
                                                <a class=\"nav-link \" href=\"" . Url::to(['/help']) . "\"><i class=\"fas fa-question\"></i> Помощь</a>
                                            </li>
                                            <li class=\"nav-item\" style='margin-left: 5%'>
                                                <a class=\"nav-link\" href='" . Url::to(['/main/logout']) . "'><i class=\"fas fa-sign-out-alt\"></i> Выйти</a>
                                                </li>";
                    echo "</ul>";
                } else {
                    echo "<ul class=\"navbar-nav mr-auto sidenav\" id=\"navAccordion\">
<li>
<br /><h3 style='color: white;'>Вход</h3>
</li>
                                        <li class=\"nav-item\" style='margin-left: 5%'>
                                            <form class=\"form-inline\" method=\"post\" action='" . Url::to(['/']) . "'>
                                                <input name=\"_csrf\" type=\"hidden\" value=\"" . Yii::$app->getRequest()->getCsrfToken() . "\"/>
                                                <input class=\"form-control mr-sm-2 btn-rounded\" style='margin-top: 20px' type=\"text\" name='email' id='email' placeholder=\"Email\" aria-label=\"Email\">
                                                <input class=\"form-control mr-sm-2 btn-rounded\" style='margin-top: 10px' type=\"password\" name='password' id='password' placeholder=\"Пароль\" aria-label=\"Пароль\">
                                                <input class='btn btn-light btn-rounded' style='margin-top: 15px' type='submit' value='Войти'>
                                            </form> 
                                        </li>
                                    </ul>";
                }
            }
            ?>
            <?php
            if (isset($data['auth_data'])) {
                echo " <form class=\"form-inline ml-auto mt-2 mt-md-0\" method=\"post\" action=\"" . Url::to(['/search']) . "\">
                                        <input name=\"_csrf\" type=\"hidden\" value=\"" . Yii::$app->getRequest()->getCsrfToken() . "\"/>
                                        <div class=\"input-group\">
                                            <input id=\"query_text\" name=\"query_text\" class=\"form-control btn-rounded\" type=\"text\" placeholder=\"Поиск...\" aria-label=\"Поиск...\">
                                            <div class=\"input-group-append\">
                                                <button class=\"btn btn-light fa fa-search btn-rounded\" title=\"Найти\" id=\"tosearch\" type=\"submit\"></button>
                                            </div>
                                        </div>
                                    </form>";
            }
            ?>
        </div>
    </nav>
</div>

<main class="content-wrapper">
    <div class="container-fluid">

        <?= $content ?>

        <p style="color: #9d9d9d;font-size: 13px;margin-top: 5px">«Step Network»‎ by Vlad Ovsienko - 2019</p>

        <button id="errorsModalBtn" data-toggle="modal" data-target="#errorsModal" style="display:none;"></button>
        <div class="modal fade" id="errorsModal" tabindex="-1" role="dialog" aria-labelledby="errorsModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="errorsModalLabel">Мы выявили следующие ошибки:</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Errors from php query -->
                        <?php
                        if (isset($_SESSION['errors'])) {
                            echo "<script>document.getElementById(\"errorsModalBtn\").click();</script>";
                            $errors = array_unique($_SESSION['errors']);
                            echo "<ul>";
                            foreach ($errors as $key => $value) {
                                echo "<li>$value</li>";
                            }
                            echo "</ul>";
                            $_SESSION['errors'] = null;
                        }
                        ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>

        <button id="errorsAjaxModalBtn" data-toggle="modal" data-target="#errorsAjaxModal"
                style="display:none;"></button>
        <div class="modal fade" id="errorsAjaxModal" tabindex="-1" role="dialog" aria-labelledby="errorsAjaxModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="errorsAjaxModalLabel">Мы выявили следующие ошибки:</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <ul id="errorsAjaxList">
                            <!-- Errors from ajax query -->
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>

        <button id="about_actionModalBtn" data-toggle="modal" data-target="#about_actionModal"
                style="display:none;"></button>
        <div class="modal fade" id="about_actionModal" tabindex="-1" role="dialog"
             aria-labelledby="about_actionModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="errorsModalLabel">Действие успешно выполнено!</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?php
                        if (isset($_SESSION['about_action'])) {
                            echo "<script>document.getElementById(\"about_actionModalBtn\").click();</script>";
                            $about = array_unique($_SESSION['about_action']);
                            echo "<ul>";
                            foreach ($about as $key => $value) {
                                echo "<li>$value</li>";
                            }
                            if (isset($_SESSION['password_reg']) && isset($_SESSION['email_reg'])) {
                                echo "<h4>Email: <span class=\"badge badge-secondary\">" . $_SESSION['email_reg'] . "</span></h4>";
                                echo "<h4>Password: <span class=\"badge badge-secondary\">" . $_SESSION['password_reg'] . "</span></h4>";
                                $_SESSION['email_reg'] = null;
                                $_SESSION['password_reg'] = null;
                            }
                            echo "</ul>";
                            $_SESSION['about_action'] = null;
                        }


                        ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
</body>
</html>