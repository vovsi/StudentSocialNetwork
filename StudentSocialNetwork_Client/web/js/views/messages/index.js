// Выбранные участники беседы (при создании)
var membersAddConversation = "";

// Формируем строку с выбранными участниками беседы (при создании)
function createConversationForm() {
    // Записываем всех выбранных участников в строку
    membersAddConversation = "";
    // Если выбрано участников до 10 (включительно) то всё ОК, больше - нельзя, ошибка
    if ($('[name="selectedMembersAddCon"]:checked').length <= 10) {
        if ($('[name="selectedMembersAddCon"]:checked').length > 0) {
            $('[name="selectedMembersAddCon"]:checked').each(function () {
                membersAddConversation += $(this).val() + '|';
            });

            let name = $('#addConversationName').val();
            let photo = $('#addConversationImage').val();
            if (name != '') {
                // Check file image on valid extension
                let exts = ['png', 'gif', 'jpg', 'jpeg'];//extensions
                if (photo) {
                    let getExt = photo.split('.');
                    getExt = getExt.reverse();
                    if ($.inArray(getExt[0].toLowerCase(), exts) > -1) {
                        //console.log('Allowed extension!');
                    } else {
                        //console.log( 'Fail extension!');
                        $('#addConversationImage').next('.custom-file-label').html('<p style="color: red;">Только файлы jpg, png, jpeg, gif!</p>');
                        return false;
                    }
                }

                // Если запись с фото
                let file = document.getElementById("addConversationImage").files;
                if (file.length > 0) {
                    let fileToLoad = file[0];
                    let fileReader = new FileReader();
                    fileReader.onload = function (fileLoadedEvent) {
                        let srcData = fileLoadedEvent.target.result; // <--- data: base64

                        createConversation(name, membersAddConversation, srcData); // Создаем беседу (с изображением)

                    }
                    fileReader.readAsDataURL(fileToLoad);
                    return true;
                }

                // Создаем беседу без изображения
                createConversation(name, membersAddConversation);

            } else {
                showErrors(["Заполните поле названия беседы."]);
            }
        } else {
            showErrors(["Выберите хотя-бы 1 участника."]);
        }
    } else {
        showErrors(["Допустимо выбрать не более 10 участников."]);
    }
}

// Загружаем список избранных (чтобы можно было добавить в беседу)
$('#addConversationModal').on('show.bs.modal', function (e) {
    if ($('#membersFavoritesList').text() == '') {
        var res = getFavoritesOfUser(1000, 0);
        if (res != null) {
            for (let i = 0; i < res.length; i++) {
                var itemFavorite = '';

                var id = res[i]['user_favorite_id'];
                var firstName = res[i]['first_name'];
                var lastName = res[i]['last_name'];
                var photoPath = getBase64FromUrlImage(res[i]['photo_path']);
                var statusVisit = res[i]['status_visit'];

                let statusVisitHtml = "";
                if (statusVisit == "online") {
                    statusVisitHtml = "<span name='status_visit' title='Этот пользователь находится сейчас на этом сайте.' class=\"badge badge-pill badge-success\" style='font-size: x-small'>online</span> ";
                } else {
                    statusVisitHtml = "<span name='status_visit' title='Этого пользователя сейчас нет на этом сайте.' class=\"badge badge-pill badge-danger\" style='font-size: x-small'>offline</span> ";
                }

                itemFavorite += "<div  data-toggle=\"member" + id + "\" class=\"custom-control custom-checkbox\" style='margin-bottom: 5px;'>\n" +
                    "  <img src=\"" + photoPath + "\" class='rounded-circle' height='50px' width=\"50px\" style='margin-right: 25px'>" +
                    "  <input onclick='showCountSelectedMembers(" + id + ")' type=\"checkbox\" class=\"custom-control-input\" value='" + id + "' name='selectedMembersAddCon' id='member" + id + "' >\n" +
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

// View select image-file
$('#addConversationImage').on('change', function () {
    var fileName = event.target.files[0].name;
    var attPhoto = $('#addConversationImage').val();
    // Check file image on valid extension
    var exts = ['png', 'jpg', 'jpeg', 'gif'];//extensions
    if (attPhoto) {
        var getExt = attPhoto.split('.');
        getExt = getExt.reverse();
        if ($.inArray(getExt[0].toLowerCase(), exts) > -1) {
            //console.log('Allowed extension!');
            $(this).next('.custom-file-label').html(fileName);
            $('#createConversation').prop("disabled", false); // Разблокируем кнопку добавления
        } else {
            //console.log( 'Fail extension!');
            $(this).next('.custom-file-label').html('<p style="color: red;">Только файлы jpg, png, jpeg, gif!</p>');
            $('#createConversation').prop("disabled", true); // Блокируем кнопку добавления
        }
    }
})

// При успешной загрузке страницы
$(document).ready(function () {
    // Форматируем кода смайликов в картинки
    $('p').emotions();
});

/*// Загружать +10 диалогов, если пользователь прокрутил страницу до конца (пагинация)
$(window).scroll(function(){
    if($(window).scrollTop()+$(window).height()>=$(document).height()){
        var offset = $("#Dialogs > a").length;
        getDialogs(10, offset);
    }
})*/

// Если элементов нет, то скрываем кнопку пагинации Показать ещё
if ($("#searchResult > li").length === 2) {
    $('#show_more').remove();
}

// Загружать +10 диалогов
function showMoreDialogs() {
    var offset = $("#Dialogs > a").length;
    getDialogs(10, offset);
}

// Загружать +10 бесед
function showMoreConversations() {
    var offset = $("#Conversations > a").length;
    getConversations(10, offset);
}