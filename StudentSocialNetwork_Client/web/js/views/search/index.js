/*// Загружать +10 людей из поиска, если пользователь прокрутил страницу до конца (пагинация)
    $(window).scroll(function(){
        if($(window).scrollTop()+$(window).height()==$(document).height()){

        }
    })*/

// Если элементов нет, то скрываем кнопку пагинации Показать ещё
if ($("#searchResult > li").length === 2) {
    $('#show_more').remove();
}

// Загружать +10 людей из поиска
function showMore() {
    $('#show_more').css('display', 'none');
    var offset = $("#searchResult > li").length - 2;
    var query = $('#query_text').val()
    getSearchUsers(query, 1000, 0);
}