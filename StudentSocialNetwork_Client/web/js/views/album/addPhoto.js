// View select image-file
$('#photo').on('change', function () {
    var fileName = event.target.files[0].name;
    var attPhoto = $('#photo').val();
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