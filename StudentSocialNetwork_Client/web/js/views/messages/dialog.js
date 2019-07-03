// Открыть диалоговое окно со смайликами по нажатию Tab
$("#parentOfTextbox").on('keydown', '#message', function (e) {
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
    var inputEl = $("#message");
    var smilesBtn = $("#smilesBtn");
    var messages = $("#messages");

    // Загружаем коды смайликов в диалоговое окно
    smiles.text(getSmilesCode());

    // Обновляем смайлики мои
    $('div.sent_msg>p').emotions();
    // Обновляем смайлики собеседника
    $('div.received_withd_msg>p').emotions();

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

// Добавить ткст в место курсора (работает только с текстом)
/*function ins2pos(str, id) {
    var TextArea = document.getElementById(id);
    var val = TextArea.innerHTML;
    var before = val.substring(0, TextArea.selectionStart);
    var after = val.substring(TextArea.selectionEnd, val.length);
    TextArea.innerHTML = before + str + after;
}*/

// Прикрепленные файлы к новому сообщению
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
});

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
    var linkId = $('#att_video_link').val();
    if (linkId != '' && linkId != null) {
        $('#attVideoYTIcon').css('display', 'inline-block');
    } else {
        $('#attVideoYTIcon').css('display', 'none');
    }
})

// Запустить таймер получения новых (непрочитанных) сообщений
/*var timerId = setTimeout(function tick() {
    getNotViewedMessagesFromDialog($('#dialog_id').val());
    timerId = setTimeout(tick, 5000);
}, 5000);*/

// Отобразить кнопку Показать ещё
$('#messages').scroll(function () {
    if ($('#messages').scrollTop() == 0) {
        $('#show_more').css('display', 'inline-block');
    } else {
        $('#show_more').css('display', 'none');
    }
})

// Если элементов нет, то удаляем кнопку пагинации Показать ещё
if ($("#searchResult > li").length === 2) {
    $('#show_more').remove();
}

// Загружать +10 сообщений
function showMore() {
    $('#show_more').css('display', 'none');
    var offset = $("#messages > div").length - 1;
    getMessagesFromDialog($('#dialog_id').val(), 10, offset);
}

// Validation form send message
function validateSendMessage() {
    var accountToId = $('#account_to_id').val();
    var text = $('#message').get(0).innerHTML;
    var attPhoto = $('#att_photo_newMessage').val();
    var files = attFiles;
    var videoYT = $('#att_video_link').val();

    // Check field that they not empty
    if (text == '' && attPhoto == '' && files == "" && videoYT == '') {
        return false;
    }

    // Check file image on valid extension
    var exts = ['png', 'gif', 'jpg', 'jpeg']; //extensions
    if (attPhoto) {
        var getExt = attPhoto.split('.');
        getExt = getExt.reverse();
        if ($.inArray(getExt[0].toLowerCase(), exts) > -1) {
            // Allowed extension
        } else {
            // Fail extension
            $('#att_photo_newMessage').next('.custom-file-label').html('<p style="color: red;">Только файлы jpg, png, jpeg, gif!</p>');
            return false;
        }
    }

    // Если сообщение с фото
    var file = document.getElementById("att_photo_newMessage").files;
    if (file.length > 0) {
        var fileToLoad = file[0];
        var fileReader = new FileReader();
        fileReader.onload = function (fileLoadedEvent) {
            var srcData = fileLoadedEvent.target.result; // <--- data: base64
            sendMessageToDialog(accountToId, text, srcData, files, videoYT); // Отправляем сообщение (с изображением)

            $('#attPhotoIcon').css('display', 'none');
            $('#attVideoYTIcon').css('display', 'none');
            $('#attFileIcon').css('display', 'none');
            $('#att_video_link').val("");
            $('#filesList').empty();
            attFiles = "";
        }
        fileReader.readAsDataURL(fileToLoad);
        return true;
    }

    // Отправляем сообщение (без изображения)
    sendMessageToDialog(accountToId, text, '', files, videoYT);

    $('#attPhotoIcon').css('display', 'none');
    $('#attVideoYTIcon').css('display', 'none');
    $('#attFileIcon').css('display', 'none');
    $('#att_video_link').val("");
    $('#filesList').empty();
    attFiles = "";

    return true;
}

// View select image-file
$('#att_photo_newMessage').on('change', function () {
    if (event.target.files.length != 0) {
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
                $('#attPhotoModal').modal('hide')
                $('#attPhotoIcon').css('display', 'inline-block');
            } else {
                //console.log( 'Fail extension!');
                $('#att_photo_newMessage').next('.custom-file-label').html('<p style="color: red;">Только файлы jpg, png, jpeg, gif!</p>');
                $('#attPhotoIcon').css('display', 'none');
            }
        }
    } else {
        $('#attPhotoIcon').css('display', 'none');
        $('#att_photo_newMessage').next('.custom-file-label').html('Загрузить изображение...');
    }
})

// Сделать ширину листа с сообщениями под ширину рамки для сообщений, и пролистать к последнему сообщению
$(document).ready(function () {
    var w = $('#messages').scrollTop($('#messages').height() * $('#messages').height())

    $("#message").css({"width": w});
});

// Выводим в полный экран выбранное фото (в виде модального окна)
$('#photo').on('show.bs.modal', function (event) {
    var image = $(event.relatedTarget)
    var pathToImage = image.data('whatever')
    var modal = $(this)
    modal.find('#modal_photo_fullsize').attr("src", pathToImage)
})

// Отправка сообщения по клавише Enter (Enter+Shift - перенос строки)
$("#message").keypress(function (e) {
    if (e.which == 13 && !e.shiftKey) {
        $('#send_mess_submit').click();
        e.preventDefault();
        return false;
    }
});

// Всплывающая подсказка (Отправить сообщение)
$('#send_mess_submit').tooltip('enable')
// Всплывающая подсказка (Внедрение YouTube-видео)
$('#att_video_link_help').tooltip('enable')