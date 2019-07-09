<?php

use yii\helpers\Url;

?>

<title>API Step Network</title>

<div class="text-center">
    <img src="<?php echo Url::to('@web/resources/main.ico'); ?>" style="height: 50px;" class="text-center"/>
</div>

<h1 class="text-center">Welcome to API Step Network!</h1>

<div class="accordion" id="accordionExample">
    <div class="alert text-center" style="background-color: #e2e2e2" role="alert">
        <h5>Controllers (v1)</h5>
    </div>
    <div class="card">

        <div class="card-header text-center" id="headingMain">
            <h5 class="mb-0">
                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseMain"
                        aria-expanded="true" aria-controls="collapseMain">
                    <h5>main</h5>
                </button>
            </h5>
        </div>
        <div id="collapseMain" class="collapse" aria-labelledby="headingMain" data-parent="#accordionExample">
            <div class="card-body">
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">GET</span> auth (<code><span>/v1/main/auth</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Авторизация аккаунта.</p>
                    <hr/>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre style="align-content: left">
{
    "auth_data": {
        "id": 6,
        "first_name": "Влад",
        "last_name": "Овсиенко",
        "patronymic": "Виталиевич",
        "email": "admin@mail.ru",
        "personal_info_id": 7,
        "blocked": 0,
        "role": "admin",
        "password": "$2y$10$ieHd7MJIr2Z5k2K68gLg4u7.KmEykGUQU7tIWO2W400LSHIaHSxMG",
        "settings_id": 7,
        "group_id": 9
    },
    "profile": {
        "first_name": "Влад",
        "last_name": "Овсиенко",
        "patronymic": "Виталиевич",
        "email": "admin@mail.ru",
        "blocked": 0,
        "role": "admin",
        "gender": "Мужской",
        "phone_number": "+380111111111",
        "activities": "Студент.",
        "interests": "",
        "about_me": "",
        "photo_path": "db/photos/profile6.jpg",
        "date_birthday": {
            "date": "1998-01-14 00:00:00.000000",
            "timezone_type": 3,
            "timezone": "Europe/Kiev"
        }
    },
    "count_new_msgs": 0
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">GET</span> getbase64fromurlimage
                        (<code><span>/v1/main/getbase64fromurlimage</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Получить base64 строку, отправляя ссылку на ресурс,
                    который находится на сервере.</p>
                    <hr/>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>url</td>
                            <td>string</td>
                            <td>Ссылка на ресурс (пример: http://localhost/db/photos/profile6.jpg</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre style="align-content: left">
"data:image/jpeg;base64,/9j/4A..."
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">GET</span> getpollanswervoted
                        (<code><span>/v1/main/getpollanswervoted</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Получить массив проголосовавших пользователей за вариант
                    ответа в опросе.</p>
                    <hr/>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>pollAnswerId</td>
                            <td>int</td>
                            <td>Id варианта ответа в опросе</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre style="align-content: left">
{
    "status": "OK",
    "votedAccounts": [
        {
            "id": 27,
            "first_name": "Лилия",
            "last_name": "Сизый",
            "patronymic": "Алексеевна",
            "email": "user6@mail.ru",
            "personal_info_id": 28,
            "blocked": 0,
            "role": "user",
            "password_hash": "13c6832977ea42d4ef7446621a855edf",
            "settings_id": 28,
            "group_id": 6,
            "status_visit": "offline",
            "photo_path": "http://localhost/db/photos/profile27.jpg"
        }
    ]
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">GET</span> checkauth (<code><span>/v1/main/checkauth</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Проверка авторизации.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre style="align-content: left">
{
    "auth_data": {
        "id": 6,
        "first_name": "Влад",
        "last_name": "Овсиенко",
        "patronymic": "Витальевич",
        "email": "admin@mail.ru",
        "personal_info_id": 7,
        "blocked": 0,
        "role": "admin",
        "password_hash": "dde464252a875322659d412d3b5411e9",
        "settings_id": 7,
        "group_id": 9
    },
    "count_new_group_msgs": 0,
    "photo_path": "http://localhost/db/photos/profile6.jpg"
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">GET</span> getprofiletouser
                        (<code><span>/v1/main/getprofiletouser</span></code>)</h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Получить данные профиля.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>id</td>
                            <td>int</td>
                            <td>id пользователя</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
{
    "profile": {
        "id": 6,
        "first_name": "Влад",
        "last_name": "Овсиенко",
        "patronymic": "Витальевич",
        "email": "admin@mail.ru",
        "blocked": 0,
        "role": "admin",
        "group": "Администрация",
        "gender": "Мужской",
        "phone_number": "+380111111111",
        "activities": "Студент.",
        "interests": "Программирование...",
        "about_me": "Здесь должна быть информация обо мне.",
        "photo_path": "http://localhost/db/photos/profile6.jpg",
        "date_birthday": {
            "date": "1998-01-14 00:00:00.000000",
            "timezone_type": 3,
            "timezone": "Europe/Kiev"
        },
        "status_visit": "online"
    }
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">GET</span> getposts (<code><span>/v1/main/getposts</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Получить записи пользователя.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>id</td>
                            <td>int</td>
                            <td>id пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>limit</td>
                            <td>int</td>
                            <td>Кол-во записей</td>
                        </tr>
                        <tr class="table-light">
                            <td>offset</td>
                            <td>int</td>
                            <td>Отступ в списке записей</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
{
    "is_there_more_posts": false,
    "posts": [
        {
            "id": 245,
            "id_FROM": 6,
            "first_name_FROM": "Влад",
            "last_name_FROM": "Овсиенко",
            "photo_FROM": "http://localhost/db/photos/profile6.jpg",
            "status_visit_FROM": "online",
            "datetime_add": "2019-07-02 17:35",
            "message": "Создаю опрос! &amp;1:12 &amp;2:23 Пиуу &amp;5:55 ",
            "path_to_image": "http://localhost/db/photos/post245.jpg",
            "id_TO": "6",
            "video_link": null,
            "files": [],
            "poll": {
                "id": 35,
                "theme": "Как дела?",
                "anon": 0,
                "answers": [
                    {
                        "id": 69,
                        "poll_id": 35,
                        "answer": "Плохо",
                        "votes": 0
                    },
                    {
                        "id": 70,
                        "poll_id": 35,
                        "answer": "Неплохо",
                        "votes": 2
                    },
                    {
                        "id": 71,
                        "poll_id": 35,
                        "answer": "Хорошо!",
                        "votes": 0
                    },
                    {
                        "id": 72,
                        "poll_id": 35,
                        "answer": "Идеально!",
                        "votes": 1
                    }
                ]
            },
            "poll_voted": [
                {
                    "id": 117,
                    "account_id": 6,
                    "poll_id": 35,
                    "answer_id": 70
                },
                {
                    "id": 118,
                    "account_id": 27,
                    "poll_id": 35,
                    "answer_id": 72
                },
                {
                    "id": 121,
                    "account_id": 28,
                    "poll_id": 35,
                    "answer_id": 70
                }
            ]
        }
    ]
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">GET</span> getcountnotviewedgroupmsgs
                        (<code><span>/v1/main/getcountnotviewedgroupmsgs</span></code>)</h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Получить кол-во непрочитанных диалогов и бесед.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
{
    "count_new_dialogs_msgs": 0,
    "count_new_conversations_msgs": 0
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> addpost (<code><span>/v1/main/addpost</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Добавить новую запись на страницу.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>account_to_id</td>
                            <td>int</td>
                            <td>id пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>message</td>
                            <td>string</td>
                            <td>Текст записи</td>
                        </tr>
                        <tr class="table-light">
                            <td>image</td>
                            <td>string</td>
                            <td>Файл изображения закодированное в base64 (gif, png, jpg, jpeg) (опционально)</td>
                        </tr>
                        <tr class="table-light">
                            <td>video_link</td>
                            <td>string</td>
                            <td>Часть ссылки на видео из YouTube Пример: https://www.youtube.com/watch?v=5gyvnQnvAlI -->
                                5gyvnQnvAlI (опционально)
                            </td>
                        </tr>
                        <tr class="table-light">
                            <td>files</td>
                            <td>string</td>
                            <td>Id файлов (разделитель - "|") (опционально)</td>
                        </tr>
                        <tr class="table-light">
                            <td>poll_theme</td>
                            <td>string</td>
                            <td>Тема опроса (опционально, если другие поля опроса не отправляются)</td>
                        </tr>
                        <tr class="table-light">
                            <td>poll_answers</td>
                            <td>array string</td>
                            <td>Варианты ответов опроса (опционально, если другие поля опроса не отправляются)</td>
                        </tr>
                        <tr class="table-light">
                            <td>poll_anon</td>
                            <td>string</td>
                            <td>Анонимный опрос? (true/false) (опционально, если другие поля опроса не отправляются)
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
{
    "status": "OK",
    "post": {
        "id": 247,
        "account_from_id": 6,
        "account_to_id": 6,
        "datetime_add": "2019-07-03 12:17",
        "message": "123",
        "path_to_image": null,
        "video_link": null,
        "files": [],
        "poll": [],
        "first_name_fromUser": "Влад",
        "last_name_fromUser": "Овсиенко",
        "photo_path_fromUser": "http://localhost/db/photos/profile6.jpg"
    }
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> removepost (<code><span>/v1/main/removepost</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Удалить запись со страницы.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>idPost</td>
                            <td>int</td>
                            <td>id записи</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
                        {
    "status": "OK"
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> updatephoto (<code><span>/v1/main/updatephoto</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Обновить изображение профиля.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>file</td>
                            <td>string</td>
                            <td>Файл изображения закодированное в base64 (gif, png, jpg, jpeg) (опционально)</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
                        {
    "status": "OK"
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> addblacklist (<code><span>/v1/main/addblacklist</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Добавить пользователя в чёрный список.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>id</td>
                            <td>int</td>
                            <td>id пользователя которого нужно добавить в ЧС</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
                        {
    "status": "OK"
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> removeblacklist
                        (<code><span>/v1/main/removeblacklist</span></code>)</h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Удалить пользователя из чёрного списка.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>id</td>
                            <td>int</td>
                            <td>id пользователя которого нужно удалить из ЧС</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
                        {
    "status": "OK"
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> cancelvotepoll
                        (<code><span>/v1/main/cancelvotepoll</span></code>)</h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Удалить голос из опроса.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>post_id</td>
                            <td>int</td>
                            <td>id записи</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
                        {
    "status": "OK",
	"poll": [
		"id": 1,
		"theme": "Test",
		"anon": 0,
		"answers": [
			[
				"id": 1,
				"poll_id": 1,
				"answer": "Text",
				"votes": 0
			]
		]
	]
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> votepoll
                        (<code><span>/v1/main/votepoll</span></code>)</h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Проголосовать в опросе.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>post_id</td>
                            <td>int</td>
                            <td>id записи</td>
                        </tr>
                        <tr class="table-light">
                            <td>answer_id</td>
                            <td>int</td>
                            <td>id варианта ответа за который проголосовал пользователь</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
                        {
    "status": "OK",
	"poll": [
		"id": 1,
		"theme": "Test",
		"anon": 0,
		"answers": [
			[
				"id": 1,
				"poll_id": 1,
				"answer": "Text",
				"votes": 1
			]
		]
	]
}
                    </pre>
                </div>
            </div>
        </div>


        <div class="card-header text-center" id="headingAdminpanel">
            <h5 class="mb-0">
                <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                        data-target="#collapseAdminpanel" aria-expanded="true" aria-controls="collapseAdminpanel">
                    <h5>adminpanel</h5>
                </button>
            </h5>
        </div>
        <div id="collapseAdminpanel" class="collapse" aria-labelledby="headingAdminpanel"
             data-parent="#accordionExample">
            <div class="card-body">
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">GET</span> getgroups (<code><span>/v1/adminpanel/getgroups</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Получить список групп.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre style="align-content: left">
{
    "groups": [
        "Администрация",
        "ЕКО 15-П-1",
        "ЕКО 15-П-2",
        "ЕКО 80-13",
        "Преподаватели",
        "РЕК 1-А-2",
        "РЕК 2-А-1",
        "РЕТ-32",
        "ФОТ 3-1-3",
        "ФОТ 3-1-4"
    ],
    "errors": []
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">GET</span> getadmins (<code><span>/v1/adminpanel/getadmins</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Получить список администраторов.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre style="align-content: left">
{
    "admins": [
        {
            "id": 6,
            "first_name": "Влад",
            "last_name": "Овсиенко",
            "patronymic": "Витальевич",
            "email": "admin@mail.ru",
            "personal_info_id": 7,
            "blocked": 0,
            "role": "admin",
            "password_hash": "dde464252a875322659d412d3b5411e9",
            "settings_id": 7,
            "group_id": 9
        }
    ],
    "errors": []
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> registrationaccount
                        (<code><span>/v1/adminpanel/registrationaccount</span></code>)</h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Зарегистрировать (создать) аккаунт. Генерирует случайный
                    пароль на выходные данные.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>first_name</td>
                            <td>string</td>
                            <td>Имя</td>
                        </tr>
                        <tr class="table-light">
                            <td>last_name</td>
                            <td>string</td>
                            <td>Фамилия</td>
                        </tr>
                        <tr class="table-light">
                            <td>patronymic</td>
                            <td>string</td>
                            <td>Отчество</td>
                        </tr>
                        <tr class="table-light">
                            <td>group</td>
                            <td>string</td>
                            <td>Группа</td>
                        </tr>
                        <tr class="table-light">
                            <td>role</td>
                            <td>string</td>
                            <td>Роль</td>
                        </tr>
                        <tr class="table-light">
                            <td>gender</td>
                            <td>string</td>
                            <td>Пол (Мужской, Женский)</td>
                        </tr>
                        <tr class="table-light">
                            <td>email</td>
                            <td>string</td>
                            <td>Email-адрес</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre style="align-content: left">
                        {
    "status": "OK",
    "password": "Hf29Kjf3f"
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> blockaccount (<code><span>/v1/adminpanel/blockaccount</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Заблокировать аккаунт (пользователя).</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>email</td>
                            <td>string</td>
                            <td>Email-адрес кого заблокировать</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
					{
  "status": "OK"
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> unblockaccount
                        (<code><span>/v1/adminpanel/unblockaccount</span></code>)</h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Разблокировать аккаунт (пользователя).</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>email</td>
                            <td>string</td>
                            <td>Email-адрес кого разблокировать</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
                        {
  "status": "OK"
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> creategroup (<code><span>/v1/adminpanel/creategroup</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Создать группу.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>name</td>
                            <td>string</td>
                            <td>Имя группы</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
                        {
  "status": "OK"
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> renamegroup (<code><span>/v1/adminpanel/renamegroup</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Переименовать группу.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>oldName</td>
                            <td>string</td>
                            <td>Старое имя группы</td>
                        </tr>
                        <tr class="table-light">
                            <td>newName</td>
                            <td>string</td>
                            <td>Новое имя группы</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
                        {
  "status": "OK"
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> moveusertodiferentgroup
                        (<code><span>/v1/adminpanel/moveusertodiferentgroup</span></code>)</h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Переместить пользователя в другую группу.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>userId</td>
                            <td>string</td>
                            <td>ID пользователя которого нужно переместить</td>
                        </tr>
                        <tr class="table-light">
                            <td>nameGroup</td>
                            <td>string</td>
                            <td>Имя группы</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
                        {
  "status": "OK"
}
                    </pre>
                </div>
            </div>
        </div>


        <div class="card-header text-center" id="headingAlbum">
            <h5 class="mb-0">
                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseAlbum"
                        aria-expanded="true" aria-controls="collapseAlbum">
                    <h5>album</h5>
                </button>
            </h5>
        </div>
        <div id="collapseAlbum" class="collapse" aria-labelledby="headingAlbum" data-parent="#accordionExample">
            <div class="card-body">
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">GET</span> getalbum (<code><span>/v1/album/getalbum</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Получить список фото пользователя в альбоме.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>id</td>
                            <td>int</td>
                            <td>id пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>limit</td>
                            <td>int</td>
                            <td>Кол-во записей</td>
                        </tr>
                        <tr class="table-light">
                            <td>offset</td>
                            <td>int</td>
                            <td>Отступ в записях</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
                        {
    "photos": [
        {
            "id": 33,
            "description": "Пейзаж (1)",
            "datetime_add": {
                "date": "2018-08-20 08:21:44.000000",
                "timezone_type": 3,
                "timezone": "Europe/Berlin"
            },
            "account_id": 6,
            "path": "http://127.0.0.1/db/photos/photo33.jpg"
        }
    ],
    "errors": []
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> add (<code><span>/v1/album/add</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Добавить фото в свой альбом.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>file</td>
                            <td>string</td>
                            <td>Файл изображения закодированное в base64 (gif, png, jpg, jpeg)</td>
                        </tr>
                        <tr class="table-light">
                            <td>description</td>
                            <td>string</td>
                            <td>Описание</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
                        {
    "status": "OK"
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> remove (<code><span>/v1/album/remove</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Удалить фото из своего альбома.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>id</td>
                            <td>int</td>
                            <td>id фото</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
                        {
    "status": "OK"
}
                    </pre>
                </div>
            </div>
        </div>

        <div class="card-header text-center" id="headingFiles">
            <h5 class="mb-0">
                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseFiles"
                        aria-expanded="true" aria-controls="collapseAlbum">
                    <h5>files</h5>
                </button>
            </h5>
        </div>
        <div id="collapseFiles" class="collapse" aria-labelledby="headingFiles" data-parent="#accordionExample">
            <div class="card-body">
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">GET</span> getfiles (<code><span>/v1/files/getfiles</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Получить список файлов пользователя.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
{
    "files": [
        {
            "id": 12,
            "account_id": 6,
            "file_name": "PhpStorm-2018.2-key.zip",
            "datetime_add": "2019-03-17 18:59",
            "path": "http://localhost/db/files/6/PhpStorm-2018.2-key.zip",
            "file_size_bytes": 782500
        }
    ],
    "errors": []
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">GET</span> getfile (<code><span>/v1/files/getfile</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Получить файл пользователя.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>idUser</td>
                            <td>int</td>
                            <td>id пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>id</td>
                            <td>int</td>
                            <td>id файла</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
{
    "files": [
        {
            "id": 12,
            "account_id": 6,
            "file_name": "PhpStorm-2018.2-key.zip",
            "datetime_add": "2019-03-17 18:59",
            "path": "http://localhost/db/files/6/PhpStorm-2018.2-key.zip",
            "file_size_bytes": 782500
        }
    ],
    "errors": []
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> load (<code><span>/v1/files/load</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Добавить файл в список файлов пользователя.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>file</td>
                            <td>string</td>
                            <td>Файл закодированный в base64</td>
                        </tr>
                        <tr class="table-light">
                            <td>fileName</td>
                            <td>string</td>
                            <td>Имя файла (с расширением)</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
                        {
    "status": "OK"
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> remove (<code><span>/v1/files/remove</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Удалить файл из своего хранилища.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>fileName</td>
                            <td>string</td>
                            <td>Название файла (с расширением)</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
                        {
    "status": "OK"
}
                    </pre>
                </div>
            </div>
        </div>


        <div class="card-header text-center" id="headingFavorites">
            <h5 class="mb-0">
                <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                        data-target="#collapseFavorites" aria-expanded="true" aria-controls="collapseFavorites">
                    <h5>favorites</h5>
                </button>
            </h5>
        </div>
        <div id="collapseFavorites" class="collapse" aria-labelledby="headingFavorites" data-parent="#accordionExample">
            <div class="card-body">
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">GET</span> getmyfavorites
                        (<code><span>/v1/favorites/getmyfavorites</span></code>)</h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Получить список моих избранных пользователей.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>limit</td>
                            <td>int</td>
                            <td>Кол-во записей</td>
                        </tr>
                        <tr class="table-light">
                            <td>offset</td>
                            <td>int</td>
                            <td>Отступ в записях</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
                        {
    "favorites": [
        {
            "user_favorite_id": 21,
            "first_name": "Влад",
            "last_name": "Которович",
            "photo_path": "http://127.0.0.1/db/photos/profile21.jpg",
            "status_visit": "online"
        }
    ],
    "errors": []
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> add (<code><span>/v1/favorites/add</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Добавить пользователя в избранные.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>id</td>
                            <td>int</td>
                            <td>id пользователя которого нужно добавить</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
                        {
    "status": "OK"
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> remove (<code><span>/v1/favorites/remove</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Удалить пользователя из списка избранных.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>id</td>
                            <td>int</td>
                            <td>id пользователя которого нужно удалить</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
                        {
    "status": "OK"
}
                    </pre>
                </div>
            </div>
        </div>


        <div class="card-header text-center" id="headingMessages">
            <h5 class="mb-0">
                <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                        data-target="#collapseMessages" aria-expanded="true" aria-controls="collapseMessages">
                    <h5>messages</h5>
                </button>
            </h5>
        </div>
        <div id="collapseMessages" class="collapse" aria-labelledby="headingMessages" data-parent="#accordionExample">
            <div class="card-body">
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">GET</span> getdialogs (<code><span>/v1/messages/getdialogs</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Получить список моих диалогов.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>limit</td>
                            <td>int</td>
                            <td>Кол-во записей</td>
                        </tr>
                        <tr class="table-light">
                            <td>offset</td>
                            <td>int</td>
                            <td>Отступ в записях</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
{
    "auth_data": {
        "id": 6,
        "first_name": "Влад",
        "last_name": "Овсиенко",
        "patronymic": "Витальевич",
        "email": "admin@mail.ru",
        "personal_info_id": 7,
        "blocked": 0,
        "role": "admin",
        "password_hash": "dde464252a875322659d412d3b5411e9",
        "settings_id": 7,
        "group_id": 9
    },
    "count_new_group_msgs": 0,
    "photo_path": "http://localhost/db/photos/profile6.jpg",
    "is_there_more": false,
    "dialogs": [
        {
            "dialog_id": 18,
            "interlocutor_id": 27,
            "interlocutor_image": "http://localhost/db/photos/profile27.jpg",
            "interlocutor_first_name": "Лилия",
            "interlocutor_last_name": "Сизый",
            "interlocutor_status_visit": "offline",
            "interlocutor_group": "ФОТ 3-1-3",
            "last_message": "",
            "last_message_photo": "",
            "last_message_files": "22|",
            "last_message_videoYT": "",
            "last_message_viewed": 0,
            "sender_point": "Вы:&nbsp;",
            "date_change": "2019-07-03 11:46",
            "count_new_messages": 0
        }
    ]
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">GET</span> getmembersofconversation
                        (<code><span>/v1/messages/getmembersofconversation</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Получить массив участников беседы.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>limit</td>
                            <td>int</td>
                            <td>Кол-во записей</td>
                        </tr>
                        <tr class="table-light">
                            <td>offset</td>
                            <td>int</td>
                            <td>Отступ в записях</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
{
    "members": [
        {
            "id": 6,
            "first_name": "Влад",
            "last_name": "Овсиенко",
            "patronymic": "Витальевич",
            "email": "admin@mail.ru",
            "personal_info_id": 7,
            "blocked": 0,
            "role": "admin",
            "password_hash": "dde464252a875322659d412d3b5411e9",
            "settings_id": 7,
            "group_id": 9,
            "status_member": "creator",
            "gender": "Мужской",
            "phone_number": "+380111111111",
            "activities": "Студент.",
            "interests": "Программирование...",
            "about_me": "Здесь должна быть информация обо мне.",
            "photo_path": "http://localhost/db/photos/profile6.jpg",
            "date_birthday": {
                "date": "1998-01-14 00:00:00.000000",
                "timezone_type": 3,
                "timezone": "Europe/Kiev"
            },
            "status_visit": "online"
        },
        {
            "id": 42,
            "first_name": "Администратор",
            "last_name": "SSN",
            "patronymic": "-",
            "email": "admin-main@mail.ru",
            "personal_info_id": 43,
            "blocked": 0,
            "role": "admin",
            "password_hash": "dde464252a875322659d412d3b5411e9",
            "settings_id": 43,
            "group_id": 9,
            "gender": "Мужской",
            "phone_number": "",
            "activities": "Администратор сайта StudentSocialNetwork.",
            "interests": "Bag fixes, site improvement.",
            "about_me": "Отвечаю на вопросы студентов/преподавателей, принимаю пожелания для улучшения сайта.",
            "photo_path": "http://localhost/db/photos/profile42.jpg",
            "date_birthday": {
                "date": "1970-01-01 00:00:00.000000",
                "timezone_type": 3,
                "timezone": "Europe/Kiev"
            },
            "status_visit": "offline"
        }
    ]
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">GET</span> getconversations
                        (<code><span>/v1/messages/getconversations</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Получить список бесед, в которых я учавствую.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>limit</td>
                            <td>int</td>
                            <td>Кол-во записей</td>
                        </tr>
                        <tr class="table-light">
                            <td>offset</td>
                            <td>int</td>
                            <td>Отступ в записях</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
{
    "auth_data": {
        "id": 6,
        "first_name": "Влад",
        "last_name": "Овсиенко",
        "patronymic": "Витальевич",
        "email": "admin@mail.ru",
        "personal_info_id": 7,
        "blocked": 0,
        "role": "admin",
        "password_hash": "dde464252a875322659d412d3b5411e9",
        "settings_id": 7,
        "group_id": 9
    },
    "count_new_group_msgs": 0,
    "photo_path": "http://localhost/db/photos/profile6.jpg",
    "is_there_more": false,
    "conversations": [
        {
            "conversation_id": 1,
            "conversation_name": "Беседа",
            "conversation_photo": "http://localhost/db/photos/conversation.jpg",
            "sender_id": 6,
            "sender_first_name": "Влад",
            "last_message": "",
            "last_message_photo": "",
            "last_message_files": "24|",
            "last_message_videoYT": "",
            "last_message_viewed": 0,
            "sender_point": "Вы:&nbsp;",
            "date_change": "2019-07-03 08:21",
            "date_change_full": {
                "date": "2019-07-03 08:21:57.000000",
                "timezone_type": 3,
                "timezone": "Europe/Kiev"
            },
            "count_new_messages": 0
        }
    ]
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">GET</span> getdialog (<code><span>/v1/messages/getdialog</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Получить список сообщений диалога.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>id</td>
                            <td>int</td>
                            <td>id диалога</td>
                        </tr>
                        <tr class="table-light">
                            <td>limit</td>
                            <td>int</td>
                            <td>Кол-во записей</td>
                        </tr>
                        <tr class="table-light">
                            <td>offset</td>
                            <td>int</td>
                            <td>Отступ в записях</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
{
    "favorite": false,
    "black_list": false,
    "recipient_id": 27,
    "recipient_status_visit": "offline",
    "recipient_photo_path": "http://localhost/db/photos/profile27.jpg",
    "recipient_first_name": "Лилия",
    "recipient_last_name": "Сизый",
    "messages": [
        {
            "sender_id": 6,
            "sender_photo": "http://localhost/db/photos/profile6.jpg",
            "sender_first_name": "Влад",
            "sender_last_name": "Овсиенко",
            "message_text": "",
            "message_photo_path": "",
            "files": [
                {
                    "id": 22,
                    "account_id": 6,
                    "file_name": "Тут циферки .xlsx",
                    "datetime_add": "2019-07-02 17:51",
                    "path": "http://localhost/db/files/6/Тут циферки .xlsx",
                    "file_size_bytes": 8408
                }
            ],
            "videoYT": "",
            "date_send": "2019-07-03 11:46",
            "viewed": 0
        }
    ],
    "is_there_more": false
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">GET</span> getconversation
                        (<code><span>/v1/messages/getconversation</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Получить список сообщений беседы.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>id</td>
                            <td>int</td>
                            <td>id беседы</td>
                        </tr>
                        <tr class="table-light">
                            <td>limit</td>
                            <td>int</td>
                            <td>Кол-во записей</td>
                        </tr>
                        <tr class="table-light">
                            <td>offset</td>
                            <td>int</td>
                            <td>Отступ в записях</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
{
    "id": 1,
    "account_author_id": 6,
    "name": "Беседа",
    "created": "2019-07-03 08:21",
    "photo_path": "http://localhost/db/photos/conversation.jpg",
    "your_status": "creator",
    "messages": [
        {
            "sender_id": 6,
            "sender_photo": "http://localhost/db/photos/profile6.jpg",
            "sender_first_name": "Влад",
            "sender_last_name": "Овсиенко",
            "message_text": "Test",
            "message_photo_path": "",
            "files": [],
            "videoYT": "",
            "date_send": "2019-07-03 12:59",
            "viewed": 0
        }
    ],
    "is_there_more": false
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">GET</span> getnewmessagesfromdialog
                        (<code><span>/v1/messages/getnewmessagesfromdialog</span></code>)</h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Получить новые (непрочитанные) сообщения диалога.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>id</td>
                            <td>int</td>
                            <td>id диалога</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
{
    "new_messages": [],
    "favorite": false,
    "black_list": false,
    "recipient_id": 27,
    "recipient_status_visit": "offline",
    "recipient_photo_path": "http://localhost/db/photos/profile27.jpg",
    "recipient_first_name": "Лилия",
    "recipient_last_name": "Сизый"
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">GET</span>
                        getnewmessagesfromconversation
                        (<code><span>/v1/messages/getnewmessagesfromconversation</span></code>)</h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Получить новые (непрочитанные) сообщения беседы.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>id</td>
                            <td>int</td>
                            <td>id беседы</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
{
    "new_messages": []
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> sendtodialog (<code><span>/v1/messages/sendtodialog</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Отправить сообщение пользователю (в диалог).</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>account_to_id</td>
                            <td>int</td>
                            <td>id пользователя которому отправляется сообщение</td>
                        </tr>
                        <tr class="table-light">
                            <td>message</td>
                            <td>string</td>
                            <td>Текст сообщения</td>
                        </tr>
                        <tr class="table-light">
                            <td>image</td>
                            <td>string</td>
                            <td>Файл изображения закодированное в base64 (gif, png, jpg, jpeg) (опционально)</td>
                        </tr>
                        <tr class="table-light">
                            <td>files</td>
                            <td>string</td>
                            <td>Файлы (id разделенные знаком "|") (опционально)</td>
                        </tr>
                        <tr class="table-light">
                            <td>videoYT</td>
                            <td>string</td>
                            <td>Ссылка на ютую видео. Пример: https://www.youtube.com/watch?v=eME64iXQeMM(опционально)
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
{
    "status": "OK",
    "message": {
        "id": 563,
        "dialog_id": 18,
        "sender_id": 6,
        "sender_first_name": "Влад",
        "sender_last_name": "Овсиенко",
        "sender_photo_path": "http://localhost/db/photos/profile6.jpg",
        "recipient_id": 27,
        "text": "Test",
        "photo_path": "",
        "files": [],
        "videoYT": "",
        "date_send": "2019-07-03 12:57",
        "viewed": 0
    }
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> sendtoconversation (<code><span>/v1/messages/sendtoconversation</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Отправить сообщение в беседу.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>conversation_id</td>
                            <td>int</td>
                            <td>id беседы</td>
                        </tr>
                        <tr class="table-light">
                            <td>message</td>
                            <td>string</td>
                            <td>Текст сообщения</td>
                        </tr>
                        <tr class="table-light">
                            <td>image</td>
                            <td>string</td>
                            <td>Файл изображения закодированное в base64 (gif, png, jpg, jpeg) (опционально)</td>
                        </tr>
                        <tr class="table-light">
                            <td>files</td>
                            <td>string</td>
                            <td>Файлы (id разделенные знаком "|") (опционально)</td>
                        </tr>
                        <tr class="table-light">
                            <td>videoYT</td>
                            <td>string</td>
                            <td>Ссылка на ютую видео. Пример: https://www.youtube.com/watch?v=eME64iXQeMM(опционально)
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
{
    "status": "OK",
    "message": {
        "id": 564,
        "conversation_id": 1,
        "sender_id": 6,
        "sender_first_name": "Влад",
        "sender_last_name": "Овсиенко",
        "sender_photo_path": "http://localhost/db/photos/profile6.jpg",
        "text": "Test",
        "photo_path": "",
        "files": [],
        "videoYT": "",
        "date_send": "2019-07-03 12:59",
        "viewed": 0
    }
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> createconversation (<code><span>/v1/messages/createconversation</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Создать беседу.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>name</td>
                            <td>int</td>
                            <td>Название беседы</td>
                        </tr>
                        <tr class="table-light">
                            <td>members</td>
                            <td>string</td>
                            <td>Id участников беседы (разделитель - "|")</td>
                        </tr>
                        <tr class="table-light">
                            <td>image_base64</td>
                            <td>string</td>
                            <td>Файл изображения закодированное в base64 (gif, png, jpg, jpeg) (опционально)</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
{
    "status": "OK",
    "conversation_id": "2"
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> removeсonversation (<code><span>/v1/messages/removeсonversation</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Удалить беседу.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>id</td>
                            <td>int</td>
                            <td>Id беседы</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
{
    "status": "OK"
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> renameсonversation (<code><span>/v1/messages/renameсonversation</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Переименовать беседу.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>id</td>
                            <td>int</td>
                            <td>Id беседы</td>
                        </tr>
                        <tr class="table-light">
                            <td>name</td>
                            <td>string</td>
                            <td>Новое название</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
{
    "status": "OK"
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> refreshphotoconversation (<code><span>/v1/messages/refreshphotoconversation</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Обновить фото беседы.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>id</td>
                            <td>int</td>
                            <td>Id беседы</td>
                        </tr>
                        <tr class="table-light">
                            <td>photoBase64</td>
                            <td>string</td>
                            <td>Новое фото (в формате base64)</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
{
    "status": "OK"
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> leaveconversation
                        (<code><span>/v1/messages/leaveconversation</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Покинуть беседу.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>id</td>
                            <td>int</td>
                            <td>Id беседы</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
{
    "status": "OK"
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> changemembersconversation
                        (<code><span>/v1/messages/changemembersconversation</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Изменить список участников беседы.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>conversationId</td>
                            <td>int</td>
                            <td>Id беседы</td>
                        </tr>
                        <tr class="table-light">
                            <td>members</td>
                            <td>int</td>
                            <td>Id участников беседы (обновленный) (разделитель - "|")</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
{
    "status": "OK",
    "conversation_id": 1
}
                    </pre>
                </div>
            </div>
        </div>


        <div class="card-header text-center" id="headingNews">
            <h5 class="mb-0">
                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseNews"
                        aria-expanded="true" aria-controls="collapseNews">
                    <h5>news</h5>
                </button>
            </h5>
        </div>
        <div id="collapseNews" class="collapse" aria-labelledby="headingNews" data-parent="#accordionExample">
            <div class="card-body">
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">GET</span> getnews (<code><span>/v1/news/getnews</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Получить список новостей.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>limit</td>
                            <td>int</td>
                            <td>Кол-во записей</td>
                        </tr>
                        <tr class="table-light">
                            <td>offset</td>
                            <td>int</td>
                            <td>Отступ в записях</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
{
    "is_there_more": true,
    "news": [
        {
            "id": 88,
            "author_id": 6,
            "theme": "Заработок в Европе все более и более становится привлекательным для украинцев. ",
            "description": "Зарплата в евро, возможность в короткий срок существенно нарастить капитал, накопить деньги на покупку жилья и авто.\r\nНо как получить легальные документы и хорошую работу? .... а языковой барьер?\r\n\r\nТЕПЕРЬ это все не проблема, ведь мы открываем программу &quot;Окно в Европу&quot;. Этот проект создан для интеграции и трудоустройства украинцев в Европейские IT-компании.\r\n\r\nНЕ УПУСТИ СВОЙ ШАНС!\r\nПриходи на День открытых дверей, посвященный проекту &quot;Окно в Европу&quot; с участием представителей Академии ШАГ из Польши и Чехии.\r\n\r\nВ программе мероприятия:\r\n- Презентация проекта &quot;Окно в Европу&quot;\r\n- Способы получения карты и вида на жительство\r\n- Советы для будущих иностранцев\r\n- IT компании для трудоустройства в Европе\r\n- Какие навыки требуются в IT вакансиях стран ЕС\r\n- Презентация образовательных программ\r\n- Индивидуальные консультации",
            "image_path": "http://localhost/db/photos/news88.jpg",
            "datetime_add": "2019-07-02 19:11",
            "video_link": null,
            "event_date": "2019-07-15 10:00",
            "event_description": "ул. Телевизионная, 4а (конференц зал)",
            "poll": {
                "id": 36,
                "theme": "Придешь?",
                "anon": 0,
                "answers": [
                    {
                        "id": 73,
                        "poll_id": 36,
                        "answer": "Да",
                        "votes": 0
                    },
                    {
                        "id": 74,
                        "poll_id": 36,
                        "answer": "Нет",
                        "votes": 1
                    }
                ]
            }
        }
    ],
    "errors": []
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">GET</span> getonenews (<code><span>/v1/news/getonenews</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Получить одну новость (по ID).</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>id</td>
                            <td>int</td>
                            <td>id новости</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
{
    "news": {
        "id": 94,
        "author_id": 6,
        "theme": "График консультаций",
        "description": "ВТ. 18:00 – 19:30 ул. Телевизионная 4а,  ауд. 209. Педько Евгений Петрович  &quot;Основы Информационных технологий, Конфигурирование Windows 10, Безопасность компьютерных сетей и систем, Коммутация\r\nв локальных сетях, базовый 7.0.0.,Коммутация в локальных сетях, базовый SWITCH1, Коммутация в локальных сетях, расширенный,Коммутация в локальных сетях, расширенный 7.0.0., Маршрутизация в IP сетях, базовый,Маршрутизация в IP сетях, расширенный, Маршрутизация в IP сетях, расширенный 7.0.0.,Прикладные протоколы и службы стека TCP/IP, Прикладные протоколы и службы стека TCP/IP 7.0.0.Структурированные\r\nкабельные системы, Использование Microsoft Azure при разработке приложений&quot;\r\n\r\nСР. 15:00-16:20пр. Д.Яворницкого, 101  ауд. 9  Паршиков Олег Евгеньевич Цифровая фотография,\r\nТеория дизайна, Создание статичных веб-страниц с помощью XHTML и CSS, система управления содержимым для web сайтов, Рекламный дизайн, Основы типографики, Основы рисунка, Основы допечатной подготовки, \r\n\r\nОсновы айдентики, Основы SEO и веб-маркетинга, Основы Adobe Photoshop, Основы Adobe Illustrator, История\r\nискусства, Интерактивная анимация в Adobe Animate, Издательская система Adobe In Design, Графический редактор Adobe Photoshop 4.0.0, Графический редактор Adobe Photoshop, Графический редактор Adobe Illustrator 4.0.0, Usability и эргономика веб-страниц, UI/UX дизайн\r\n\r\nПТ. 16:30 -18:00 ул. Телевизионная 4а, ауд. 207. Паник Леонид Александрович. &quot;Основы программирования на языке С++,Объектно-ориентированное программирование с использованием языка C++ Базовый.",
        "image_path": null,
        "datetime_add": "2019-07-02 19:25",
        "video_link": null,
        "event_date": null,
        "event_description": null,
        "poll": null,
        "poll_voted": null
    },
    "errors": []
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">GET</span> getevents (<code><span>/v1/news/getevents</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Получить список новостей-событий.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
{
    "events": [
        {
            "id": 31,
            "event_date": {
                "date": "2019-08-23 20:20:00.000000",
                "timezone_type": 3,
                "timezone": "Europe/Kiev"
            }
        },
        {
            "id": 93,
            "event_date": {
                "date": "2019-07-26 13:25:00.000000",
                "timezone_type": 3,
                "timezone": "Europe/Kiev"
            }
        }
    ],
    "errors": []
}
</pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> add (<code><span>/v1/news/add</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Добавить новость.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>theme</td>
                            <td>string</td>
                            <td>Тема</td>
                        </tr>
                        <tr class="table-light">
                            <td>description</td>
                            <td>string</td>
                            <td>Описание</td>
                        </tr>
                        <tr class="table-light">
                            <td>image</td>
                            <td>string</td>
                            <td>Файл изображения закодированное в base64 (gif, png, jpg, jpeg) (опционально)</td>
                        </tr>
                        <tr class="table-light">
                            <td>video_link</td>
                            <td>string</td>
                            <td>Часть ссылки на видео из YouTube Пример: https://www.youtube.com/watch?v=5gyvnQnvAlI -->
                                5gyvnQnvAlI (опционально)
                            </td>
                        </tr>
                        <tr class="table-light">
                            <td>pollTheme</td>
                            <td>string</td>
                            <td>Тема опроса (опционально, если нет других данных о опросе)</td>
                        </tr>
                        <tr class="table-light">
                            <td>pollAnswers</td>
                            <td>string</td>
                            <td>Варианты ответов опроса (разделитель - "|") (опционально, если нет других данных о
                                опросе)
                            </td>
                        </tr>
                        <tr class="table-light">
                            <td>pollAnon</td>
                            <td>string</td>
                            <td>Анонимный ли опрос? (true/false) (опцинально, если нет других данных о опросе)</td>
                        </tr>
                        <tr class="table-light">
                            <td>event_date</td>
                            <td>string</td>
                            <td>Дата-время начала события Пример: 2018-11-23 20:20:00 (опционально)</td>
                        </tr>
                        <tr class="table-light">
                            <td>event_description</td>
                            <td>string</td>
                            <td>Описание (место) события (опционально)</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
{
    "status": "OK"
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> remove (<code><span>/v1/news/remove</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Удалить новость.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>id</td>
                            <td>string</td>
                            <td>id новости</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
                       {
    "status": "OK"
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> votepoll (<code><span>/v1/news/votepoll</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Проголосовать в опросе.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>news_id</td>
                            <td>string</td>
                            <td>id новости</td>
                        </tr>
                        <tr class="table-light">
                            <td>answer_id</td>
                            <td>string</td>
                            <td>id варианта ответа</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
                       {
    "status": "OK",
	"poll": [
		"id": 1,
		"theme": "Test",
		"anon": 0,
		"answers": [
			[
				"id": 1,
				"poll_id": 1,
				"answer": "Text",
				"votes": 1
			]
		]
	]
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> cancelvotepoll
                        (<code><span>/v1/news/cancelvotepoll</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Отменить голос в опросе.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>news_id</td>
                            <td>string</td>
                            <td>id новости</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
                       {
    "status": "OK",
	"poll": [
		"id": 1,
		"theme": "Test",
		"anon": 0,
		"answers": [
			[
				"id": 1,
				"poll_id": 1,
				"answer": "Text",
				"votes": 1
			]
		]
	]
}
                    </pre>
                </div>
            </div>
        </div>


        <div class="card-header text-center" id="headingSearch">
            <h5 class="mb-0">
                <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                        data-target="#collapseSearch" aria-expanded="true" aria-controls="collapseSearch">
                    <h5>search</h5>
                </button>
            </h5>
        </div>
        <div id="collapseSearch" class="collapse" aria-labelledby="headingSearch" data-parent="#accordionExample">
            <div class="card-body">
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> users (<code><span>/v1/search/users</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Поиск по имени/фамилии/отчеству. Получить список
                    пользователей в которых имени/фамилии/отчестве присутствует запрашеваемое слово(а).</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>query</td>
                            <td>string</td>
                            <td>Имя/Фамилия/Отчество пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>limit</td>
                            <td>int</td>
                            <td>Кол-во записей</td>
                        </tr>
                        <tr class="table-light">
                            <td>offset</td>
                            <td>int</td>
                            <td>Отступ в записях</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
                       {
    "search_text": "Влад",
    "result_search": [
        {
            "id": 6,
            "first_name": "Влад",
            "last_name": "Овсиенко",
            "patronymic": "Виталиевич",
            "group": "Другая",
            "photo_path": "http://127.0.0.1/db/photos/profile6.jpg",
             "status_visit": "online"
        }
    ]
}
                    </pre>
                </div>
            </div>
        </div>


        <div class="card-header text-center" id="headingSettings">
            <h5 class="mb-0">
                <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                        data-target="#collapseSettings" aria-expanded="true" aria-controls="collapseSettings">
                    <h5>settings</h5>
                </button>
            </h5>
        </div>
        <div id="collapseSettings" class="collapse" aria-labelledby="headingSettings" data-parent="#accordionExample">
            <div class="card-body">
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">GET</span> getdataprivacy
                        (<code><span>/v1/settings/getdataprivacy</span></code>)</h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Получить данные приватности.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
                      {
    "privacy": {
        "view_my_posts": "all",
        "write_post": "nobody"
    },
    "errors": []
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">GET</span> getdatablacklist
                        (<code><span>/v1/settings/getdatablacklist</span></code>)</h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Получить список чёрного листа (ЧС) пользователя.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>limit</td>
                            <td>int</td>
                            <td>Кол-во записей</td>
                        </tr>
                        <tr class="table-light">
                            <td>offset</td>
                            <td>int</td>
                            <td>Отступ в записях</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
                      {
    "black_list": [
        {
            "id": 2,
            "user_black_list_id": 21,
            "first_name": "Влад",
            "last_name": "Которович",
            "photo_path": "http://127.0.0.1/db/photos/profile21.jpg"
        }
    ],
    "errors": []
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">GET</span> getdataprofile
                        (<code><span>/v1/settings/getdataprofile</span></code>)</h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Получить данные профиля.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
                      {
    "profile": {
        "id": 6,
        "first_name": "Влад",
        "last_name": "Овсиенко",
        "patronymic": "Виталиевич",
        "email": "admin@mail.ru",
        "blocked": 0,
        "role": "admin",
        "group": "Другая",
        "gender": "Мужской",
        "phone_number": "+380111111111",
        "activities": "Студент.",
        "interests": "",
        "about_me": "",
        "photo_path": "http://127.0.0.1/db/photos/profile6.jpg",
        "date_birthday": {
            "date": "1998-01-14 00:00:00.000000",
            "timezone_type": 3,
            "timezone": "Europe/Berlin"
        }
    },
    "errors": []
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> saveprofile (<code><span>/v1/settings/saveprofile</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Обновить данные профиля новыми значениями.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>first_name</td>
                            <td>string</td>
                            <td>Имя</td>
                        </tr>
                        <tr class="table-light">
                            <td>last_name</td>
                            <td>string</td>
                            <td>Фамилия</td>
                        </tr>
                        <tr class="table-light">
                            <td>patronymic</td>
                            <td>string</td>
                            <td>Отчество</td>
                        </tr>
                        <tr class="table-light">
                            <td>email</td>
                            <td>string</td>
                            <td>email-адрес</td>
                        </tr>
                        <tr class="table-light">
                            <td>gender</td>
                            <td>string</td>
                            <td>Пол (Мужской, Женский)</td>
                        </tr>
                        <tr class="table-light">
                            <td>phone_number</td>
                            <td>string</td>
                            <td>Номер мобильного телефона (опционально)</td>
                        </tr>
                        <tr class="table-light">
                            <td>activities</td>
                            <td>string</td>
                            <td>Деятельность (опционально)</td>
                        </tr>
                        <tr class="table-light">
                            <td>interests</td>
                            <td>string</td>
                            <td>Интересы (опционально)</td>
                        </tr>
                        <tr class="table-light">
                            <td>about_me</td>
                            <td>string</td>
                            <td>Онформация обо мне (опционально)</td>
                        </tr>
                        <tr class="table-light">
                            <td>date_birthday</td>
                            <td>string</td>
                            <td>Дата рождения (опционально)</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
                      {
    "status": "OK"
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> saveprivacy (<code><span>/v1/settings/saveprivacy</span></code>)
                    </h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Обновить данные приватности новыми значениями.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>write_post</td>
                            <td>string</td>
                            <td>Кому доступно отправлять записи на мою страницу? (Варианты: all, nobody)</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
                      {
    "status": "OK"
}
                    </pre>
                </div>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><span class="badge badge-secondary">POST</span> changepassword
                        (<code><span>/v1/settings/changepassword</span></code>)</h4>
                    <hr>
                    <p class="mb-0">
                    <p style="font-weight: bold">Описание:</p>Обновить данные пароля.</p>
                    <hr>
                    <p style="font-weight: bold">Входные параметры:</p>
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-dark">
                            <th scope="col" style="width: 30%">Поле</th>
                            <th scope="col" style="width: 10%">Тип</th>
                            <th scope="col" style="width: 70%">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="table-light">
                            <td>email (Get/Cookie)</td>
                            <td>string</td>
                            <td>Email пользователя</td>
                        </tr>
                        <tr class="table-light">
                            <td>password (Get/Cookie)</td>
                            <td>string</td>
                            <td>Пароль пользователя (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>old_password_hash</td>
                            <td>string</td>
                            <td>Старый пароль (hash)</td>
                        </tr>
                        <tr class="table-light">
                            <td>new_password</td>
                            <td>string</td>
                            <td>Новый пароль</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p style="font-weight: bold">Выходные данные (JSON):</p>
                    <pre>
                      {
    "status": "OK"
}
                    </pre>
                </div>
            </div>
        </div>

    </div>