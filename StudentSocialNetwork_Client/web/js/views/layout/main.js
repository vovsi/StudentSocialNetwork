$(document).ready(function () {
    $('.nav-link-collapse').on('click', function () {
        $('.nav-link-collapse').not(this).removeClass('nav-link-show');
        $(this).toggleClass('nav-link-show');
    });
});

// Запустить таймер обновления статуса непрочитанных групп сообщений (диалогов, бесед) (в шапке сайта)
var timerId = setTimeout(function tick() {
    refreshNotViewedGroupMsgs();
    timerId = setTimeout(tick, 5000);
}, 5000);

// Validations fields
(function () {
    'use strict';
    window.addEventListener('load', function () {
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.getElementsByClassName('needs-validation');
        // Loop over them and prevent submission
        var validation = Array.prototype.filter.call(forms, function (form) {
            form.addEventListener('submit', function (event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

// Всплывающая подсказка (Найти (поиск))
$('#tosearch').tooltip('enable');

// Всплывающая подсказка (Online/Offline help)
$('[name="status_visit"]').tooltip('enable');

// Всплывающая подсказка (upper button)
$('[class="topbutton"]').tooltip('enable');