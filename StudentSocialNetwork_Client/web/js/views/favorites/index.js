/*// Загружать +10 людей из избранных, если пользователь прокрутил страницу до конца (пагинация)
    $(window).scroll(function(){
        if($(window).scrollTop()+$(window).height()>=$(document).height()){
            var offset = $("#favoritesUsers > li").length - 1;
            getFavorites(10, offset);
        }
    })*/

// Если элементов нет, то скрываем кнопку пагинации Показать ещё
if ($("#searchResult > li").length === 2) {
    $('#show_more').remove();
}

// Загружать +10 людей из избранных
function showMore() {
    $('#show_more').css('display', 'none');
    var offset = $("#favoritesUsers > li").length - 1;
    getFavorites(10, offset);
};