// Прикрепленный опрос к новой записи
var attPollChoices = [];

// Отрабатывает при сркытии диалогвого окна с опросом (для прикрепления к записи) Отображение прикрепил ли опрос
// пользователь
$('#attPollModal').on('hidden.bs.modal', function (e) {
    let theme = $('#themePoll').val();
    let firstChoice = $("#answerChoices input").first().val();
    if (theme != '' && theme != null && firstChoice != '' && firstChoice != null) {
        $('#attPollIcon').css('display', 'inline-block');

        attPollChoices = [];
        $.each($("#answerChoices input"), function () {
            if (this.value != "" && this.value != null) {
                attPollChoices.push(this.value);
            }
        });
    } else {
        $('#attPollIcon').css('display', 'none');
        attPollChoices = [];
    }
})

// Удалить опрос (при добавлении новой записи на страницу)
function removePoll() {
    // Удаляем все кроме последнего варианта ответа
    for (var i = 0; i < 10; i++) {
        removeLastChoice();
    }
    // В первом варианте стираем значение
    $("#answerChoices input").last().val("");
    // Стираем значение в теме
    $('#themePoll').val("");
    $('#attPollIcon').css('display', 'none');
    attPollChoices = [];
}

// Добавить вариант ответа для опроса
function addChoice() {
    if ($('#answerChoices input').length < 10) {
        $('#answerChoices').append('<input type="text" class="form-control" name="answerChoice" placeholder="Введите вариант ответа" style="width: 100%;margin-top: 5px">');
    }
}

// Удалить последний вариант ответа для опроса
function removeLastChoice() {
    if ($('#answerChoices input').length > 1) {
        $("#answerChoices input").last().remove();
    }
}

// Открыть диалоговое окно со смайликами по нажатию Tab
$("#parentOfTextbox").on('keydown', '#text', function (e) {
    var keyCode = e.keyCode || e.which;

    if (keyCode == 9) {
        e.preventDefault();
        if ($('#myModal').is(':visible')) {
            $('#attSmilesModal').modal('hide');
        } else {
            $('#attSmilesModal').modal('show');
        }
    }
});

// Работа со смайликами
$(document).ready(function () {
    var smiles = $("#smilesList");
    var inputEl = $("#text");
    var smilesBtn = $("#smilesBtn");

    // Загружаем коды смайликов в диалоговое окно
    smiles.text(getSmilesCode());

    // Обновляем смайлики в тексте
    $('.post-message').emotions();

    smiles.emotions();

    $("#smilesList span").click(function () {
        $('#attSmilesModal').modal('hide')
        //var shortCode = $.emotions.shortcode($(this).attr("title"));
        // Вставляем смайлик на место курсора
        //inputEl.text(inputEl.text() + " " + shortCode + " ");
        var content = document.querySelector('[data-target="insert"]')
        insertHTML('&nbsp;' + $(this).get(0).outerHTML + '&nbsp;', content);
        //ins2pos($(this).get(0).outerHTML+'&nbsp;', 'message');

        inputEl.focus();
    });

    function insertHTML(html, el) {
        var sel, range;
        if (window.getSelection) {
            sel = window.getSelection();
            if (elementContainsSelection(el)) {
                if (sel.getRangeAt && sel.rangeCount) {
                    range = sel.getRangeAt(0);
                    range.deleteContents();
                    var el = document.createElement("div");
                    el.innerHTML = html;
                    var frag = document.createDocumentFragment(),
                        node, lastNode;
                    while ((node = el.firstChild)) {
                        lastNode = frag.appendChild(node);
                    }
                    range.insertNode(frag);

                    if (lastNode) {
                        range = range.cloneRange();
                        range.setStartAfter(lastNode);
                        range.collapse(false);
                        sel.removeAllRanges();
                        sel.addRange(range);
                    }
                } else if (document.selection && document.selection.type != "Control") {
                    document.selection.createRange().pasteHTML(html);
                }
            } else {
                setEndOfContenteditable(el);
                insertHTML(html, el);
            }
        }
    }

    function setEndOfContenteditable(contentEditableElement) {
        var range, selection;
        if (document.createRange) {
            range = document.createRange();
            range.selectNodeContents(contentEditableElement);
            range.collapse(false);
            selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);
        } else if (document.selection) {
            range = document.body.createTextRange();
            range.moveToElementText(contentEditableElement);
            range.collapse(false);
            range.select();
        }
    }

    function elementContainsSelection(el) {
        var sel;
        if (window.getSelection) {
            sel = window.getSelection();
            if (sel.rangeCount > 0) {
                for (var i = 0; i < sel.rangeCount; ++i) {
                    if (!isOrContains(sel.getRangeAt(i).commonAncestorContainer, el)) {
                        return false;
                    }
                }
                return true;
            }
        } else if ((sel = document.selection) && sel.type != "Control") {
            return isOrContains(sel.createRange().parentElement(), el);
        }
        return false;
    }

    function isOrContains(node, container) {
        while (node) {
            if (node === container) {
                return true;
            }
            node = node.parentNode;
        }
        return false;
    }
});

