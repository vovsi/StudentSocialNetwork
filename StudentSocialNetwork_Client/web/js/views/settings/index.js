/*// Загружать +10 людей из чёрного списка, если пользователь прокрутил страницу до конца (пагинация)
    $(window).scroll(function(){
        if($(window).scrollTop()+$(window).height()>=$(document).height()){
            var offset = $("#black_list > li").length;
            getBlackList(10, offset);
        }
    })*/

// Если элементов нет, то скрываем кнопку пагинации Показать ещё
if ($("#searchResult > li").length === 2) {
    $('#show_more').remove();
}

// Загружать +10 людей из чёрного списка
function showMore() {
    $('#show_more').css('display', 'none');
    var offset = $("#black_list > li").length;
    getBlackList(10, offset);
}

// Всплывающая подсказка (Личные данные (% заполнение профиля))
$('#progressBarFillPrivateData').tooltip('enable')

// Set precent fill private data
window.onload = function (e) {
    var countAllInput = 10; // Count all inputs

    // Value of all inputs
    var valInputs = [
        $('#first_name').val(),
        $('#last_name').val(),
        $('#patronymic').val(),
        $('#email').val(),
        $('#gender').val(),
        $('#phone_number').val(),
        $('#activities').val(),
        $('#interests').val(),
        $('#about_me').val(),
        $('#date_birthday').val()
    ];
    var countFillInputs = 0;
    for (var i = 0; i < valInputs.length; i++) {
        if (valInputs[i] != '') {
            countFillInputs++;
        }
    }
    var percent = countFillInputs / countAllInput * 100;
    var progressBar = $('#progressBarFillPrivateData');
    progressBar.css('width', percent + '%');
    progressBar.attr('aria-valuenow', percent);
    progressBar.text(percent + '%');
}