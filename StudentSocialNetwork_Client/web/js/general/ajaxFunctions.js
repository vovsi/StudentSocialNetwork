// Host server
const HOST = "http://18.223.123.52"; //"http://socialnetworkforstudents.zzz.com.ua/web";

// Получить параметры для авторизации (повторные, при каждом запросе)
function getAuthParams() {
    return "email=" + getCookie('email') + "&password=" + getCookie('password');
}

// Получить файл из input в формате Base64
function encodeImageFileToBase64(element) {
    var file = element.files[0];
    var reader = new FileReader();
    reader.onloadend = function () {
        console.log('1] : ' + reader.result);
    }
    var res = reader.readAsDataURL(file);
    console.log('2] : ' + res);
}

function toDataURL(url, callback) {
    var httpRequest = new XMLHttpRequest();
    httpRequest.onload = function () {
        var fileReader = new FileReader();
        fileReader.onloadend = function () {
            callback(fileReader.result);
        }
        fileReader.readAsDataURL(httpRequest.response);
    };
    httpRequest.open('GET', url, true);
    httpRequest.setRequestHeader('Access-Control-Allow-Origin', '*');
    httpRequest.setRequestHeader('Access-Control-Allow-Headers', '*');
    httpRequest.setRequestHeader('Access-Control-Expose-Headers', '*');
    httpRequest.setRequestHeader('Access-Control-Allow-Credentials', 'true');
    httpRequest.withCredentials = true;
    httpRequest.responseType = 'blob';
    httpRequest.send();
}

// Форматируем текст с учётом смайликов (преобразовываем теги span в кодовое имя смайлика)
function formatTextWithSmiles(text) {
    let formatMessage = text; // Отформатированное сообщение с учётом смайликов
    const regex = /<span title="([a-z_]+)" class="emotions emo-[a-z_]+" contentEditable="false"><\/span>/gi;
    let m;
    while ((m = regex.exec(text)) !== null) {
        // This is necessary to avoid infinite loops with zero-width matches
        if (m.index === regex.lastIndex) {
            regex.lastIndex++;
        }

        // The result can be accessed through the `m`-variable.
        m.forEach((match, groupIndex) => {
            if (groupIndex === 1) {

                let reg = new RegExp('<span title="' + match + '" class="emotions emo-' + match + '" contentEditable="false"></span>', 'gi');
                //let reg = /<span title="" class="emotions emo-/+match+/" contentEditable="false"><\/span>/gi;
                let codeSmile = $.emotions.shortcode(match);
                formatMessage = formatMessage.replace(reg, codeSmile);
            }
        });
    }
    // Убираем пробелы (кодовые)
    // Убираем сначала двойные, и ставим пробел
    let regSpaceTwo = new RegExp('&nbsp;&nbsp;', 'gi');
    formatMessage = formatMessage.replace(regSpaceTwo, ' ');
    // Убираем оставшиеся одинарные, и тсавим пробел
    let regSpaceOnes = new RegExp('&nbsp;', 'gi');
    formatMessage = formatMessage.replace(regSpaceOnes, ' ');

    return formatMessage;
}

// Заменить html код "<br>" переноса строки на "\n" в строке
function replaceNewLineCode(text) {
    // Убираем оставшиеся одинарные, и тсавим пробел
    let reg = new RegExp('<br>', 'gi');
    text = text.replace(reg, '\n');

    return text;
}

// Получить читабельный вид размера файла (с байтов в кбайты, мбайты...)
function formatSize(length) {
    var i = 0, type = ['б', 'Кб', 'Мб', 'Гб', 'Тб', 'Пб'];
    while ((length / 1000 | 0) && i < type.length - 1) {
        length /= 1024;
        i++;
    }
    return length.toFixed(2) + ' ' + type[i];
}

// Получить base64 строку изображения по её url
function getBase64FromUrlImage(url) {
    var result = "";
    $.ajax({
        url: HOST + "/v1/main/getbase64fromurlimage?url=" + url,
        dataType: "json",
        async: false,
        type: "GET",
        xhrFields: {
            withCredentials: false
        },
        success: function (data) {
            result = data;
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        }
    });
    return result;
}

// Возвращает cookie с именем name, если есть, если нет, то undefined
function getCookie(name) {
    var matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}

// Отображение ошибок в модальном окне
function showErrors(errors) {
    if (errors.length > 0) {
        $('#errorsAjaxList').empty();
        for (var i = 0; i < errors.length; i++) {
            $('#errorsAjaxList').append('<li>' + errors[i] + '</li>');
        }
        document.getElementById("errorsAjaxModalBtn").click();
    }
}

// Добавить в избранные
function addToFavorites(id) {
    var data = {id: id};
    $.ajax({
        crossDomain: true,
        async: true,
        url: HOST + "/v1/favorites/add?" + getAuthParams(),
        dataType: "json",
        type: "POST",
        data: data,
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
            //xhr.setRequestHeader("Cookie", 'email='+email+'; password='+password);
        },
        success: function (data) {
            //response( data );
            if (data['status'] === "OK") {

                // Good query
                $('#btn_removeFromFavorites' + id).css('display', 'inline');
                $('#btn_addToFavorites' + id).css('display', 'none');

            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        }
    });
}

// Удалить из избранных
function removeFromFavorites(id) {
    var data = {id: id};
    $.ajax({
        url: HOST + "/v1/favorites/remove?" + getAuthParams(),
        dataType: "json",
        type: "POST",
        data: data,
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
            //xhr.setRequestHeader("Cookie", 'email='+email+'; password='+password);
        },
        success: function (data) {
            //response( data );
            if (data['status'] === "OK") {

                // Good query
                $('#btn_addToFavorites' + id).css('display', 'inline');
                $('#btn_removeFromFavorites' + id).css('display', 'none');

            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        }
    });
}

// Добавить в чёрный список
function addToBlackList(id) {
    var data = {id: id};
    $.ajax({
        url: HOST + "/v1/main/addblacklist?" + getAuthParams(),
        dataType: "json",
        type: "POST",
        data: data,
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
            //xhr.setRequestHeader("Cookie", 'email='+email+'; password='+password);
        },
        success: function (data) {
            //response( data );
            if (data['status'] === "OK") {

                // Good query
                $('#btn_addToBlackList' + id).css('display', 'none');
                $('#btn_removeFromBlackList' + id).css('display', 'inline');

            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        }
    });
}

// Удалить из чёрного списка
function removeFromBlackList(id) {
    var data = {id: id};
    $.ajax({
        url: HOST + "/v1/main/removeblacklist?" + getAuthParams(),
        dataType: "json",
        type: "POST",
        data: data,
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
            //xhr.setRequestHeader("Cookie", 'email='+email+'; password='+password);
        },
        success: function (data) {
            //response( data );
            if (data['status'] === "OK") {

                // Good query
                $('#btn_addToBlackList' + id).css('display', 'inline');
                $('#btn_removeFromBlackList' + id).css('display', 'none');

            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        }
    });
}

// Добавить запись на стену
function addPost(accountToId, message, fileBase64, videoLink, files, pollTheme, pollAnswers, pollAnon) {
    let formatMessage = formatTextWithSmiles(message);
    formatMessage = replaceNewLineCode(formatMessage);

    var data = {
        account_to_id: accountToId,
        message: formatMessage,
        image: fileBase64,
        video_link: videoLink,
        files: files,
        poll_theme: pollTheme,
        poll_answers: pollAnswers,
        poll_anon: pollAnon
    };

    $.ajax({
        url: HOST + "/v1/main/addpost?" + getAuthParams(),
        dataType: "json",
        type: "POST",
        data: data,
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
            //xhr.setRequestHeader("Cookie", document.cookie);
            //xhr.setRequestHeader("Set-Cookie", 'email=admin@mail.ru; password=123admin123');
        },
        success: function (data) {
            //response( data );
            if (data['status'] === "OK") {
                if (data['post'] != null) {
                    var id = data['post']['id'];
                    var accountFromId = data['post']['account_from_id'];
                    var datetimeAdd = data['post']['datetime_add'];
                    var message = data['post']['message'];
                    var pathToImage = data['post']['path_to_image'];
                    if (pathToImage != null) {
                        pathToImage = getBase64FromUrlImage(pathToImage);
                    }
                    var videoLink = data['post']['video_link'];
                    var files = data['post']['files'];
                    var firstNameFromUser = data['post']['first_name_fromUser'];
                    var lastNameFromUser = data['post']['last_name_fromUser'];
                    var photoPathFromUser = getBase64FromUrlImage(data['post']['photo_path_fromUser']);
                    var poll = data['post']['poll'];

                    var statusVisitFromHtml = "<span name='status_visit' title='Этот пользователь находится сейчас на этом сайте.' class=\"badge badge-pill badge-success\">online</span>";

                    var itemPost = "<div id='post" + id + "' class=\"tab-content border-top\" style=\"padding-top: 10px;\">" +
                        "<a href=" + accountFromId + "'/'>" +
                        "<img src=\"" + photoPathFromUser + "\" class='rounded-circle' height='50px' width=\"50px\"></a>" +
                        "<a href=" + accountFromId + "'/'>" +
                        "<label>&#160;" + firstNameFromUser + "&#160;" + lastNameFromUser + "&#160;" + statusVisitFromHtml + "&#160;</label></a>" +
                        "<a href=" + accountFromId + "'/'>" +
                        "<label class=\"text-left\" style=\"color: gray;\">" + datetimeAdd + "</label></a>";

                    itemPost += "<button name='remove_post' onclick='removePost(" + id + ")' role='button' aria-label=\"Close\" class=\"close\"><span aria-hidden=\"true\">&times;</span></button>";

                    if (message === null) {
                        message = " ";
                    } else {
                        // Заменяем ссылки на гипертекстовые
                        //message = message.replace(/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i, '<a href="$1" target="_blank">$1</a>');
                    }
                    itemPost += "<br />" +
                        "<p class='post-message' style=\"word-wrap: break-word;white-space: pre-wrap;text-align: left\">" + message + "</p>";

                    // Если прикреплено изображение
                    if (pathToImage != null) {
                        itemPost += "<img src=\"" + pathToImage + "\" style='width:50%' class='btn-rounded' >";
                    }
                    // Если прикреплено видео
                    if (videoLink != null) {
                        itemPost += "<iframe id=\"video\" name=\"video\" style='margin-bottom: 5px' width=\"520px\" height=\"300px\" src=\"https://www.youtube.com/embed/" + videoLink + "\" frameborder=\"0\" allow=\"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>";
                    }
                    // Если прикреплены файлы
                    if (files != '') {
                        itemPost += "<br />";
                        for (var file of files) {
                            if (file != null) {
                                itemPost += "<a href='" + file['path'] + "' target='_blank'>" + file['file_name'] + " (" + formatSize(file['file_size_bytes']) + ")</a><br />";
                            }
                        }
                    }
                    // Если прикреплен опрос
                    if (poll != '') {
                        itemPost += "<div class=\"jumbotron\" style='padding: 2rem 2rem;'>";
                        itemPost += "<div id='postPoll" + id + "' class=\"span6\">";
                        itemPost += "<h5>" + poll['theme'] + "</h5>";
                        for (let i = 0; i < poll['answers'].length; i++) {
                            itemPost += "<button onclick='onVoteInPost(" + id + ", " + poll['answers'][i]['id'] + ")' class='btn btn-link'>" + poll['answers'][i]['answer'] + "</button><br />";
                        }
                        itemPost += "</div>";
                        itemPost += "</div>";
                    }

                    itemPost += "</div>";

                    // Добавляем новый пост в список других
                    $('#Posts').prepend(itemPost);

                    // Перезагружаем title для статуса (онлайн/оффлайн)
                    $('[name="status_visit"]').tooltip('enable');

                    // Очищаем поля
                    $('#text').empty();
                    $('#video_link').val('');
                    $('#att_photoCreatePost').val('');
                    $('#att_photoCreatePost').next('.custom-file-label').html('Загрузить изображение...');

                    // Обновляем смайлики в тексте
                    $('.post-message').emotions();
                }
            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        }
    });
}

