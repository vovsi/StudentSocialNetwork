// Изменяем ширину текста в соответствии с шириной окна
window.onresize = function (e) {
    if ($(window).width() <= '370') {
        $('#headerText').css('font-size', '180%');
        $('#underHeaderText').css('font-size', '80%');
    } else {
        $('#headerText').css('font-size', '350%');
        $('#underHeaderText').css('font-size', '100%');
    }
};