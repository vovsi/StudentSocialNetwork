// Загружать +10 фото, если пользователь прокрутил страницу до конца (пагинация)
/*$(window).scroll(function(){
    if($(window).scrollTop()+$(window).height()>=$(document).height()){
        var offset = $("#gallery > div").length;
        getPhotos($('#idUser').val(), 10, offset);
    }
})*/

// Выводим в полный экран выбранное фото (в виде модального окна)
$('#photoModal').on('show.bs.modal', function (event) {
    var image = $(event.relatedTarget);
    var pathToImage = image.data('path');
    var idImage = image.data('id');
    var description = image.data('description');
    var datetimeAdd = image.data('datetime');
    var modal = $(this);
    modal.find('#modal_photo_fullsize').attr("src", pathToImage);
    modal.find('#removePhoto').attr("onclick", 'removePhotoFromAlbum(' + idImage + ')');
    modal.find('#description').text(description);
    modal.find('#datetime_add').text('Добавлено ' + datetimeAdd);
});