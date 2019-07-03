// Удалить последний вариант ответа для опроса
function removeLastChoice() {
    if ($('#answerChoices input').length > 1) {
        $("#answerChoices input").last().remove();
    }
}

// Добавить вариант ответа для опроса
function addChoice() {
    if ($('#answerChoices input').length < 10) {
        $('#answerChoices').append('<input type="text" class="form-control" name="answerChoice[]" placeholder="Введите вариант ответа" style="width: 100%;margin-top: 5px">');
    }
}

// View select image-file
$('#image').on('change', function () {
    var fileName = event.target.files[0].name;
    var attPhoto = $('#image').val();
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

// Всплывающая подсказка (Добавление ссылки на видео из ютуба)
$('#video_link_help').tooltip('enable')

// Всплывающая подсказка (Внедрение YouTube-видео)
$('#video_link_help').tooltip('enable')

// Всплывающая подсказка (Создание события)
$('#icon_event').tooltip('enable')