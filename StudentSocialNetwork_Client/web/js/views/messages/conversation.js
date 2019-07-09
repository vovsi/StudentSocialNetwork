function changeMembersConversationForm() {
    // id беседы
    let conversationId = $('#conversation_id').val();
    if (null != conversationId) {
        // Записываем всех выбранных участников в строку
        membersAddConversation = "";
        // Если выбрано участников до 10 (включительно) то всё ОК, больше - нельзя, ошибка
        if ($('[name="selectedMembersAddCon"]:checked').length <= 10) {
            if ($('[name="selectedMembersAddCon"]:checked').length > 0) {
                $('[name="selectedMembersAddCon"]:checked').each(function () {
                    membersAddConversation += $(this).val() + '|';
                });
                changeMembersConversation(conversationId, membersAddConversation);
            } else {
                showErrors(['Выберите хотя-бы 1 участника.']);
            }
        } else {
            showErrors(["Допустимо выбрать не более 10 участников."]);
        }
    } else {
        showErrors(['Не найден id беседы.']);
    }
}

// Загружаем список участников беседы
$('#membersConversationModal').on('show.bs.modal', function (e) {
    if ($('#membersList').text() == '') {
        let res = getMembersOfConversation($('#conversation_id').val());
        if (res != null) {
            // Отображаем кол-во участников в заголовке окна
            $('#countMembers').text(res.length);
            for (let i = 0; i < res.length; i++) {
                let itemMember = '';

                let id = res[i]['id'];
                let firstName = res[i]['first_name'];
                let lastName = res[i]['last_name'];
                let photoPath = getBase64FromUrlImage(res[i]['photo_path']);
                let statusVisit = res[i]['status_visit'];
                let statusMember = res[i]['status_member'];

                let statusMemberHtml = "";
                if (statusMember === 'creator') {
                    statusMemberHtml = " <sup style='font-weight: bold'>admin</sup>";
                }

                let statusVisitHtml = "";
                if (statusVisit == "online") {
                    statusVisitHtml = "<span name='status_visit' title='Этот пользователь находится сейчас на этом сайте.' class=\"badge badge-pill badge-success\" style='font-size: x-small'>online</span> ";
                } else {
                    statusVisitHtml = "<span name='status_visit' title='Этого пользователя сейчас нет на этом сайте.' class=\"badge badge-pill badge-danger\" style='font-size: x-small'>offline</span> ";
                }

                itemMember += "<div style='margin-bottom: 5px;'><a href='/" + id + "'>\n" +
                    "  <img src=\"" + photoPath + "\" class='rounded-circle' height='50px' width=\"50px\" style='margin-right: 25px' alt=\"img\"></a>" +
                    "  <a href='/" + id + "'><label>" + firstName + " " + lastName + "</label></a>\n" +
                    statusVisitHtml +
                    statusMemberHtml +
                    "</div>";

                // Добавляем пользователя в список других
                $('#membersList').prepend(itemMember);
            }
        }
    }
});

// Отобразить кол-во выбранных участников для беседы
function showCountSelectedMembers(id) {
    let currentCount = parseInt($('#countSelectedMembers').text(), 0);
    // Меняем значение кол-ва текущих выбранных участников
    if ($('#member' + id).prop('checked')) {
        currentCount = currentCount + 1;
    } else {
        currentCount = currentCount - 1;
    }
    // Если выбрано больше, чем можно, то выделяем красным кол-во
    if (currentCount > 10) {
        $('#spanSelectedMembers').addClass('badge-danger').removeClass('badge-info');
    } else {
        $('#spanSelectedMembers').addClass('badge-info').removeClass('badge-danger');
    }
    $('#countSelectedMembers').text(currentCount);
}

// Загружаем список избранных, и участников беседы из них, для редактирования участников беседы
$('#changeMembersConversationModal').on('show.bs.modal', function (e) {
    if ($('#membersFavoritesList').text() == '') {
        let creatorId = $('#my_id').val();
        let favorites = getFavoritesOfUser(1000, 0);
        let members = getMembersOfConversation($('#conversation_id').val());
        // Задаем кол-во выбранных участников
        $('#countSelectedMembers').text(members.length - 1);
        if (favorites != null && members != null) {
            for (let i = 0; i < favorites.length; i++) {
                let itemFavorite = '';

                let id = favorites[i]['user_favorite_id'];
                let firstName = favorites[i]['first_name'];
                let lastName = favorites[i]['last_name'];
                let photoPath = getBase64FromUrlImage(favorites[i]['photo_path']);
                let statusVisit = favorites[i]['status_visit'];

                // Если этот участник выбран, то выбираем его в списке checkboxes
                let selected = '';
                let res = members.find(f => f['id'] === id);
                if (null != res) {
                    selected = 'checked';
                }

                let statusVisitHtml = "";
                if (statusVisit == "online") {
                    statusVisitHtml = "<span name='status_visit' title='Этот пользователь находится сейчас на этом сайте.' class=\"badge badge-pill badge-success\" style='font-size: x-small'>online</span> ";
                } else {
                    statusVisitHtml = "<span name='status_visit' title='Этого пользователя сейчас нет на этом сайте.' class=\"badge badge-pill badge-danger\" style='font-size: x-small'>offline</span> ";
                }

                itemFavorite += "<div  data-toggle=\"member" + id + "\" class=\"custom-control custom-checkbox\" style='margin-bottom: 5px;'>\n" +
                    "  <img src=\"" + photoPath + "\" class='rounded-circle' height='50px' width=\"50px\" style='margin-right: 25px'>" +
                    "  <input " + selected + " onclick='showCountSelectedMembers(" + id + ")' type=\"checkbox\" class=\"custom-control-input\" value='" + id + "' name='selectedMembersAddCon' id='member" + id + "' >\n" +
                    "  <label class=\"custom-control-label\" for=\"member" + id + "\">" + firstName + " " + lastName + "</label>\n" +
                    statusVisitHtml +
                    "  <sup>Id: " + id + "</sup>" +
                    "</div>";

                // Добавляем пользователя в список других
                $('#membersFavoritesList').prepend(itemFavorite);
            }
        }
    }
});

// View select image-file
$('#refreshPhotoConversationInput').on('change', function () {
    var fileName = event.target.files[0].name;
    var attPhoto = $(this).val();
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
});

function refreshPhotoConversationSubmit() {
    let conversationId = $('#conversation_id').val();
    let file = document.getElementById("refreshPhotoConversationInput").files;
    if (file.length > 0) {
        let fileToLoad = file[0];
        let fileReader = new FileReader();
        fileReader.onload = function (fileLoadedEvent) {
            let srcData = fileLoadedEvent.target.result; // <--- data: base64
            refreshPhotoConversation(conversationId, srcData);
        };
        fileReader.readAsDataURL(fileToLoad);
        return true;
    }
}

function renameConversationSubmit() {
    let newName = $('#renameConversationInput').val();
    let conversationId = $('#conversation_id').val();
    if (newName != '') {
        renameConversation(conversationId, newName);
    }
}

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
    getMessagesFromConversation($('#my_id').val(), $('#conversation_id').val(), 10, offset);
}

// Validation form send message
function validateSendMessage() {
    var conversation_id = $('#conversation_id').val();
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
            sendMessageToConversation(conversation_id, text, srcData, files, videoYT); // Отправляем сообщение (с изображением)

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
    sendMessageToConversation(conversation_id, text, '', files, videoYT);

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