// Прикрепленные файлы к новой записи
var attFiles = "";

// Формируем строку с выбранными файлами для сообщения
$('#attFileModal').on('hidden.bs.modal', function (e) {
    // Записываем все выбранные файлы в строку
    attFiles = "";
    // Если выбрано файлов до 10 (включительно) то всё ОК, больше - нельзя, ошибка
    if ($('[name="selectedFiles"]:checked').length <= 10) {
        $('[name="selectedFiles"]:checked').each(function () {
            attFiles += $(this).val() + '|';
        });
        if (attFiles != "") {
            $('#attFileIcon').css('display', 'inline-block');
        } else {
            $('#attFileIcon').css('display', 'none');
        }
    } else {
        showErrors(["Допустимо выбрать не более 10 файлов."]);
        $('#attFileIcon').css('display', 'none');
        $('#filesList').empty();
    }
})

// Загружаем список файлов (чтобы можно было прикрепить к сообщению)
$('#attFileModal').on('show.bs.modal', function (e) {
    if ($('#filesList').text() == '') {
        var res = getFilesOfUser();
        if (res != null) {
            for (let i = 0; i < res.length; i++) {
                var itemFile = '';

                var id = res[i]['id'];
                var accountId = res[i]['account_id'];
                var fileName = res[i]['file_name'];
                var datetimeAdd = res[i]['datetime_add'];
                var path = res[i]['path'];
                var fileSizeBytes = res[i]['file_size_bytes'];

                itemFile += "<div class=\"custom-control custom-checkbox\">\n" +
                    "  <input type=\"checkbox\" class=\"custom-control-input\" value='" + id + "' name='selectedFiles' id='file" + id + "' >\n" +
                    "  <label class=\"custom-control-label\" for=\"file" + id + "\">" + fileName + "</label>\n" +
                    "</div>";

                // Добавляем новый пост в список других
                $('#filesList').prepend(itemFile);
            }
        }
    }
})

// Отрабатывает при сркытии диалогвого окна с YouTube видео (для прикрепления к сообщению)
$('#attVideoYTModal').on('hidden.bs.modal', function (e) {
    var linkId = $('#video_link').val();
    if (linkId != '' && linkId != null) {
        $('#attVideoYTIcon').css('display', 'inline-block');
    } else {
        $('#attVideoYTIcon').css('display', 'none');
    }
})

/*// Загружать +10 записей, если пользователь прокрутил страницу до конца (пагинация)
$(window).scroll(function(){
    if($(window).scrollTop()+$(window).height()>=$(document).height()){
        var idUser = $('#account_to_id').val();
        var offset = $("#Posts > div").length;
        getPosts(idUser, 10, offset);
    }
})*/

// Если элементов нет, то скрываем кнопку пагинации Показать ещё
if ($("#searchResult > li").length === 2) {
    $('#show_more').remove();
}

// Загружать +10 записей
function showMore() {
    $('#show_more').css('display', 'none');
    let idUser = $('#account_to_id').val();
    let offset = $("#Posts > div").length;
    let myId = $('#myId').val();
    getPosts(idUser, 10, offset, myId);
}

// Validation form add post
function validateAddPost() {
    let accountToId = $('#account_to_id').val();
    let text = $('#text').get(0).innerHTML;
    let attPhoto = $('#att_photoCreatePost').val();
    let videoLink = $('#video_link').val();
    let files = attFiles;
    let pollTheme = $('#themePoll').val();
    let pollChoices = attPollChoices;
    let pollAnon = $('#addPollAnon').is(':checked');

    if (text == '' && attPhoto == '' && videoLink == '' && files == '' && pollChoices.length == 0) {
        $('#errorAddPost').css('display', 'block');
        return false;
    }

    // Если в опросе ответов нет, то тему не берем, на отправку
    if (pollChoices.length == 0) {
        pollTheme = null;
    }

    // Check file image on valid extension
    let exts = ['png', 'gif', 'jpg', 'jpeg'];//extensions
    if (attPhoto) {
        let getExt = attPhoto.split('.');
        getExt = getExt.reverse();
        if ($.inArray(getExt[0].toLowerCase(), exts) > -1) {
            //console.log('Allowed extension!');
        } else {
            //console.log( 'Fail extension!');
            $('#att_photoCreatePost').next('.custom-file-label').html('<p style="color: red;">Только файлы jpg, png, jpeg, gif!</p>');
            return false;
        }
    }

    // Если запись с фото
    let file = document.getElementById("att_photoCreatePost").files;
    if (file.length > 0) {
        let fileToLoad = file[0];
        let fileReader = new FileReader();
        fileReader.onload = function (fileLoadedEvent) {
            let srcData = fileLoadedEvent.target.result; // <--- data: base64
            addPost(accountToId, text, srcData, videoLink, files, pollTheme, pollChoices, pollAnon); // Добавляем запись (с изображением)

            $('#attPhotoIcon').css('display', 'none');
            $('#attVideoYTIcon').css('display', 'none');
            $('#attFileIcon').css('display', 'none');
            $('#video_link').val("");
            $('#filesList').empty();
            attFiles = "";
            removePoll();
        }
        fileReader.readAsDataURL(fileToLoad);
        return true;
    }

    // Добавляем запись (без изображения)
    addPost(accountToId, text, '', videoLink, files, pollTheme, pollChoices, pollAnon);

    $('#attPhotoIcon').css('display', 'none');
    $('#attVideoYTIcon').css('display', 'none');
    $('#attFileIcon').css('display', 'none');
    $('#video_link').val("");
    $('#filesList').empty();
    attFiles = "";
    removePoll();

    return true;
}