// Проголосовать в опросе (в записи профиля)
function onVoteInPost(postId, answerId) {
    var data = {
        post_id: postId,
        answer_id: answerId
    };
    $.ajax({
        url: HOST + "/v1/main/votepoll?" + getAuthParams(),
        dataType: "json",
        type: "POST",
        data: data,
        xhrFields: {
            withCredentials: false

        },
        beforeSend: function (xhr) {

        },
        success: function (data) {
            //response( data );
            if (data['status'] === "OK") {
                let htmlPoll = '';
                let theme = data['poll']['theme'];

                $('#postPoll' + postId).html('');

                let summVotes = 0;
                for (let i = 0; i < data['poll']['answers'].length; i++) {
                    summVotes = summVotes + data['poll']['answers'][i]['votes'];
                }
                htmlPoll += "<h5>" + theme + "</h5>";
                for (let i = 0; i < data['poll']['answers'].length; i++) {

                    let percent = 0;
                    if (data['poll']['answers'][i]['votes'] > 0) {
                        percent = Math.round(data['poll']['answers'][i]['votes'] / summVotes * 100);
                    }
                    htmlPoll += "<strong class='show-poll-voted' title='Нажмите, чтобы посмотреть проголосовавших.' data-toggle=\"modal\" data-target=\"#pollVotedModal\" onclick='showPollOptionVoted(" + data['poll']['answers'][i]['id'] + ")'>" + data['poll']['answers'][i]['answer'] + "</strong><span class=\"float-right\">" + percent + "% (" + data['poll']['answers'][i]['votes'] + ")</span>";

                    htmlPoll += "<div class=\"progress show-poll-voted\" data-toggle=\"modal\" data-target=\"#pollVotedModal\" onclick='showPollOptionVoted(" + data['poll']['answers'][i]['id'] + ")'>" +
                        "<div class=\"progress-bar bg-info\" role=\"progressbar\" style=\"width: " + percent + "%\" aria-valuenow=\"" + percent + "\" aria-valuemin=\"0\" aria-valuemax=\"100\"></div>" +
                        "</div>";

                }
                htmlPoll += "<button class='btn btn-link float-right' onclick='cancelVoteInPollPost(" + postId + ")'>Отменить голос</button>";
                htmlPoll += "</div>";
                htmlPoll += "</div>";

                $('#postPoll' + postId).prepend(htmlPoll);
                // Обновить всплывающие подсказки
                $('[class="show-poll-voted"]').tooltip('enable')

            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        }
    });
}

// Проголосовать в опросе (в новостях)
function onVoteInNews(newsId, answerId) {
    var data = {
        news_id: newsId,
        answer_id: answerId
    };
    $.ajax({
        url: HOST + "/v1/news/votepoll?" + getAuthParams(),
        dataType: "json",
        type: "POST",
        data: data,
        xhrFields: {
            withCredentials: false

        },
        beforeSend: function (xhr) {

        },
        success: function (data) {
            if (data['status'] === "OK") {
                let htmlPoll = '';
                let theme = data['poll']['theme'];

                $('#pollContent').empty();

                let summVotes = 0;
                for (let i = 0; i < data['poll']['answers'].length; i++) {
                    summVotes = summVotes + data['poll']['answers'][i]['votes'];
                }

                // Проверяем, анонимный ли опрос, если да, то формируем html строку с уведомлением
                let anonTextHtml = '';
                if (data['poll']['anon'] == 1) {
                    anonTextHtml = "<label style='color: gray;font-size: small'>(Анонимный опрос)</label>";
                }
                htmlPoll += anonTextHtml;

                htmlPoll += "<h5>" + theme + "</h5>";
                for (let i = 0; i < data['poll']['answers'].length; i++) {

                    let percent = 0;
                    if (data['poll']['answers'][i]['votes'] > 0) {
                        percent = Math.round(data['poll']['answers'][i]['votes'] / summVotes * 100);
                    }
                    htmlPoll += "<strong class='show-poll-voted' title='Нажмите, чтобы посмотреть проголосовавших.' data-toggle=\"modal\" data-target=\"#pollVotedModal\" onclick='showPollOptionVoted(" + data['poll']['answers'][i]['id'] + ")'>" + data['poll']['answers'][i]['answer'] + "</strong><span class=\"float-right\">" + percent + "% (" + data['poll']['answers'][i]['votes'] + ")</span>";

                    htmlPoll += "<div class=\"progress show-poll-voted\" data-toggle=\"modal\" data-target=\"#pollVotedModal\" onclick='showPollOptionVoted(" + data['poll']['answers'][i]['id'] + ")'>" +
                        "<div class=\"progress-bar bg-info\" role=\"progressbar\" style=\"width: " + percent + "%\" aria-valuenow=\"" + percent + "\" aria-valuemin=\"0\" aria-valuemax=\"100\"></div>" +
                        "</div>";

                }
                htmlPoll += "<button class='btn btn-link float-right' onclick='cancelVoteInPollNews(" + newsId + ")'>Отменить голос</button>";
                htmlPoll += "</div>";
                htmlPoll += "</div>";

                $('#pollContent').append(htmlPoll);
                // Обновить всплывающие подсказки
                $('[class="show-poll-voted"]').tooltip('enable')

            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        }
    });
}

// Отменить голос в опросе (в записи профиля)
function cancelVoteInPollPost(postId) {
    var data = {
        post_id: postId,
    };
    $.ajax({
        url: HOST + "/v1/main/cancelvotepoll?" + getAuthParams(),
        dataType: "json",
        type: "POST",
        data: data,
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {

        },
        success: function (data) {
            //response( data );
            if (data['status'] === "OK") {

                let htmlPoll = '';
                let theme = data['poll']['theme'];

                $('#postPoll' + postId).html('');

                htmlPoll += "<h5>" + theme + "</h5>";
                for (let i = 0; i < data['poll']['answers'].length; i++) {
                    htmlPoll += "<button onclick='onVoteInPost(" + postId + ", " + data['poll']['answers'][i]['id'] + ")' class='btn btn-link'>" + data['poll']['answers'][i]['answer'] + "</button><br />";
                }

                $('#postPoll' + postId).prepend(htmlPoll);
            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        }
    });
}

// Отменить голос в опросе (в новостях)
function cancelVoteInPollNews(newsId) {
    var data = {
        news_id: newsId,
    };
    $.ajax({
        url: HOST + "/v1/news/cancelvotepoll?" + getAuthParams(),
        dataType: "json",
        type: "POST",
        data: data,
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {

        },
        success: function (data) {
            //response( data );
            if (data['status'] === "OK") {

                let htmlPoll = '';
                let theme = data['poll']['theme'];

                $('#pollContent').empty();

                // Проверяем, анонимный ли опрос, если да, то формируем html строку с уведомлением
                let anonTextHtml = '';
                if (data['poll']['anon'] == 1) {
                    anonTextHtml = "<label style='color: gray;font-size: small'>(Анонимный опрос)</label>";
                }
                htmlPoll += anonTextHtml;

                htmlPoll += "<h5>" + theme + "</h5>";
                for (let i = 0; i < data['poll']['answers'].length; i++) {
                    htmlPoll += "<button onclick='onVoteInNews(" + newsId + ", " + data['poll']['answers'][i]['id'] + ")' class='btn btn-link'>" + data['poll']['answers'][i]['answer'] + "</button><br />";
                }

                $('#pollContent').append(htmlPoll);
            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        }
    });
}

// Отобразить список проголосовавших за вариант ответа в опросе
function showPollOptionVoted(optionId) {
    $.ajax({
        url: HOST + "/v1/main/getpollanswervoted?pollAnswerId=" + optionId + "&" + getAuthParams(),
        dataType: "json",
        type: "GET",
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
            $('#load_anim_poll_option_voted').css('display', 'inline-block');
        },
        success: function (data) {

            if (data['status'] === 'OK') {
                $('#pollOptionVoted').html('');
                let listVotedHtml = '';
                // Если голосов больше 0
                if (data['votedAccounts'].length > 0) {
                    for (let i = 0; i < data['votedAccounts'].length; i++) {
                        let id = data['votedAccounts'][i]['id'];
                        let firstName = data['votedAccounts'][i]['first_name'];
                        let lastName = data['votedAccounts'][i]['last_name'];
                        let photoUser = getBase64FromUrlImage(data['votedAccounts'][i]['photo_path']);
                        let statusVisitHtml = "";
                        if (data['votedAccounts'][i]['status_visit'] == "online") {
                            statusVisitHtml = "<span name='status_visit' title='Этот пользователь находится сейчас на этом сайте.' class=\"badge badge-pill badge-success\">online</span> ";
                        } else {
                            statusVisitHtml = "<span name='status_visit' title='Этого пользователя сейчас нет на этом сайте.' class=\"badge badge-pill badge-danger\">offline</span> ";
                        }
                        listVotedHtml += "<li id='btn_removeFromFavorites$user_favorite_id' class=\"list-group-item\">" +
                            "<div class='form-row'>" +
                            "<a href='/" + id + "' class='col'>" +
                            "<img src=\"" + photoUser + "\" class='rounded-circle' height='50px' width=\"50px\"></a>" +
                            "<div class='col'><a href='/" + id + "'>" + statusVisitHtml + " " + firstName + " " + lastName + "</a></div>" +
                            "<div class='col'>";
                        listVotedHtml += "</div>" +
                            "</div>" +
                            "</li>";
                    }
                } else {
                    listVotedHtml += "<h5 style='color: gray;'>Нет голосов</h5>";
                }
                $('#pollOptionVoted').prepend(listVotedHtml);
            }

            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        },
        complete: function (data) {
            $('#load_anim_poll_option_voted').css('display', 'none');
        }
    });
}

// Создать беседу
function createConversation(name, members, imageBase64) {
    var data = {
        name: name,
        members: members,
        image_base64: imageBase64
    };

    $.ajax({
        url: HOST + "/v1/messages/createconversation?" + getAuthParams(),
        dataType: "json",
        type: "POST",
        data: data,
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
            //xhr.setRequestHeader("Cookie", 'email='+email+'; password='+password);
        },
        success: function (data) {
            //response( data );
            if (data['status'] === "OK") {
                window.location.href = '/messages/conversation?id=' + data['conversation_id'];
            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        }
    });
}

// Удалить запись со страницы
function removePost(id) {
    var data = {idPost: id};
    $.ajax({
        url: HOST + "/v1/main/removepost?" + getAuthParams(),
        dataType: "json",
        type: "POST",
        data: data,
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
            //xhr.setRequestHeader("Cookie", 'email='+email+'; password='+password);
        },
        success: function (data) {
            //response( data );
            if (data['status'] === "OK") {

                // Good query
                $('#post' + id).remove();

            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        }
    });
}

// Удалить новость
function removeNews(id) {
    var data = {id: id};
    $.ajax({
        url: HOST + "/v1/news/remove?" + getAuthParams(),
        dataType: "json",
        type: "POST",
        data: data,
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
            //xhr.setRequestHeader("Cookie", 'email='+email+'; password='+password);
        },
        success: function (data) {
            //response( data );
            if (data['status'] === "OK") {

                // Good query
                $('#fullViewNewsModal').modal('hide')
                $('#news' + id).remove();

            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        }
    });
}

// Удалить фото из альбома
function removePhotoFromAlbum(id) {
    var data = {id: id};
    $.ajax({
        url: HOST + "/v1/album/remove?" + getAuthParams(),
        dataType: "json",
        type: "POST",
        data: data,
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
            //xhr.setRequestHeader("Cookie", 'email='+email+'; password='+password);
        },
        success: function (data) {
            //response( data );
            if (data['status'] === "OK") {

                // Good query
                $('#photoModal').modal('hide')
                $('#photo' + id).remove();

            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        }
    });
}

// Удалить файл
function removeFile(filename, id) {
    var data = {fileName: filename};
    $.ajax({
        url: HOST + "/v1/files/remove?" + getAuthParams(),
        dataType: "json",
        type: "POST",
        data: data,
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
            //xhr.setRequestHeader("Cookie", 'email='+email+'; password='+password);
        },
        success: function (data) {
            //response( data );
            if (data['status'] === "OK") {

                $('#' + id).remove();

            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        }
    });
}

// Получить файл пользователя (по его id)
function getFileOfUser(userId, fileId) {
    var res;
    $.ajax({
        url: HOST + "/v1/files/getfile?idUser=" + userId + "&id=" + fileId + "&" + getAuthParams(),
        dataType: "json",
        async: false,
        type: "GET",
        xhrFields: {
            withCredentials: false
        },
        success: function (data) {
            //response( data );
            if (data != null) {
                res = data;
            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        }
    });
    return res;
}

// Отправить сообщение пользователю в диалог
function sendMessageToDialog(accountToId, message, imageBase64, files, videoYT) {
    let formatMessage = formatTextWithSmiles(message);
    formatMessage = replaceNewLineCode(formatMessage);

    var data = {
        account_to_id: accountToId,
        message: formatMessage,
        image: imageBase64,
        files: files,
        videoYT: videoYT
    };

    $.ajax({
        url: HOST + "/v1/messages/sendtodialog?" + getAuthParams(),
        dataType: "json",
        type: "POST",
        data: data,
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
            //xhr.setRequestHeader("Cookie", 'email='+email+'; password='+password);
        },
        success: function (data) {
            //response( data );
            if (data['status'] === "OK") {
                if (data['message'] != null) {
                    var messageText = data['message']['text'];
                    var messagePhotoPath = data['message']['photo_path'];
                    if (messagePhotoPath != null) {
                        messagePhotoPath = getBase64FromUrlImage(messagePhotoPath);
                    }
                    var dateSend = data['message']['date_send'];
                    var videoYT = data['message']['videoYT'];
                    var files = data['message']['files'];

                    var itemMessage = '';
                    itemMessage += "<div class=\"outgoing_msg\"><div class=\"sent_msg\">";

                    if (messageText === null) {
                        messageText = " ";
                    }
                    itemMessage += "<p>" + messageText;
                    // Вывод фото к сообщению
                    if (messagePhotoPath !== '') {
                        itemMessage += "<br/><a href='' data-toggle=\"modal\" data-target=\"#photo\" data-whatever=\"" + messagePhotoPath + "\"><img id=\"img_photo_profile\" class='btn-rounded' width=\"50%\" src=\"" + messagePhotoPath + "\" style='margin-bottom: 10px'/></a>";
                    }
                    // Если прикреплено видео YT
                    if (videoYT !== "") {
                        itemMessage += "<iframe id=\"video\" name=\"video\" style='margin-bottom: 5px' width=\"100%\" src=\"https://www.youtube.com/embed/" + videoYT + "\" frameborder=\"0\" allow=\"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>";
                    }
                    // Если прикреплены файлы
                    if (files != '') {
                        if (messagePhotoPath != null || videoYT !== "") {
                            itemMessage += "<br />";
                        }
                        for (var file of files) {
                            if (file != null) {
                                itemMessage += "<a href='" + file['path'] + "' target='_blank'>" + file['file_name'] + " (" + formatSize(file['file_size_bytes']) + ")</a><br />";
                            }
                        }
                    }
                    itemMessage += "</p>";
                    itemMessage += "<span class=\"time_date\">" + dateSend + "</span>";

                    itemMessage += "</div></div>";

                    // Добавляем новый пост в список других
                    $('#messages').append(itemMessage);

                    // Scroll в самый низ списка сообщений
                    $('#messages').scrollTop($('#messages').height() * $('#messages').height());

                    // Очищаем поля
                    $('#message').text('');
                    $('#att_photo_newMessage').val('');
                    $('#att_photo_newMessage').next('.custom-file-label').html('Загрузить изображение...');

                    // Обновляем смайлики мои
                    $('div.sent_msg>p').emotions();
                }
            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        }
    });
}

// Отправить сообщение в беседу
function sendMessageToConversation(conversationId, message, imageBase64, files, videoYT) {
    let formatMessage = formatTextWithSmiles(message);
    formatMessage = replaceNewLineCode(formatMessage);

    var data = {
        conversation_id: conversationId,
        message: formatMessage,
        image: imageBase64,
        files: files,
        videoYT: videoYT
    };

    $.ajax({
        url: HOST + "/v1/messages/sendtoconversation?" + getAuthParams(),
        dataType: "json",
        type: "POST",
        data: data,
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
            //xhr.setRequestHeader("Cookie", 'email='+email+'; password='+password);
        },
        success: function (data) {
            //response( data );
            if (data['status'] === "OK") {
                if (data['message'] != null) {
                    var messageText = data['message']['text'];
                    var messagePhotoPath = data['message']['photo_path'];
                    if (messagePhotoPath != null) {
                        messagePhotoPath = getBase64FromUrlImage(messagePhotoPath);
                    }
                    var dateSend = data['message']['date_send'];
                    var videoYT = data['message']['videoYT'];
                    var files = data['message']['files'];

                    var itemMessage = '';
                    itemMessage += "<div class=\"outgoing_msg\"><div class=\"sent_msg\">";

                    if (messageText === null) {
                        messageText = " ";
                    }
                    itemMessage += "<p>" + messageText;
                    // Вывод фото к сообщению
                    if (messagePhotoPath !== '') {
                        itemMessage += "<br/><a href='' data-toggle=\"modal\" data-target=\"#photo\" data-whatever=\"" + messagePhotoPath + "\"><img id=\"img_photo_profile\" class='btn-rounded' width=\"50%\" src=\"" + messagePhotoPath + "\" style='margin-bottom: 10px'/></a>";
                    }
                    // Если прикреплено видео YT
                    if (videoYT !== "") {
                        itemMessage += "<iframe id=\"video\" name=\"video\" style='margin-bottom: 5px' width=\"100%\" src=\"https://www.youtube.com/embed/" + videoYT + "\" frameborder=\"0\" allow=\"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>";
                    }
                    // Если прикреплены файлы
                    if (files != '') {
                        if (messagePhotoPath != null || videoYT !== "") {
                            itemMessage += "<br />";
                        }
                        for (var file of files) {
                            if (file != null) {
                                itemMessage += "<a href='" + file['path'] + "' target='_blank'>" + file['file_name'] + " (" + formatSize(file['file_size_bytes']) + ")</a><br />";
                            }
                        }
                    }
                    itemMessage += "</p>";
                    itemMessage += "<span class=\"time_date\">" + dateSend + "</span>";

                    itemMessage += "</div></div>";

                    // Добавляем новый пост в список других
                    $('#messages').append(itemMessage);

                    // Scroll в самый низ списка сообщений
                    $('#messages').scrollTop($('#messages').height() * $('#messages').height());

                    // Очищаем поля
                    $('#message').text('');
                    $('#att_photo_newMessage').val('');
                    $('#att_photo_newMessage').next('.custom-file-label').html('Загрузить изображение...');

                    // Обновляем смайлики мои
                    $('div.sent_msg>p').emotions();
                }
            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        }
    });
}

// Загрузить доп. кол-во записей на страницу (пагинация)
function getPosts(accountToId, limit, offset, myId) {
    $.ajax({
        url: HOST + "/v1/main/getposts?id=" + accountToId + "&limit=" + limit + "&offset=" + offset + "&" + getAuthParams(),
        dataType: "json",
        type: "GET",
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
            //xhr.setRequestHeader("Cookie", 'email='+email+'; password='+password);
            $('#load_anim').css('display', 'inline-block');
        },
        success: function (data) {
            //response( data );
            if (data['posts'] != null) {
                // Скрываем кнопку Показать ещё, если больше нет записей
                if (!data['is_there_more_posts']) {
                    $('#show_more').remove();
                }

                for (let i = 0; i < data['posts'].length; i++) {
                    var id = data['posts'][i]['id'];
                    var accountFromId = data['posts'][i]['id_FROM'];
                    var datetimeAdd = data['posts'][i]['datetime_add'];
                    var message = data['posts'][i]['message'];
                    var pathToImage = data['posts'][i]['path_to_image'];
                    if (pathToImage != null) {
                        pathToImage = getBase64FromUrlImage(pathToImage);
                    }
                    var videoLink = data['posts'][i]['video_link'];
                    var files = data['posts'][i]['files'];
                    var firstNameFromUser = data['posts'][i]['first_name_FROM'];
                    var lastNameFromUser = data['posts'][i]['last_name_FROM'];
                    var photoPathFromUser = getBase64FromUrlImage(data['posts'][i]['photo_FROM']);
                    var statusVisitFrom = data['posts'][i]['status_visit_FROM'];
                    var poll = data['posts'][i]['poll'];
                    var pollVoted = data['posts'][i]['poll_voted'];

                    // Вывод статуса посещения пользователя
                    if (statusVisitFrom === "online") {
                        var statusVisitFromHtml = "<span name='status_visit' title='Этот пользователь находится сейчас на этом сайте.' class=\"badge badge-pill badge-success\">online</span>";
                    } else {
                        var statusVisitFromHtml = "<span name='status_visit' title='Этого пользователя сейчас нет на этом сайте..' class=\"badge badge-pill badge-danger\">offline</span>";
                    }

                    var itemPost = "<div id='post" + id + "' class=\"tab-content border-top\" style=\"padding-top: 10px;\">" +
                        "<a href=" + accountFromId + "'/'>" +
                        "<img src=\"" + photoPathFromUser + "\" class='rounded-circle' height='50px' width=\"50px\"></a>" +
                        "<a href=" + accountFromId + "'/'>" +
                        "<label>&#160;" + firstNameFromUser + "&#160;" + lastNameFromUser + "&#160;" + statusVisitFromHtml + "&#160;</label></a>" +
                        "<a href=" + accountFromId + "'/'>" +
                        "<label class=\"text-left\" style=\"color: gray;\">" + datetimeAdd + "</label></a>";

                    itemPost += "<button name='remove_post' onclick='removePost(" + id + ")' role='button' aria-label=\"Close\" class=\"close\"><span aria-hidden=\"true\">&times;</span></button>";

                    // Вывод сообщения
                    if (message != null) {
                        itemPost += "<br />" +
                            "<p class='post-message' style=\"word-wrap: break-word;white-space: pre-wrap;text-align: left;margin-top: 5px\">" + message + "</p>";
                    }

                    // Вывод изображения
                    if (pathToImage != null) {
                        itemPost += "<img src=\"" + pathToImage + "\" style='width:50%' class='btn-rounded' >";
                    }
                    // Вывод видео
                    if (videoLink != null) {
                        itemPost += "<iframe name=\"video\" style='margin-bottom: 5px;margin-top: 5px' width=\"520px\" height=\"300px\" src=\"https://www.youtube.com/embed/" + videoLink + "\" frameborder=\"0\" allow=\"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>";
                    }
                    // Вывод файлов
                    if (files != '') {
                        itemPost += "<br />";
                        for (var file of files) {
                            if (file != null) {
                                itemPost += "<a href='" + file['path'] + "' target='_blank'>" + file['file_name'] + " (" + formatSize(file['file_size_bytes']) + ")</a><br />";
                            }
                        }
                    }
                    //Вывод опроса
                    if (poll != '') {
                        // Проверяем, анонимный ли опрос, если да, то формируем html строку с уведомлением
                        let anonTextHtml = '';
                        if (poll['anon'] == 1) {
                            anonTextHtml = "<label style='color: gray;font-size: small'>(Анонимный опрос)</label>";
                        }

                        let countAnswers = poll['answers'].length;
                        let summVotes = 0;
                        for (let j = 0; j < poll['answers'].length; j++) {
                            summVotes = summVotes + poll['answers'][j]['votes'];
                        }
                        itemPost += "<div class=\"jumbotron\" style='padding: 2rem 2rem;'>";
                        itemPost += anonTextHtml;
                        itemPost += "<div id='postPoll" + id + "' class=\"span6\">";

                        itemPost += "<h5>" + poll['theme'] + "</h5>";
                        let imVoted = false;
                        for (let g = 0; g < poll['answers'].length; g++) {

                            // Check user on voted in poll
                            let voted = false;
                            for (let f = 0; f < pollVoted.length; f++) {
                                if (pollVoted[f]['account_id'] == myId) { // ??????????
                                    voted = true;
                                    break;
                                }
                            }
                            // If not voted
                            if (voted == false) {
                                itemPost += "<button onclick='onVoteInPost(" + id + ", " + poll['answers'][g]['id'] + ")' class='btn btn-link'>" + poll['answers'][g]['answer'] + "</button><br />";
                            } else {
                                // If voted
                                imVoted = true;
                                let percent = 0;
                                if (poll['answers'][g]['votes'] > 0) {
                                    percent = Math.round(poll['answers'][g]['votes'] / summVotes * 100);
                                }
                                itemPost += "<strong class='show-poll-voted' title='Нажмите, чтобы посмотреть проголосовавших.' data-toggle=\"modal\" data-target=\"#pollVotedModal\" onclick='showPollOptionVoted(" + poll['answers'][g]['id'] + ")'>" + poll['answers'][g]['answer'] + "</strong><span class=\"float-right\">" + percent + "% (" + poll['answers'][g]['votes'] + ")</span>";

                                itemPost += "<div class=\"progress show-poll-voted\" data-toggle=\"modal\" data-target=\"#pollVotedModal\" onclick='showPollOptionVoted(" + poll['answers'][g]['id'] + ")'>" +
                                    "<div class=\"progress-bar bg-info\" role=\"progressbar\" style=\"width: " + percent + "%\" aria-valuenow=\"" + percent + "\" aria-valuemin=\"0\" aria-valuemax=\"100\"></div>" +
                                    "</div>";
                            }
                        }
                        if (imVoted) {
                            itemPost += "<button class='btn btn-link float-right' onclick='cancelVoteInPollPost(" + id + ")'>Отменить голос</button>";
                        }
                        itemPost += "</div>";
                        itemPost += "</div>";
                    }

                    itemPost += "</div>";

                    // Добавляем новый пост в список других
                    $('#Posts').append(itemPost);
                }
                // Перезагружаем title для статуса (онлайн/оффлайн)
                $('[name="status_visit"]').tooltip('enable');

                // Обновляем смайлики в тексте
                $('.post-message').emotions();
            } else {
                $('#load_anim').remove();
                $('#show_more').remove();
            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        },
        complete: function (data) {
            $('#load_anim').css('display', 'none');
            $('#show_more').css('display', 'inline-block');
        }
    });
}

// Загрузить новость (в полном виде)
function showFullOneNews(id, myId) {
    $.ajax({
        async: true,
        url: HOST + "/v1/news/getonenews?id=" + id + "&" + getAuthParams(),
        dataType: "json",
        type: "GET",
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
            $('#load_anim_full_size').css('display', 'inline-block');
            $('#load_anim_poll_option_voted').css('display', 'inline-block');

            $('#fullViewNewsModalBody').css('visibility', 'hidden');
            $('#fullViewNewsModalHeader').css('visibility', 'hidden');
        },
        success: function (data) {
            if (data['news'] != null) {
                let pathToImage = data['news']['image_path'];
                let id = data['news']['id'];
                let theme = data['news']['theme'];
                let description = data['news']['description'];
                let datetimeAdd = data['news']['datetime_add'];
                let videoLink = data['news']['video_link'];
                let eventDate = data['news']['event_date'];
                let eventDescription = data['news']['event_description'];
                let poll = data['news']['poll'];
                let pollVoted = data['news']['poll_voted'];

                $('#theme').text(theme);

                // Если есть ссылка на видео, то отображаем блок с видео
                if (videoLink == null) {
                    $('#video').css('display', 'none');
                } else {
                    $('#video').css('display', 'inline-block');
                    $('#video').attr('src', 'https://www.youtube.com/embed/' + videoLink);
                }

                // Если эта новость - событие, то отображаем блок с этой инфой
                if (eventDate !== null) {
                    $('#modalEventInfo').css('display', 'inline-block');
                    let eventDescriptionHtml = '';
                    if (eventDescription == null) {
                        eventDescriptionHtml = 'Место события не указано';
                    } else {
                        eventDescriptionHtml = 'Место события: ' + eventDescription;
                    }

                    $('#event_date').text('Начнется ' + eventDate);
                    $('#event_description').text(eventDescriptionHtml);
                } else {
                    $('#modalEventInfo').css('display', 'none');
                }

                if (pathToImage != null) {
                    $('#image').css('display', 'inline-block');
                    $('#image').attr('src', getBase64FromUrlImage(pathToImage));
                } else {
                    $('#image').css('display', 'none');
                }

                // Прикрепленный опрос
                $('#pollContent').empty();
                let pollHtml = '';
                if (poll != null) {
                    $('#poll').css('display', 'inline-block');
                    // Проверяем, анонимный ли опрос, если да, то формируем html строку с уведомлением
                    let anonTextHtml = '';
                    if (poll['anon'] == 1) {
                        anonTextHtml = "<label style='color: gray;font-size: small'>(Анонимный опрос)</label>";
                    }

                    let summVotes = 0;
                    for (let j = 0; j < poll['answers'].length; j++) {
                        summVotes = summVotes + poll['answers'][j]['votes'];
                    }
                    pollHtml += anonTextHtml;

                    pollHtml += "<h5>" + poll['theme'] + "</h5>";
                    let imVoted = false;
                    for (let g = 0; g < poll['answers'].length; g++) {

                        // Check user on voted in poll
                        let voted = false;
                        for (let f = 0; f < pollVoted.length; f++) {
                            if (pollVoted[f]['account_id'] == myId) {
                                voted = true;
                                break;
                            }
                        }
                        // If not voted
                        if (voted == false) {
                            pollHtml += "<button onclick='onVoteInNews(" + id + ", " + poll['answers'][g]['id'] + ")' class='btn btn-link'>" + poll['answers'][g]['answer'] + "</button><br />";
                        } else {
                            // If voted
                            imVoted = true;
                            let percent = 0;
                            if (poll['answers'][g]['votes'] > 0) {
                                percent = Math.round(poll['answers'][g]['votes'] / summVotes * 100);
                            }
                            pollHtml += "<strong class='show-poll-voted' title='Нажмите, чтобы посмотреть проголосовавших.' data-toggle=\"modal\" data-target=\"#pollVotedModal\" onclick='showPollOptionVoted(" + poll['answers'][g]['id'] + ")'>" + poll['answers'][g]['answer'] + "</strong><span class=\"float-right\">" + percent + "% (" + poll['answers'][g]['votes'] + ")</span>";

                            pollHtml += "<div class=\"progress show-poll-voted\" data-toggle=\"modal\" data-target=\"#pollVotedModal\" onclick='showPollOptionVoted(" + poll['answers'][g]['id'] + ")'>" +
                                "<div class=\"progress-bar bg-info\" role=\"progressbar\" style=\"width: " + percent + "%\" aria-valuenow=\"" + percent + "\" aria-valuemin=\"0\" aria-valuemax=\"100\"></div>" +
                                "</div>";
                        }
                    }
                    if (imVoted) {
                        pollHtml += "<button class='btn btn-link float-right' onclick='cancelVoteInPollNews(" + id + ")'>Отменить голос</button>";
                    }
                    pollHtml += "</div>";
                    pollHtml += "</div>";
                } else {
                    $('#poll').css('display', 'none');
                }
                $('#pollContent').append(pollHtml);

                $('#description').text(description);
                $('#datetime_add').text(datetimeAdd);

                if ($('#removeNews') != null) {
                    $('#removeNews').attr('onclick', 'removeNews(' + id + ')');
                }
            }

            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        },
        complete: function (data) {
            $('#loadEvents_anim').css('display', 'none');
            $('#load_anim_poll_option_voted').css('display', 'none');
            $('#load_anim_full_size').css('display', 'none');

            $('#fullViewNewsModalBody').css('visibility', 'visible');
            $('#fullViewNewsModalHeader').css('visibility', 'visible');
        }
    });
}

// Загрузить доп. кол-во новостей на страницу (пагинация)
function getNews(limit, offset) {
    $.ajax({
        url: HOST + "/v1/news/getnews?limit=" + limit + "&offset=" + offset + "&" + getAuthParams(),
        dataType: "json",
        type: "GET",
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
            //xhr.setRequestHeader("Cookie", 'email='+email+'; password='+password);
            $('#load_anim').css('display', 'inline-block');
        },
        success: function (data) {
            //response( data );
            if (data['news'] != null) {
                if (data['news'].length === 0) {
                    $('#load_anim').remove();
                    $('#show_more').remove();
                }

                // Скрываем кнопку Показать ещё, если больше нет записей
                if (!data['is_there_more']) {
                    $('#show_more').remove();
                }

                for (let i = 0; i < data['news'].length; i++) {
                    var itemNews = '';
                    var resDesc = '';

                    // Описание
                    if (data['news'][i]['description'].length > 500) {
                        var cutStr = data['news'][i]['description'].substr(0, 499);
                        resDesc = cutStr + '...';
                    } else {
                        resDesc = data['news'][i]['description'];
                    }

                    var eventHtml = "";
                    // Прикрепленное событие
                    var eventDate = '';
                    if (data['news'][i]['event_date'] != null) {
                        eventDate = data['news'][i]['event_date'];
                        eventHtml = "<i name=\"icon_event\" class=\"fa fa-bell\" title=\"Эта новость является событием. Подробности на странице с новостью.\" style=\"color: #d41717;width: 20px;\"></i> " +
                            "<span name='event_date' title='Дата и время начала события.' class=\"badge border border-danger btn-rounded\" style='margin-right: 5px'>" + eventDate + "</span>";
                    }

                    var idNews = data['news'][i]['id'];
                    itemNews += "<div id='news" + idNews + "' class=\"list-group\">" +
                        "<a href=\"\" data-toggle=\"modal\" data-target=\"#fullViewNewsModal\"  data-id='" + idNews + "'"
                        + "' class=\"list-group-item list-group-item-action flex-column align-items-start\">" +
                        "<div class=\"d-flex w-100 justify-content-between\">" +
                        "<h5 class=\"text-left\">" + eventHtml + data['news'][i]['theme'] + "</h5>" +
                        "<small name='date_add' title='Дата-время добавления новости.'>" + data['news'][i]['datetime_add'] + "</small>" +
                        "</div>" +
                        "<p class=\"mb-1\" style=\"word-wrap: break-word;white-space: pre-wrap;text-align: left\">" + resDesc + "</p>" +
                        "</a>" +
                        "</div>";

                    // Добавляем новый пост в список других
                    $('#News').append(itemNews);
                }

                // Всплывающая подсказка (Новость-событие)
                $('[name="icon_event"]').tooltip('enable')

                // Всплывающая подсказка (Дата события)
                $('[name="event_date"]').tooltip('enable')

                // Всплывающая подсказка (Дата добавления новости)
                $('[name="date_add"]').tooltip('enable')
            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        },
        complete: function (data) {
            $('#load_anim').css('display', 'none');
            $('#show_more').css('display', 'inline-block');
        }
    });
}

// Загрузить доп. кол-во диалогов (пагинация)
function getDialogs(limit, offset) {
    $.ajax({
        url: HOST + "/v1/messages/getdialogs?limit=" + limit + "&offset=" + offset + "&" + getAuthParams(),
        dataType: "json",
        type: "GET",
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
            //xhr.setRequestHeader("Cookie", 'email='+email+'; password='+password);
            $('#show_more_dialogs').css('display', 'none');
            $('#load_anim').css('display', 'inline-block');
        },
        success: function (data) {
            //response( data );
            if (data['dialogs'] != null) {
                if (data['dialogs'].length === 0) {
                    $('#load_anim').remove();
                    //$('#show_more_dialogs').remove();
                }
                // Скрываем кнопку Показать ещё, если больше нет записей
                if (!data['is_there_more']) {
                    $('#show_more_dialogs').remove();
                }
                for (let i = 0; i < data['dialogs'].length; i++) {
                    var itemDialog = '';

                    var countNewMessages = '';
                    if (data['dialogs'][i]['countNewMessages'] > 0) {
                        countNewMessages = "<span style='margin-left: 10px' class=\"badge badge-info\">+" + data['dialogs'][i]['countNewMessages'] + "</span>";
                    }

                    var avatar = getBase64FromUrlImage(data['dialogs'][i]['interlocutor_image']);
                    var dateSend = data['dialogs'][i]['date_change'];
                    itemDialog += "<a href=\"/messages/dialog?id=" + data['dialogs'][i]['dialog_id'] + "\" class='list-group-item list-group-item-action flex-column align-items-start'>"
                        + "<div class=\"d-flex w-100 justify-content-between\">"
                        + "<img src=\"" + avatar + "\" class='rounded-circle' height='50px' width=\"50px\">"
                        + "<label class='text-center' style='font-weight: bold'>" + data['dialogs'][i]['interlocutor_first_name'] + ' ' + data['dialogs'][i]['interlocutor_last_name'] + "</label>";

                    itemDialog += "<label style='color: grey;font-size: small'>(" + data['dialogs'][i]['interlocutor_group'] + ")</label>";

                    if (data['dialogs'][i]['interlocutor_status_visit'] === "online") {
                        itemDialog += " <span name='status_visit' style='height: 20px' title='Этот пользователь находится сейчас на этом сайте.' class=\"badge badge-pill badge-success\">online</span>";
                    } else {
                        itemDialog += " <span name='status_visit' style='height: 20px' title='Этого пользователя сейчас нет на этом сайте.' class=\"badge badge-pill badge-danger\">offline</span>";
                    }
                    itemDialog += "</div>";
                    // Если последним сообщением является фото без текста
                    if (data['dialogs'][i]['last_message'] !== '') {
                        var resMess = '';
                        var cutStr = '';
                        // Если длина сообщения больше 60 символов, то сокращаем, для вывода
                        if (data['dialogs'][i]['last_message'].length > 60) {
                            cutStr = substr($value['last_message'], 0, 59);
                            resMess = cutStr + '...';
                        } else {
                            resMess = data['dialogs'][i]['last_message'];
                        }

                        itemDialog += "<p style='display: inline-block;' class='mb-1'><label style='color: gray;'>" + data['dialogs'][i]['sender_point'] + "</label>" + resMess + countNewMessages + "<span style='color: #888a85;display: inline-block;margin-left: 5%;font-size: small''>" + dateSend + "</span></p>";
                    } else if (data['dialogs'][i]['last_message_photo'] !== '') {
                        itemDialog += "<p style='display: inline-block;' class='mb-1'><label style='color: gray;'>" + data['dialogs'][i]['sender_point'] + "</label>(Фотография)<span style='color: #888a85;display: inline-block;margin-left: 10%;font-size: small''>" + dateSend + "</span></p>";
                    } else if (data['dialogs'][i]['last_message_files'] !== '') {
                        itemDialog += "<p style='display: inline-block;' class='mb-1'><label style='color: gray;'>" + data['dialogs'][i]['sender_point'] + "</label>(Файл)<span style='color: #888a85;display: inline-block;margin-left: 10%;font-size: small''>" + dateSend + "</span></p>";
                    } else if (data['dialogs'][i]['last_message_videoYT'] !== '') {
                        itemDialog += "<p style='display: inline-block;' class='mb-1'><label style='color: gray;'>" + data['dialogs'][i]['sender_point'] + "</label>(Видео YT)<span style='color: #888a85;display: inline-block;margin-left: 10%;font-size: small''>" + dateSend + "</span></p>";
                    }
                    itemDialog += "</a>";

                    // Добавляем новый пост в список других
                    $('#Dialogs').append(itemDialog);
                }
                // Перезагружаем title для статуса (онлайн/оффлайн)
                $('[name="status_visit"]').tooltip('enable');

                // Форматируем кода смайликов в картинки
                $('p').emotions();
            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        },
        complete: function (data) {
            $('#load_anim').css('display', 'none');
            $('#show_more_dialogs').css('display', 'inline-block');
        }
    });
}

// Загрузить доп. кол-во бесед (пагинация)
function getConversations(limit, offset) {
    $.ajax({
        url: HOST + "/v1/messages/getconversations?limit=" + limit + "&offset=" + offset + "&" + getAuthParams(),
        dataType: "json",
        type: "GET",
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
            //xhr.setRequestHeader("Cookie", 'email='+email+'; password='+password);
            $('#show_more_conversations').css('display', 'none');
            $('#load_anim').css('display', 'inline-block');
        },
        success: function (data) {
            //response( data );
            if (data['conversations'] != null) {
                if (data['conversations'].length === 0) {
                    $('#load_anim').remove();
                    $('#show_more_conversations').remove();
                }
                // Скрываем кнопку Показать ещё, если больше нет записей
                if (!data['is_there_more']) {
                    $('#show_more_conversations').remove();
                }
                for (let i = 0; i < data['conversations'].length; i++) {
                    var itemConversation = '';

                    let countNewMessages = '';
                    if (data['conversations'][i]['count_new_messages'] > 0) {
                        countNewMessages = "<span style='margin-left: 10px' class=\"badge badge-info\">+" + data['conversations'][i]['count_new_messages'] + "</span>";
                    }

                    let dateSend = data['conversations'][i]["date_change"];
                    itemConversation += "<a href=\"/messages/conversation?id=" + data['conversations'][i]['conversation_id'] + "\" class='list-group-item list-group-item-action flex-column align-items-start'>" +
                        "<div class=\"d-flex w-100 justify-content-between\">" +
                        "<img src=\"" + data['conversations'][i]['conversation_photo'] + "\" class='rounded-circle' height='50px' width=\"50px\">" +
                        "<label style='font-weight: bold;margin-top:10px;margin-right: 50px'>" + data['conversations'][i]['conversation_name'] + "</label>" +
                        "<label></label>";
                    itemConversation += "</div>";

                    let resMess = '';
                    if (data['conversations'][i]['last_message'] !== '') {
                        // Если длина сообщения больше 60 символов, то сокращаем, для вывода
                        if (data['conversations'][i]['last_message'].length > 60) {
                            let cutStr = data['conversations'][i]['last_message'].indexOf(0, 59);
                            resMess = cutStr + '...';
                        } else {
                            resMess = data['conversations'][i]['last_message'];
                        }

                        itemConversation += "<p style='display: inline-block;' class='mb-1'><label style='color: gray;'>" + data['conversations'][i]['sender_point'] + "</label>" + resMess + countNewMessages + "<span style='color: #888a85;display: inline-block;margin-left: 5%;font-size: small'>" + dateSend + "</span></p>";
                    } else if (data['conversations'][i]['last_message_photo'] !== '') {
                        itemConversation += "<p style='display: inline-block;' class='mb-1'><label style='color: gray;'>" + data['conversations'][i]['sender_point'] + "</label>(Фотография)<span style='color: #888a85;display: inline-block;margin-left: 5%;font-size: small'>" + dateSend + "</span></p>";
                    } else if ($value['last_message_files'] !== '') {
                        itemConversation += "<p style='display: inline-block;' class='mb-1'><label style='color: gray;'>" + data['conversations'][i]['sender_point'] + "</label>(Файл)<span style='color: #888a85;display: inline-block;margin-left: 5%;font-size: small'>" + dateSend + "</span></p>";
                    } else if (data['conversations'][i]['last_message_videoYT'] !== '') {
                        itemConversation += "<p style='display: inline-block;' class='mb-1'><label style='color: gray;'>" + data['conversations'][i]['sender_point'] + "</label>(Видео YT)<span style='color: #888a85;display: inline-block;margin-left: 5%;font-size: small'>" + dateSend + "</span></p>";
                    }
                    itemConversation += "</a>";

                    // Добавляем новый элемент в список других
                    $('#Conversations').append(itemConversation);
                }
                // Перезагружаем title для статуса (онлайн/оффлайн)
                $('[name="status_visit"]').tooltip('enable');

                // Форматируем кода смайликов в картинки
                $('p').emotions();
            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        },
        complete: function (data) {
            $('#load_anim').css('display', 'none');
            $('#show_more_conversations').css('display', 'inline-block');
        }
    });
}

// Загрузить доп. кол-во сообщений в диалог (пагинация)
function getMessagesFromDialog(idDialog, limit, offset) {
    $.ajax({
        url: HOST + "/v1/messages/getdialog?id=" + idDialog + "&limit=" + limit + "&offset=" + offset + "&" + getAuthParams(),
        dataType: "json",
        type: "GET",
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
            //xhr.setRequestHeader("Cookie", 'email='+email+'; password='+password);
            $('#load_anim').css('display', 'inline-block');
        },
        success: function (data) {
            //response( data );
            if (data['messages'] != null) {
                // Скрываем кнопку Показать ещё, если больше нет записей
                if (!data['is_there_more']) {
                    $('#show_more').remove();
                }

                data['messages'] = data['messages'].reverse();
                for (let i = 0; i < data['messages'].length; i++) {
                    var itemMessage = '';

                    var senderId = data['messages'][i]['sender_id'];
                    var senderPhoto = getBase64FromUrlImage(data['messages'][i]['sender_photo']);
                    var messageText = data['messages'][i]['message_text'];
                    var messagePhotoPath = data['messages'][i]['message_photo_path'];
                    if (messagePhotoPath != null) {
                        messagePhotoPath = getBase64FromUrlImage(messagePhotoPath);
                    }
                    var dateSend = data['messages'][i]['date_send'];
                    var recipientId = data['recipient_id'];
                    var videoYT = data['messages'][i]['videoYT'];
                    var files = data['messages'][i]['files'];

                    // Если отправитель - тот, с кем ведется диалог
                    if (senderId === recipientId) {
                        itemMessage += "<div class=\"incoming_msg\">";
                        itemMessage += "<div class=\"incoming_msg_img\"> <a href='" + senderId + "'><img class='rounded-circle' style='width: 50px;height: 50px;' src=\"" + senderPhoto + "\" alt=\"sunil\"></a> </div>";
                        itemMessage += "<div class=\"received_msg\">";
                        itemMessage += "<div class=\"received_withd_msg\">";
                    } else {
                        itemMessage += "<div class=\"outgoing_msg\">";
                        itemMessage += "<div class=\"sent_msg\">";
                    }

                    itemMessage += "<p>" + messageText;
                    // Вывод фото к сообщению
                    if (messagePhotoPath != '') {
                        itemMessage += "<br/><a href='' data-toggle=\"modal\" data-target=\"#photo\" data-whatever=\"" + messagePhotoPath + "\"><img id=\"img_photo_profile\" class='btn-rounded' width=\"50%\" src=\"" + messagePhotoPath + "\" style='margin-bottom: 10px'/></a>";
                    }
                    // Если прикреплено видео YT
                    if (videoYT !== "") {
                        itemMessage += "<iframe id=\"video\" name=\"video\" style='margin-bottom: 5px' width=\"100%\" src=\"https://www.youtube.com/embed/" + videoYT + "\" frameborder=\"0\" allow=\"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>";
                    }
                    // Если прикреплены файлы
                    if (files != '') {
                        itemMessage += "<br />";
                        for (var file of files) {
                            if (file != null) {
                                itemMessage += "<a href='" + file['path'] + "' target='_blank'>" + file['file_name'] + " (" + formatSize(file['file_size_bytes']) + ")</a><br />";
                            }
                        }
                    }
                    itemMessage += "</p>";
                    itemMessage += "<span class=\"time_date\">" + dateSend + "</span>";

                    if (senderId === recipientId) {
                        itemMessage += "</div></div></div>";
                    } else {
                        itemMessage += "</div></div>";
                    }

                    // Добавляем новый пост в список других
                    $('#messages').prepend(itemMessage);
                }
                // Обновляем смайлики мои
                $('div.sent_msg>p').emotions();
                // Обновляем смайлики собеседника
                $('div.received_withd_msg>p').emotions();
            } else {
                $('#load_anim').remove();
                $('#show_more').remove();
            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        },
        complete: function (data) {
            $('#load_anim').css('display', 'none');
            $('#show_more').css('display', 'inline-block');
        }
    });
}

// Загрузить доп. кол-во сообщений в беседу (пагинация)
function getMessagesFromConversation(myId, idConversation, limit, offset) {
    $.ajax({
        url: HOST + "/v1/messages/getconversation?id=" + idConversation + "&limit=" + limit + "&offset=" + offset
        + "&" + getAuthParams(),
        dataType: "json",
        type: "GET",
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
            //xhr.setRequestHeader("Cookie", 'email='+email+'; password='+password);
            $('#load_anim').css('display', 'inline-block');
        },
        success: function (data) {
            //response( data );
            if (data['messages'] != null) {
                // Скрываем кнопку Показать ещё, если больше нет записей
                if (!data['is_there_more']) {
                    $('#show_more').remove();
                }

                data['messages'] = data['messages'].reverse();
                for (let i = 0; i < data['messages'].length; i++) {
                    var itemMessage = '';

                    var senderId = data['messages'][i]['sender_id'];
                    var senderPhoto = getBase64FromUrlImage(data['messages'][i]['sender_photo']);
                    var senderFirstName = data['messages'][i]['sender_first_name'];
                    var senderLastName = data['messages'][i]['sender_last_name'];
                    var messageText = data['messages'][i]['message_text'];
                    var messagePhotoPath = data['messages'][i]['message_photo_path'];
                    if (messagePhotoPath != null) {
                        messagePhotoPath = getBase64FromUrlImage(messagePhotoPath);
                    }
                    var dateSend = data['messages'][i]['date_send'];
                    var videoYT = data['messages'][i]['videoYT'];
                    var files = data['messages'][i]['files'];

                    // Если отправитель - не тот, с кем ведется диалог
                    if (senderId != myId) {
                        itemMessage += "<div class=\"incoming_msg\">";
                        itemMessage += "<div class=\"incoming_msg_img\"> <a href='" + senderId + "'><img class='rounded-circle' style='width: 50px;height: 50px;' src=\"" + senderPhoto + "\" alt=\"sunil\"></a> </div>";
                        itemMessage += "<div class=\"received_msg\">";
                        itemMessage += "<div class=\"received_withd_msg\">";
                        itemMessage += "<label style='font-size: small;'><a href='" + senderId + "'>" + senderFirstName + " " + senderLastName + "</a></label>";
                    } else {
                        itemMessage += "<div class=\"outgoing_msg\">";
                        itemMessage += "<div class=\"sent_msg\">";
                    }

                    itemMessage += "<p>" + messageText;
                    // Вывод фото к сообщению
                    if (messagePhotoPath != '') {
                        itemMessage += "<br/><a href='' data-toggle=\"modal\" data-target=\"#photo\" data-whatever=\"" + messagePhotoPath + "\"><img id=\"img_photo_profile\" class='btn-rounded' width=\"50%\" src=\"" + messagePhotoPath + "\" style='margin-bottom: 10px'/></a>";
                    }
                    // Если прикреплено видео YT
                    if (videoYT !== "") {
                        itemMessage += "<iframe id=\"video\" name=\"video\" style='margin-bottom: 5px' width=\"100%\" src=\"https://www.youtube.com/embed/" + videoYT + "\" frameborder=\"0\" allow=\"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>";
                    }
                    // Если прикреплены файлы
                    if (files != '') {
                        itemMessage += "<br />";
                        for (var file of files) {
                            if (file != null) {
                                itemMessage += "<a href='" + file['path'] + "' target='_blank'>" + file['file_name'] + " (" + formatSize(file['file_size_bytes']) + ")</a><br />";
                            }
                        }
                    }
                    itemMessage += "</p>";
                    itemMessage += "<span class=\"time_date\">" + dateSend + "</span>";

                    if (senderId !== myId) {
                        itemMessage += "</div></div></div>";
                    } else {
                        itemMessage += "</div></div>";
                    }

                    // Добавляем новый пост в список других
                    $('#messages').prepend(itemMessage);
                }
                // Обновляем смайлики мои
                $('div.sent_msg>p').emotions();
                // Обновляем смайлики собеседника
                $('div.received_withd_msg>p').emotions();
            } else {
                $('#load_anim').remove();
                $('#show_more').remove();
            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        },
        complete: function (data) {
            $('#load_anim').css('display', 'none');
            $('#show_more').css('display', 'inline-block');
        }
    });
}

function getFilesOfUser() {
    var res;
    $.ajax({
        url: HOST + "/v1/files/getfiles?" + getAuthParams(),
        dataType: "json",
        async: false,
        type: "GET",
        xhrFields: {
            withCredentials: false
        },
        success: function (data) {
            //response( data );
            if (data['files'] != null) {
                res = data['files'];
            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        }
    });
    return res;
}

// Загрузить доп. кол-во фото в альбом (пагинация)
function getPhotos(idUser, limit, offset) {
    $.ajax({
        url: HOST + "/v1/album/getalbum?id=" + idUser + "&limit=" + limit + "&offset=" + offset + "&" + getAuthParams(),
        dataType: "json",
        type: "GET",
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
            //xhr.setRequestHeader("Cookie", 'email='+email+'; password='+password);
        },
        success: function (data) {
            //response( data );
            if (data['photos'] != null) {
                for (let i = 0; i < data['photos'].length; i++) {
                    var itemPhoto = '';

                    var base64Image = getBase64FromUrlImage(data['photos'][i]['path']);
                    var toHtml = "<a id='photo" + data['photos'][i]['id'] + "' href='' data-toggle=\"modal\" data-target=\"#photoModal\" " +
                        "data-description='" + data['photos'][i]['description'] + "'" +
                        "data-id='" + data['photos'][i]['id'] + "'" +
                        "data-datetime='" + data['photos'][i]['datetime_add'] + "'" +
                        " data-path=\"" + data['photos'][i]['path'] + "\">" +
                        "<img id=\"img_photo_profile\" class='btn-rounded' src=\"" + base64Image + "\"/></a>";

                    itemPhoto = "<div class=\"item\">" + toHtml + "</div>";

                    // Добавляем новое фото в список других
                    $('#gallery').append(itemPhoto);
                }
            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        }
    });
}

// Получить список избранных пользователя
function getFavoritesOfUser(limit, offset) {
    var res;
    $.ajax({
        url: HOST + "/v1/favorites/getmyfavorites?limit=" + limit + "&offset=" + offset + "&" + getAuthParams(),
        dataType: "json",
        async: false,
        type: "GET",
        xhrFields: {
            withCredentials: false
        },
        success: function (data) {
            if (data['favorites'] != null) {
                res = data['favorites'];
            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        }
    });
    return res;
}

// Получить список участников беседы
function getMembersOfConversation(conversationId) {
    var res;
    $.ajax({
        url: HOST + "/v1/messages/getmembersofconversation?id=" + conversationId + "&" + getAuthParams(),
        dataType: "json",
        async: false,
        type: "GET",
        xhrFields: {
            withCredentials: false
        },
        success: function (data) {
            if (data['members'] != null) {
                res = data['members'];
            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        }
    });
    return res;
}

// Изменить список участников беседы
function changeMembersConversation(conversationId, members) {
    var data = {
        conversationId: conversationId,
        members: members
    };

    $.ajax({
        url: HOST + "/v1/messages/changemembersconversation?" + getAuthParams(),
        dataType: "json",
        type: "POST",
        data: data,
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
            //xhr.setRequestHeader("Cookie", 'email='+email+'; password='+password);
        },
        success: function (data) {
            //response( data );
            if (data['status'] === "OK") {
                window.location.href = '/messages/conversation?id=' + data['conversation_id'];
            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        }
    });
}

// Загрузить доп. кол-во избранных пользователей (пагинация)
function getFavorites(limit, offset) {
    $.ajax({
        url: HOST + "/v1/favorites/getmyfavorites?limit=" + limit + "&offset=" + offset + "&" + getAuthParams(),
        dataType: "json",
        type: "GET",
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
            //xhr.setRequestHeader("Cookie", 'email='+email+'; password='+password);
            $('#load_anim').css('display', 'inline-block');
        },
        success: function (data) {
            //response( data );
            if (data['favorites'] != null) {
                if (data['favorites'].length === 0) {
                    $('#load_anim').remove();
                    $('#show_more').remove();
                }
                // Скрываем кнопку Показать ещё, если больше нет записей
                if (!data['is_there_more_favorites']) {
                    $('#show_more').remove();
                }
                for (let i = 0; i < data['favorites'].length; i++) {
                    var itemFavorite = '';

                    var userFavoriteId = data['favorites'][i]['user_favorite_id'];
                    var firstName = data['favorites'][i]['first_name'];
                    var lastName = data['favorites'][i]['last_name'];
                    var photoUser = getBase64FromUrlImage(data['favorites'][i]['photo_path']);
                    var statusVisitHtml = "";
                    if (data['favorites'][i]['status_visit'] === "online") {
                        statusVisitHtml = "<span name='status_visit' title='Этот пользователь находится сейчас на этом сайте.' class=\"badge badge-pill badge-success\">online</span> ";
                    } else {
                        statusVisitHtml = "<span name='status_visit' title='Этого пользователя сейчас нет на этом сайте.' class=\"badge badge-pill badge-danger\">offline</span> ";
                    }
                    itemFavorite += "<li id='btn_removeFromFavorites" + userFavoriteId + "' class=\"list-group-item\">" +
                        "<div class='form-row'>" +
                        "<a href=" + userFavoriteId + "'/' class='col'><img src=\"" + photoUser + "\" class='rounded-circle' height='50px' width=\"50px\"></a>" +
                        "<div class='col'> " + statusVisitHtml + " " + firstName + " " + lastName + "</div>" +
                        "<div class='col'>";
                    itemFavorite += "<button onclick='removeFromFavorites(" + userFavoriteId + ")' role='button' class=\"btn btn-info btn-rounded\" style=\"background-color: #36BEC3;border-color: #36BEC3;display: inline\">Убрать</button>";
                    itemFavorite += "</div></div></li>";

                    // Добавляем избранного в список других
                    $('#favoritesUsers').append(itemFavorite);
                }
                // Перезагружаем title для статуса (онлайн/оффлайн)
                $('[name="status_visit"]').tooltip('enable');
            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        },
        complete: function (data) {
            $('#load_anim').css('display', 'none');
            $('#show_more').css('display', 'inline-block');
        }
    });
}

// Загрузить доп. кол-во пользователей из чёрного списка (пагинация)
function getBlackList(limit, offset) {
    $.ajax({
        url: HOST + "/v1/settings/getdatablacklist?limit=" + limit + "&offset=" + offset + "&" + getAuthParams(),
        dataType: "json",
        type: "GET",
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
            //xhr.setRequestHeader("Cookie", 'email='+email+'; password='+password);
            $('#load_anim').css('display', 'inline-block');
        },
        success: function (data) {
            //response( data );
            if (data['black_list'] != null) {
                if (data['black_list'].length === 0) {
                    $('#load_anim').remove();
                    $('#show_more').remove();
                }
                // Скрываем кнопку Показать ещё, если больше нет записей
                if (!data['is_there_more_black_list']) {
                    $('#show_more').remove();
                }
                for (let i = 0; i < data['black_list'].length; i++) {
                    var itemBL = '';

                    var userBlackListId = data['black_list'][i]['user_black_list_id'];
                    var firstName = data['black_list'][i]['first_name'];
                    var lastName = data['black_list'][i]['last_name'];
                    var photoUser = getBase64FromUrlImage(data['black_list'][i]['photo_path']);
                    var statusVisitHtml = "";
                    if (data['black_list'][i]['status_visit'] === "online") {
                        statusVisitHtml = "<span name='status_visit' title='Этот пользователь находится сейчас на этом сайте.' class=\"badge badge-pill badge-success\">online</span> ";
                    } else {
                        statusVisitHtml = "<span name='status_visit' title='Этого пользователя сейчас нет на этом сайте.' class=\"badge badge-pill badge-danger\">offline</span> ";
                    }
                    itemBL += "<li id='btn_removeFromBlackList" + userBlackListId + "' class=\"list-group-item\">" +
                        "<div class='form-row'>" +
                        "<a href=" + userBlackListId + "'/' class='col'>" +
                        "<img src=\"" + photoUser + "\" class='rounded-circle' height='50px' width=\"50px\"></a>" +
                        "<div class='col'>" + statusVisitHtml + " " + firstName + " " + lastName + "</div>" +
                        " <div class='col'>";
                    itemBL += "<button onclick='removeFromBlackList(" + userBlackListId + ")' role='button' class=\"btn btn-info btn-rounded\" style=\"background-color: #36BEC3;border-color: #36BEC3;display: inline\">Убрать</button>";
                    itemBL += "</div></div></li>";

                    // Добавляем пользователя из ЧС в список других
                    $('#black_list').append(itemBL);
                }
                // Перезагружаем title для статуса (онлайн/оффлайн)
                $('[name="status_visit"]').tooltip('enable');
            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        },
        complete: function (data) {
            $('#load_anim').css('display', 'none');
            $('#show_more').css('display', 'inline-block');
        }
    });
}

// Загрузить доп. кол-во пользователей из поиска (пагинация)
function getSearchUsers(query, limit, offset) {
    var data = {
        query: query,
        limit: limit,
        offset: offset
    };
    $.ajax({
        url: HOST + "/v1/search/users?" + getAuthParams(),
        dataType: "json",
        type: "POST",
        data: data,
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
            $('#load_anim').css('display', 'inline-block');
        },
        success: function (data) {
            //response( data );
            if (data['result_search'] != null) {
                for (let i = 0; i < data['result_search'].length; i++) {
                    var itemUserSearch = '';

                    var userId = data['result_search'][i]['id'];
                    var firstName = data['result_search'][i]['first_name'];
                    var lastName = data['result_search'][i]['last_name'];
                    var patronymic = data['result_search'][i]['patronymic'];
                    var photoUser = getBase64FromUrlImage(data['result_search'][i]['photo_path']);
                    var group = data['result_search'][i]['group'];
                    var statusVisitHtml = "";
                    if (data['result_search'][i]['status_visit'] === "online") {
                        statusVisitHtml = "<span name='status_visit' title='Этот пользователь находится сейчас на этом сайте.' class=\"badge badge-pill badge-success\">online</span> ";
                    } else {
                        statusVisitHtml = "<span name='status_visit' title='Этого пользователя сейчас нет на этом сайте.' class=\"badge badge-pill badge-danger\">offline</span> ";
                    }
                    itemUserSearch += "<li class=\"list-group-item\">" +
                        "<div class='form-row'>" +
                        "<a href=" + userId + "'/' class='col'>" +
                        "<img src=\"" + photoUser + "\" class='rounded-circle' height='50px' width=\"50px\"></a>" +
                        " <a href=" + userId + "'/' class='col'><div class='col'>" + firstName + " " + patronymic + " " + lastName + " <label style='color: gray;'>&nbsp;(" + group + ")</label></div></a> " +
                        "<div class='col'>" +
                        statusVisitHtml +
                        "</div> </div></li>";

                    // Добавляем пользователя из ЧС в список других
                    $('#searchResult').append(itemUserSearch);
                }
                // Перезагружаем title для статуса (онлайн/оффлайн)
                $('[name="status_visit"]').tooltip('enable');
            } else {
                $('#load_anim').remove();
                $('#show_more').remove();
            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        },
        complete: function (data) {
            $('#load_anim').css('display', 'none');
            $('#show_more').css('display', 'inline-block');
        }
    });
}

// Получить массив всех событий (id, date)
function getEvents() {
    var dataReq = null;
    $.ajax({
        async: false,
        url: HOST + "/v1/news/getevents?" + getAuthParams(),
        dataType: "json",
        type: "GET",
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
        },
        success: function (data) {
            //response( data );
            if (data['events'] != null) {
                dataReq = data['events'];
            }

            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        }
    });
    return dataReq;
}

// Добавить блок с событием в список событий на конкретный день (когда пользователь выбрал день в календаре событий)
function addEventToListEvents(idNews) {
    $.ajax({
        async: true,
        url: HOST + "/v1/news/getonenews?id=" + idNews + "&" + getAuthParams(),
        dataType: "json",
        type: "GET",
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
            $('#loadEvents_anim').css('display', 'inline-block');
        },
        success: function (data) {
            //response( data );
            if (data['news'] != null) {
                let id = data['news']['id'];
                let theme = data['news']['theme'];

                let eventItem = '<div class="card">' +
                    '<div class="card-header" id="heading' + id + '">' +
                    '<h5 class="mb-0">' +
                    '<button data-id="' + id + '" data-toggle="modal" data-target="#fullViewNewsModal" class="btn btn-link" type="button" style="text-decoration: none;"><h5 style="word-wrap: break-word;white-space: pre-line;font-weight: bold; color: #5e5e5e;">' +
                    theme +
                    '</h5></button>' +
                    '</h5>' +
                    '</div>' +
                    ' </div>';

                $('#listEvents').append(eventItem);
            }

            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        },
        complete: function (data) {
            $('#loadEvents_anim').css('display', 'none');
        }
    });
}

// Получить кол-во непрочитанных диалогов и обновить статус непр. диалогов
function refreshNotViewedGroupMsgs() {
    var countDialogs, countConversations, countAll = 0;
    if ($('#count_not_viewed_group_msgs').length > 0) {
        $.ajax({
            async: true,
            url: HOST + "/v1/main/getcountnotviewedgroupmsgs?" + getAuthParams(),
            dataType: "json",
            type: "GET",
            /*headers: {
                'Set-Cookie': 'email='+getCookie('email')+';password='+getCookie('password')+';'
            },*/
            xhrFields: {
                withCredentials: false
            },
            beforeSend: function (xhr) {
                //xhr.setRequestHeader("Access-Control-Allow-Origin", 'http://socialnetworkforstudents.zzz.com.ua');
                //xhr.setRequestHeader("Cookie", 'email='+getCookie('email')+';password='+getCookie('password')+';');
            },
            success: function (data) {
                //response( data );
                if (data['count_new_dialogs_msgs'] != null && data['count_new_conversations_msgs'] != null) {
                    countDialogs = data['count_new_dialogs_msgs'];
                    countConversations = data['count_new_conversations_msgs'];
                    countAll = data['count_new_dialogs_msgs'] + data['count_new_conversations_msgs'];

                    // Если есть новые сообщения в диалогах
                    if (countDialogs > 0) {
                        // Обновляем счетчик (на странице /messages)
                        if ($('#count_new_dialogs').length > 0) {
                            $('#count_new_dialogs').text('+' + countDialogs);
                        }
                        // Если есть новые сообщения для диалога, то получить их (по конкретному диалогу)
                        if ($('#dialog_id').length > 0) {
                            getNotViewedMessagesFromDialog($('#dialog_id').val());
                        }
                    } else {
                        if ($('#count_new_dialogs').length > 0) {
                            $('#count_new_dialogs').text();
                        }
                    }
                    // Если есть новые сообщения в беседах
                    if (countConversations > 0) {
                        // Обновляем счетчик (на странице /messages)
                        if ($('#count_new_conversations').length > 0) {
                            $('#count_new_conversations').text('+' + countConversations);
                        }
                        // Если есть новые сообщения для беседы, то получить их (по конкретной беседе)
                        if ($('#conversation_id').length > 0) {
                            getNotViewedMessagesFromConversation($('#my_id').val(), $('#conversation_id').val());
                        }
                    } else {
                        if ($('#count_new_conversations').length > 0) {
                            $('#count_new_conversations').text();
                        }
                    }
                    // Отображение кнопки "Есть новые сообщение. Обновить?" во вкладке диалогов
                    if ($('#tip_newMessages').length > 0) {
                        // Если есть новые сообщения в диалогах и беседах (сумма)
                        if (countAll > 0) {
                            $('#tip_newMessages').css('display', 'inline-block');
                        } else {
                            $('#tip_newMessages').css('display', 'none');
                        }
                    }
                    // Обновить статус непрочитанных диалогов в меню сайта
                    if (countAll <= 0) {
                        countAll = '';
                    }
                    $('#count_not_viewed_group_msgs').text(countAll);
                }

                // Receive errors
                if (data['errors'] != null) {
                    showErrors(data['errors']);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                // Errors query
            },
            complete: function (data) {
            }
        });
    }
}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

// Получить и вывести непрочитанные (новые) сообщения в диалоге
function getNotViewedMessagesFromDialog(idDialog) {
    var count = 0;
    if ($('#count_not_viewed_group_msgs').length > 0) {
        $.ajax({
            async: true,
            url: HOST + "/v1/messages/getnewmessagesfromdialog?id=" + idDialog + "&" + getAuthParams(),
            dataType: "json",
            type: "GET",
            xhrFields: {
                withCredentials: false
            },
            beforeSend: function (xhr) {
            },
            success: function (data) {
                //response( data );
                if (data['new_messages'] != null) {

                    let senderPhoto = getBase64FromUrlImage(data['recipient_photo_path']);

                    for (let i = 0; i < data['new_messages'].length; i++) {
                        let itemMess = "";

                        let messageText = data['new_messages'][i]['text'];
                        let messagePhotoPath = data['new_messages'][i]['photo_path'];
                        let videoYT = data['new_messages'][i]['video_youtube'];
                        let files = data['new_messages'][i]['files'];
                        if (messagePhotoPath != null) {
                            messagePhotoPath = getBase64FromUrlImage(messagePhotoPath);
                        }
                        let dateSend = sqlToJsDate(data['new_messages'][i]['date_send']['date']);

                        if (messageText === null) {
                            messageText = " ";
                        }

                        itemMess += "<div class=\"incoming_msg\">";
                        itemMess += "<div class=\"incoming_msg_img\"> <img class='rounded-circle' style='width: 50px;height: 50px;' src=\"" + senderPhoto + "\" alt=\"sunil\"> </div>";
                        itemMess += "<div class=\"received_msg\">";
                        itemMess += "<div class=\"received_withd_msg\">";
                        itemMess += "<p>" + messageText;
                        // Вывод фото к сообщению
                        if (messagePhotoPath !== "") {
                            itemMess += "<br/><a href='' data-toggle=\"modal\" data-target=\"#photo\" data-whatever=\"" + data['new_messages'][i]['photo_path'] + "\"><img id=\"img_photo_profile\" class='btn-rounded' width=\"50%\" src=\"" + messagePhotoPath + "\" style='margin-bottom: 10px'/></a>";
                        }
                        // Если прикреплено видео YT
                        if (videoYT != null) {
                            itemMess += "<iframe id=\"video\" name=\"video\" style='margin-bottom: 5px' width=\"100%\" src=\"https://www.youtube.com/embed/" + videoYT + "\" frameborder=\"0\" allow=\"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>";
                        }
                        // Если прикреплены файлы
                        if (files != null) {
                            itemMess += "<br />";
                            for (var file of files) {
                                if (file != null) {
                                    itemMess += "<a href='" + file['path'] + "' target='_blank'>" + file['file_name'] + " (" + formatSize(file['file_size_bytes']) + ")</a><br />";
                                }
                            }
                        }
                        itemMess += "</p>";
                        itemMess += "<span class=\"time_date\">" + dateSend.getFullYear() + "-" + (dateSend.getMonth() + 1) + "-" + dateSend.getDate() + " " + dateSend.getHours() + ":" + dateSend.getMinutes() + "</span>";
                        itemMess += "</div></div>";

                        // Добавляем новое сообщение к другим
                        $('#messages').append(itemMess);
                    }
                    if (data['new_messages'].length > 0) {
                        // Scroll в самый низ списка сообщений
                        $('#messages').scrollTop($('#messages').height() * $('#messages').height());
                        // Обновляем смайлики собеседника
                        $('div.received_withd_msg>p').emotions();
                    }
                }

                // Receive errors
                if (data['errors'] != null) {
                    showErrors(data['errors']);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                // Errors query
            }
        });
    }
}

// Получить и вывести непрочитанные (новые) сообщения в беседе
function getNotViewedMessagesFromConversation(myId, idConversation) {
    var count = 0;
    if ($('#count_not_viewed_group_msgs').length > 0) {
        $.ajax({
            async: true,
            url: HOST + "/v1/messages/getnewmessagesfromconversation?id=" + idConversation + "&" + getAuthParams(),
            dataType: "json",
            type: "GET",
            xhrFields: {
                withCredentials: false
            },
            beforeSend: function (xhr) {
            },
            success: function (data) {
                //response( data );
                if (data['new_messages'] != null) {

                    data['new_messages'] = data['new_messages'].reverse();
                    for (let i = 0; i < data['new_messages'].length; i++) {
                        var itemMessage = '';

                        var senderId = data['new_messages'][i]['sender_id'];
                        var senderPhoto = getBase64FromUrlImage(data['new_messages'][i]['sender_photo']);
                        var senderFirstName = data['new_messages'][i]['sender_first_name'];
                        var senderLastName = data['new_messages'][i]['sender_last_name'];
                        var messageText = data['new_messages'][i]['message_text'];
                        var messagePhotoPath = data['new_messages'][i]['message_photo_path'];
                        if (messagePhotoPath != '') {
                            messagePhotoPath = getBase64FromUrlImage(messagePhotoPath);
                        }
                        var dateSend = data['new_messages'][i]['date_send'];
                        var videoYT = data['new_messages'][i]['videoYT'];
                        var files = data['new_messages'][i]['files'];

                        // Если отправитель - не тот, с кем ведется диалог
                        if (senderId != myId) {
                            itemMessage += "<div class=\"incoming_msg\">";
                            itemMessage += "<div class=\"incoming_msg_img\"> <a href='" + senderId + "'><img class='rounded-circle' style='width: 50px;height: 50px;' src=\"" + senderPhoto + "\" alt=\"sunil\"></a> </div>";
                            itemMessage += "<div class=\"received_msg\">";
                            itemMessage += "<div class=\"received_withd_msg\">";
                            itemMessage += "<label style='font-size: small;'><a href='" + senderId + "'>" + senderFirstName + " " + senderLastName + "</a></label>";
                        } else {
                            itemMessage += "<div class=\"outgoing_msg\">";
                            itemMessage += "<div class=\"sent_msg\">";
                        }

                        itemMessage += "<p>" + messageText;
                        // Вывод фото к сообщению
                        if (messagePhotoPath != '') {
                            itemMessage += "<br/><a href='' data-toggle=\"modal\" data-target=\"#photo\" data-whatever=\"" + messagePhotoPath + "\"><img id=\"img_photo_profile\" class='btn-rounded' width=\"50%\" src=\"" + messagePhotoPath + "\" style='margin-bottom: 10px'/></a>";
                        }
                        // Если прикреплено видео YT
                        if (videoYT !== "") {
                            itemMessage += "<iframe id=\"video\" name=\"video\" style='margin-bottom: 5px' width=\"100%\" src=\"https://www.youtube.com/embed/" + videoYT + "\" frameborder=\"0\" allow=\"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>";
                        }
                        // Если прикреплены файлы
                        if (files != '') {
                            itemMessage += "<br />";
                            for (var file of files) {
                                if (file != null) {
                                    itemMessage += "<a href='" + file['path'] + "' target='_blank'>" + file['file_name'] + " (" + formatSize(file['file_size_bytes']) + ")</a><br />";
                                }
                            }
                        }
                        itemMessage += "</p>";
                        itemMessage += "<span class=\"time_date\">" + dateSend + "</span>";

                        if (senderId !== myId) {
                            itemMessage += "</div></div></div>";
                        } else {
                            itemMessage += "</div></div>";
                        }

                        // Добавляем новое сообщение в список других
                        $('#messages').append(itemMessage);
                    }
                    if (data['new_messages'].length > 0) {
                        // Scroll в самый низ списка сообщений
                        $('#messages').scrollTop($('#messages').height() * $('#messages').height());
                        // Обновляем смайлики мои
                        $('div.sent_msg>p').emotions();
                        // Обновляем смайлики собеседника
                        $('div.received_withd_msg>p').emotions();
                    }
                }

                // Receive errors
                if (data['errors'] != null) {
                    showErrors(data['errors']);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                // Errors query
            }
        });
    }
}

// Преобразовать SQL дату к Date JS
function sqlToJsDate(sqlDate) {
    //sqlDate in SQL DATETIME format ("yyyy-mm-dd hh:mm:ss.ms")
    var sqlDateArr1 = sqlDate.split("-");
    //format of sqlDateArr1[] = ['yyyy','mm','dd hh:mm:ms']
    var sYear = sqlDateArr1[0];
    var sMonth = (Number(sqlDateArr1[1]) - 1).toString();
    var sqlDateArr2 = sqlDateArr1[2].split(" ");
    //format of sqlDateArr2[] = ['dd', 'hh:mm:ss.ms']
    var sDay = sqlDateArr2[0];
    var sqlDateArr3 = sqlDateArr2[1].split(":");
    //format of sqlDateArr3[] = ['hh','mm','ss.ms']
    var sHour = sqlDateArr3[0];
    var sMinute = sqlDateArr3[1];
    var sqlDateArr4 = sqlDateArr3[2].split(".");
    //format of sqlDateArr4[] = ['ss','ms']
    var sSecond = sqlDateArr4[0];
    var sMillisecond = sqlDateArr4[1];

    return new Date(sYear, sMonth, sDay, sHour, sMinute, sSecond, sMillisecond);
}

// Удалить беседу
function removeConversation(conversationId) {
    var data = {id: conversationId};
    $.ajax({
        url: HOST + "/v1/messages/removeconversation?" + getAuthParams(),
        dataType: "json",
        type: "POST",
        data: data,
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
            //xhr.setRequestHeader("Cookie", 'email='+email+'; password='+password);
        },
        success: function (data) {
            //response( data );
            if (data['status'] === "OK") {

                window.location.href = '/messages';
                // Good query
                //$('#btn_addToFavorites' + id).css('display', 'inline');
                //$('#btn_removeFromFavorites' + id).css('display', 'none');

            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        }
    });
}

// Переименовать беседу
function renameConversation(conversationId, newName) {
    var data = {
        id: conversationId,
        name: newName
    };
    $.ajax({
        url: HOST + "/v1/messages/renameconversation?" + getAuthParams(),
        dataType: "json",
        type: "POST",
        data: data,
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
        },
        success: function (data) {
            //response( data );
            if (data['status'] === "OK") {

                window.location.href = '/messages/conversation?id=' + conversationId;

            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        }
    });
}

// Обновить фото беседы
function refreshPhotoConversation(conversationId, photoBase64) {
    var data = {
        id: conversationId,
        photoBase64: photoBase64
    };
    $.ajax({
        url: HOST + "/v1/messages/refreshphotoconversation?" + getAuthParams(),
        dataType: "json",
        type: "POST",
        data: data,
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
        },
        success: function (data) {
            //response( data );
            if (data['status'] === "OK") {

                window.location.href = '/messages/conversation?id=' + conversationId;

            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        }
    });
}

// Выйти из беседы
function leaveFromConversation(conversationId) {
    var data = {id: conversationId};
    $.ajax({
        url: HOST + "/v1/messages/leaveconversation?" + getAuthParams(),
        dataType: "json",
        type: "POST",
        data: data,
        xhrFields: {
            withCredentials: false
        },
        beforeSend: function (xhr) {
        },
        success: function (data) {
            //response( data );
            if (data['status'] === "OK") {

                window.location.href = '/messages';

            }
            // Receive errors
            if (data['errors'] != null) {
                showErrors(data['errors']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // Errors query
        }
    });
}
