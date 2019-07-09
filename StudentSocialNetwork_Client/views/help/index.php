<?php

use yii\helpers\Url;

?>

<title>Справка</title>
<li class="list-group-item">Справка</li>

<div class="accordion">
    <div id="profile" class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                        data-target="#collapseProfile" aria-expanded="false">
                    Профиль
                </button>
            </h5>
        </div>

        <div id="collapseProfile" class="collapse">
            <div class="card-body text-left">
                Вкладка <b>Профиль</b> содержит имя, фамилию и отчество пользователя, статус онлайна (посещения), его
                основную фотографию, основную информацию
                о пользователе, а также подробную (опциональную) информацию о пользователе. Каждый пользователь имеет
                возможность добавлять текстовые записи (с возможностью прикрепить фото, видео, файлы, опрос...) к себе
                на
                страницу. Каждый пользователь
                имеет возможность обновить его основную фотографию на странице профиля.<br/>
                Рядом с каждой записью на странице есть иконка крестика, при нажатии на которую происходит удаление
                конкретной записи.
            </div>
        </div>
    </div>
    <div id="news" class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseNews"
                        aria-expanded="false">
                    Новости
                </button>
            </h5>
        </div>

        <div id="collapseNews" class="collapse">
            <div class="card-body text-left">
                Вкладка <b>Новости</b> содержит список новостей университета. <br/>
                Выделенным чёрным цветом указывается тема новости, в конце темы дата добавления новости, а далее
                описание.
                В случае если описание слишком большое, то описание обрезается, и в конце краткого описания ставится
                троеточие.
                В таком случае, для просмотра полного описания - нужно кликнуть по новости. В открывшемся окне будет
                указана
                тема, полное описание, изображение, опрос и видео (если прикрепрелено) <br/>
                Новость может быть событием, отличие в том, что такая новость имеет дату начала любого события
                (мероприятие,
                встреча, семинар и т.д.) такая новость помечается красной отметкой, и указывается место встречи этого
                события.
                В календаре можно увидеть круглые точки на определенных днях - это означает что в этот день есть
                какое-то
                событие. Для просмотра - кликните на эту дату.
            </div>
        </div>
    </div>
    <div id="dialog" class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseDialog"
                        aria-expanded="false">
                    Сообщения
                </button>
            </h5>
        </div>

        <div id="collapseDialog" class="collapse">
            <div class="card-body text-left">
                Вкладка <b>Сообщения</b> содержит список диалогов и бесед вас, с пользователями. <br/>
                При первой авторизации список диалогов и бесед пуст. Чтобы начать общение с каким-либо пользователем,
                нужно
                открыть вкладку профиля пользователя, нажать кнопку "Написать сообщение" заполнить поля нужными данными,
                и отправить сообщение. Создайте беседу, либо попросите добавить вас в неё.<br/>
                Найти пользователя можно двумя способами, через:
                <ul>
                    <li>Адресную строку. ID профиля указывается после адреса сайта с символом "/" в конце.</li>
                    <li>Поиск. В шапке сайта справа находится поиск по пользователям, в нем можно указать имя, фамилию,
                        отчество пользователя.
                    </li>
                </ul>
                В конце сообщения указывается дата отправки последнего сообщения в диалоге (от вас, или
                собеседника)<br/><br/>
                При открытии диалога или беседы отображается список всех сообщений конкретного диалога или беседы. Слева
                расположены сообщения собеседника, а справа - ваши. Если у вас есть непрочитанное сообщение, то возле
                вкладки "Сообщения" будет отображено число непрочитанных диалогов и бесед. А при просмотре списков
                диалогов, рядом отображено число непрочитанных сообщений в аналогично и с беседами.
            </div>
        </div>
    </div>
    <div id="album" class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseAlbum"
                        aria-expanded="false">
                    Альбом
                </button>
            </h5>
        </div>

        <div id="collapseAlbum" class="collapse">
            <div class="card-body text-left">
                Вкладка <b>Альбом</b> содержит галерею вами загреженных фотографий. При открытии любой фотографии
                отображается
                окно на весь экран, и под фотографией - описание к ней с датой добавления. <br/>
                При нажатии на кнопку "Добавить" (над галереей) откроется страница добавления новой фотографии к вам в
                галерею.
                В ней указывается файл изображения, и описание (опционально)
            </div>
        </div>
    </div>
    <div id="album" class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseFiles"
                        aria-expanded="false">
                    Файлы
                </button>
            </h5>
        </div>

        <div id="collapseFiles" class="collapse">
            <div class="card-body text-left">
                Вкладка <b>Файлы</b> содержит список файлов, которые вы можете загрузить. Это небольшое облачное
                хранилище
                которым вы можете пользоваться. <br/>
                Ограничения:<br/>
                <ul>
                    <li>Размер файла не более 20 мегабайт</li>
                    <li>Количество файлов на пользователя (максимум) - 100</li>
                    <li>Нельзя загружать одинаковые файлы (если имя и расширение совпадают с уже загруженым файлом)</li>
                </ul>
                Список доступных для загрузки файлов (расширения):<br/>
                'pdf','ppt','pptx','rar','txt','doc','docx','dot','docm','dotx','dotm','docb','xls','xlt','xlm','xlsx',
                'xlsm','xltx','xltm','zip'
            </div>
        </div>
    </div>
    <div id="favorites" class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseFavorites"
                        aria-expanded="false">
                    Избранные
                </button>
            </h5>
        </div>

        <div id="collapseFavorites" class="collapse">
            <div class="card-body text-left">
                Вкладка <b>Избранные</b> содержит список пользователей которых вы добавили в избранные (закладки). Это
                вкладка, предназначена для сохранения ссылок на пользователей, которых вы хотите пометить как особенных.
                Рядом с каждым пользователем есть кнопка "Убрать" которая удалит пользователя из списка избранных. <br/>
                Для добавления пользователя в избранные, нужно открыть профиль этого пользователя, и нажать на кнопку
                "Добавить в избранные" рядом с его основной фотографией, либо через опциональное меню в диалоге с этим
                пользователем.
            </div>
        </div>
    </div>
    <div id="settings" class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseSettings"
                        aria-expanded="false">
                    Настройки
                </button>
            </h5>
        </div>

        <div id="collapseSettings" class="collapse">
            <div class="card-body text-left">
                Вкладка <b>Настройки</b> содержит подменю с категориями настроек. <br/>
                Категории:
                <ul>
                    <li><b>Личные данные</b>. Редактирование таких данных как: имя, фамилия, отчество, email, пол, номер
                        телефона,
                        деятельность, интересы. о мне, дата рождения.
                    </li>
                    <li><b>Приватность</b>. Настраивает доступ к тем или иным данным (функциям) других пользователей по
                        отношению
                        к вашей странице. Например "Кто может оставлять записи у меня на странице?" обозначает настройку
                        возможности
                        отправить запись на вашу страницу другим пользователям. Вы можете выбрать вариант "Никто" тогда
                        никто,
                        кроме вас не сможет оставить сообщение, либо "Все" тогда каждый пользователь имеет возможность
                        отправить
                        запись к вам на страницу.
                    </li>
                    <li><b>Безопасность</b>. Злесь можно поменять пароль, указав старый, и новый.</li>
                    <li><b>Чёрный список</b>. Это список пользователей которых вы добавили в чёрный список (ЧС).
                        Добавить
                        пользователя в ЧС можно через вкладку профиля нажав на кнопку "Добавить в ЧС" либо открыв диалог
                        с пользователем и нажать "Действия" -> "Добавить в чёрный список"
                    </li>
                </ul>
                Пометки полей с красной звездочкой (*) обозначает обязательное поле для заполнения.
            </div>
        </div>
    </div>
    <div id="search" class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseSearch"
                        aria-expanded="false">
                    Поиск
                </button>
            </h5>
        </div>

        <div id="collapseSearch" class="collapse">
            <div class="card-body text-left">
                Страница <b>Поиска</b> содержит текстовое поле для ввода имени, фамилии, отчества пользователя, которого
                нужно
                найти. Не обязательно указывать все данные, достаточно ввести одно имя. Поиск отобразит все совпадения
                в ФИО с этим запросом. <br/>
                Поиск находится в шапке сайта (в самом верху) расположен в правой части.
            </div>
        </div>
    </div>
    <div id="videoLink" class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseVideoLink"
                        aria-expanded="false">
                    Добавление видео из YouTube
                </button>
            </h5>
        </div>

        <div id="collapseVideoLink" class="collapse">
            <div class="card-body text-left">
                Cпособы: <br/>
                1) Скопируйте часть ссылки на сайте YouTube в адресной строке которая выделена красным на скриншоте.
                <img src="<?php echo Url::to('@web/resources/helpPage/video_link_help_1.png'); ?>" class="btn-rounded"
                     style="width: 50%;"/><br/>
                2) Нажмите кнопку Поделиться на сайте YouTube и скопируйте часть ссылки которая выделена красным на
                скриншоте.
                <img src="<?php echo Url::to('@web/resources/helpPage/video_link_help_2.png'); ?>" class="btn-rounded"
                     style="width: 50%;"/><br/>
                Скопированную часть ссылки вставьте в поле для внедрения видео.
            </div>
        </div>
    </div>
    <div id="adminConnect" class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseAdminConnect"
                        aria-expanded="false">
                    Связь с администрацией
                </button>
            </h5>
        </div>

        <div id="collapseAdminConnect" class="collapse">
            <div class="card-body text-left">
                Остались вопросы?<br/>
                Задайте их нашему <b>администратору</b> - <a href="<?php echo Url::to(['/42']); ?>">Алминистратор
                    CNN</a> в сообщении.
            </div>
        </div>
    </div>
</div>

<a href="#" title="Вернуться к началу" class="topbutton"><i class="fa fa-chevron-up"></i></a>