// Validation form send message
function validateSendMessage() {
    var message = $('#message').val();
    var attPhotoNewMessage = $('#att_photo_newMessage').val();
    if (message == '' && attPhotoNewMessage == '') {
        return false;
    }

    // Check file image on valid extension
    var exts = ['png', 'gif', 'jpg', 'jpeg'];//extensions
    if (attPhotoNewMessage) {
        var getExt = attPhotoNewMessage.split('.');
        getExt = getExt.reverse();
        if ($.inArray(getExt[0].toLowerCase(), exts) > -1) {
            //console.log('Allowed extension!');
        } else {
            //console.log( 'Fail extension!');
            $('#att_photo_newMessage').next('.custom-file-label').html('<p style="color: red;">Только файлы jpg, png, jpeg, gif!</p>');
            return false;
        }
    }
    return true;
}

// View select image-file
$('#att_photoCreatePost').on('change', function () {
    if (event.target.files.length != 0) {
        var fileName = event.target.files[0].name;
        var attPhoto = $('#att_photoCreatePost').val();
        // Check file image on valid extension
        var exts = ['png', 'jpg', 'jpeg', 'gif'];//extensions
        if (attPhoto) {
            var getExt = attPhoto.split('.');
            getExt = getExt.reverse();
            if ($.inArray(getExt[0].toLowerCase(), exts) > -1) {
                //console.log('Allowed extension!');
                $(this).next('.custom-file-label').html(fileName);
                $('#attPhotoModal').modal('hide')
                $('#attPhotoIcon').css('display', 'inline-block');
            } else {
                //console.log( 'Fail extension!');
                $(this).next('.custom-file-label').html('<p style="color: red;">Только файлы jpg, png, jpeg, gif!</p>');
                $('#attPhotoIcon').css('display', 'none');
            }
        }
    } else {
        $('#attPhotoIcon').css('display', 'none');
        $('#att_photo_newMessage').next('.custom-file-label').html('Загрузить изображение...');
    }
})
$('#update_photo').on('change', function () {
    var fileName = event.target.files[0].name;
    var attPhoto = $('#update_photo').val();
    // Check file image on valid extension
    var exts = ['png', 'jpg', 'jpeg', 'gif'];//extensions
    if (attPhoto) {
        var getExt = attPhoto.split('.');
        getExt = getExt.reverse();
        if ($.inArray(getExt[0].toLowerCase(), exts) > -1) {
            //console.log('Allowed extension!');
            $(this).next('.custom-file-label').html(fileName);
        } else {
            //console.log( 'Fail extension!');
            $(this).next('.custom-file-label').html('<p style="color: red;">Только файлы jpg, png, jpeg, gif!</p>');
        }
    }
})
$('#att_photo_newMessage').on('change', function () {
    var fileName = event.target.files[0].name;
    var attPhoto = $('#att_photo_newMessage').val();
    // Check file image on valid extension
    var exts = ['png', 'jpg', 'jpeg', 'gif'];//extensions
    if (attPhoto) {
        var getExt = attPhoto.split('.');
        getExt = getExt.reverse();
        if ($.inArray(getExt[0].toLowerCase(), exts) > -1) {
            //console.log('Allowed extension!');
            $(this).next('.custom-file-label').html(fileName);
        } else {
            //console.log( 'Fail extension!');
            $(this).next('.custom-file-label').html('<p style="color: red;">Только файлы jpg, png, jpeg, gif!</p>');
        }
    }
})

// Сделать ширину кнопки (обновить фото) под ширину изображения (аватарки)
$(document).ready(function () {
    var w = $("#img_photo_profile").outerWidth();
    $("#btn_uodatePhoto").css({"width": w});
});

// Всплывающая подсказка (Внедрение YouTube-видео)
$('#att_video_link_help').tooltip('enable');

// Всплывающая подсказка (Показать кто проголосовал в опросе (за конкретный вариант ответа))
$('[class="show-poll-voted"]').tooltip('enable');

// Всплывающая подсказка (Online/Offline help)
$('[name="status_visit"]').tooltip('enable');

// Всплывающая подсказка (Найти (поиск))
$('#tosearch').tooltip('enable');