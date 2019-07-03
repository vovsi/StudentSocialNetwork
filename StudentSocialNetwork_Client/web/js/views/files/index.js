// Download file
function downloadFile(path) {
    var link = document.createElement('a');
    link.setAttribute('href', path);
    link.setAttribute('target', '_blank');
    link.setAttribute('download', 'download');
    onload = link.click();
}

// View select file for load
$('#load_file').on('change', function () {
    var fileName = event.target.files[0].name;
    var file = $('#load_file').val();
    // Check file on valid extension
    var exts = ['pdf', 'ppt', 'pptx', 'rar', 'txt', 'doc', 'docx', 'dot', 'docm', 'dotx', 'dotm', 'docb', 'xls', 'xlt', 'xlm', 'xlsx', 'xlsx',
        'xlsm', 'xltx', 'xltm', 'zip'];//extensions
    if (file) {
        var fileSize = event.target.files[0].size / 1024 / 1024;
        var getExt = file.split('.');
        getExt = getExt.reverse();
        if (fileSize <= 10) {
            if ($.inArray(getExt[0].toLowerCase(), exts) > -1) {
                //console.log('Allowed extension!');
                $(this).next('.custom-file-label').html(file);
                $('#btnLoad').prop('disabled', false);
            } else {
                //console.log( 'Fail extension!');
                $(this).next('.custom-file-label').html('<p style="color: red;">Недопустимый формат!</p>');
                $('#btnLoad').prop('disabled', true);
            }
        } else {
            $(this).next('.custom-file-label').html('<p style="color: red;align-content: left">Размер больше 10 Мб!</p>');
            $('#btnLoad').prop('disabled', true);
        }
    }
});