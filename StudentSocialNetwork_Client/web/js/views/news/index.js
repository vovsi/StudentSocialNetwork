// Отобразить/Скрыть календарь событий
$(".toggle-btn").click(function (e) {
    e.preventDefault();
    $(this).prev().toggleClass("collapse-open")
});

// Загрузить календарь событий
$(function () {
    //$("#incContentCalendarEvents").css('width', $(window).width());
    $("#incContentCalendarEvents").load("/html/calendarEvents.html");
});

/*// Загружать +10 новостей, если пользователь прокрутил страницу до конца (пагинация)
$(window).scroll(function(){
    if($(window).scrollTop()+$(window).height()>=$(document).height()){
        var offset = $("#News > div").length;
        getNews(10, offset);
    }
})*/

// Если элементов нет, то скрываем кнопку пагинации Показать ещё
if ($("#searchResult > li").length === 2) {
    $('#show_more').remove();
}

// Загружать +10 новостей
function showMore() {
    $('#show_more').css('display', 'none');
    var offset = $("#News > div").length;
    getNews(10, offset);
}

// Выводим в полный экран выбранную новость (в виде модального окна)
$('#fullViewNewsModal').on('show.bs.modal', function (event) {
    var news = $(event.relatedTarget);
    showFullOneNews(news.data('id'), $('#myId').val());
});

// Всплывающая подсказка (Новость-событие)
$('[name="icon_event"]').tooltip('enable');

// Всплывающая подсказка (Дата события)
$('[name="event_date"]').tooltip('enable');

// Всплывающая подсказка (Дата добавления новости)
$('[name="date_add"]').tooltip('enable');