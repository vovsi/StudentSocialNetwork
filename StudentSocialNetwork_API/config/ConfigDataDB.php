<?php

namespace app\config;

class ConfigDataDB
{
    // ФАЙЛЫ
    const LIMIT_FILES = 15; // Лимит файлов для каждого пользователя
    const ALLOWS_FILE_EXTENSION = array(
        'pdf',
        'ppt',
        'pptx',
        'rar',
        'txt',
        'doc',
        'docx',
        'dot',
        'docm',
        'dotx',
        'dotm',
        'docb',
        'xls',
        'xlt',
        'xlm',
        'xlsx',
        'xlsm',
        'xltx',
        'xltm',
        'zip'
    ); // Доступные расширения файлов для загрузки в Мои файлы
    const ALLOWS_IMAGE_EXTENSION = array('gif', 'png', 'jpg', 'jpeg'); // Доступные расширения изображений

    // ЛИМИТЫ НА КОЛ-ВО СИМВОЛОВ
    const LIMIT_SYMBOLS_POST = 3000; // Ззапись
    const LIMIT_SYMBOLS_MESSAGE = 3000; // Сообщение
    const LIMIT_SYMBOLS_VIDEO_YOUTUBE = 100; // id YouTube видео (ПРОВЕРЯЕТСЯ ТОЛЬКО В НОВОСТЯХ)
    const LIMIT_SYMBOLS_RAW_DATE = 100; // Длина даты (сырой вид / строковый)
    const LIMIT_SYMBOLS_PHOTO_DESCRIPTION = 1000; // Описание к фотографии

    const LIMIT_SYMBOLS_NEWS_THEME = 1500; // Тема для новостей
    const LIMIT_SYMBOLS_NEWS_DESCRIPTION = 5000; // Описание для новостей
    const LIMIT_SYMBOLS_NEWS_EVENT_DESCRIPTION = 200; // Описание события для новостей

    const LIMIT_SELECTED_FILES = 10; // Кол-во выбранных файлов (к сообщению и т.п.)
    const LIMIT_SELECTED_CONVERSATION_MEMBERS = 10; // Кол-во участников беседы
    const LIMIT_SYMBOLS_POLL_THEME = 1500; // Тема опроса
    const LIMIT_SYMBOLS_POLL_ANSWER = 500; // Ответ опроса
    const LIMIT_COUNT_POLL_ANSWERS = 10; // Кол-во вариантов ответов в опросе
    const LIMIT_SYMBOLS_CONVERSATION_NAME = 30; // Название беседы

    const LIMIT_SYMBOLS_PROFILE_FIRST_NAME = 100; // Именя профиля
    const LIMIT_SYMBOLS_PROFILE_LAST_NAME = 100; // Фамилия профиля
    const LIMIT_SYMBOLS_PROFILE_PATRONYMIC = 100; // Отчество профиля
    const LIMIT_SYMBOLS_PROFILE_EMAIL = 200; // Email адрес
    const LIMIT_SYMBOLS_PROFILE_GENDER = 50; // Пол
    const LIMIT_SYMBOLS_PROFILE_PHONE_NUMBER = 50; // Номер телефона
    const LIMIT_SYMBOLS_PROFILE_INTERESTS = 1500; // Интересы
    const LIMIT_SYMBOLS_PROFILE_ACTIVITIES = 1500; // Деятельность
    const LIMIT_SYMBOLS_PROFILE_ABOUT_ME = 2000; // О мне (подробная информация о пользователе)

    // МИНИМАЛЬНОЕ КОЛ-ВО СИМВОЛОВ
    const MIN_SYMBOLS_CONVERSATION_NAME = 1;

    // РЕГУЛЯРНЫЕ ВЫРАЖЕНИЯ НА ВАЛИДНОСТЬ ДАННЫХ
    const REGEX_VALID_EMAIL = '/^((([0-9A-Za-z]{1}[-0-9A-z\.]{1,}[0-9A-Za-z]{1})|([0-9А-Яа-я]{1}[-0-9А-я\.]{1,}
        [0-9А-Яа-я]{1}))@([-0-9A-Za-z]{1,}\.){1,2}[-A-Za-z]{2,})$/u'; // Email

}