<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'block_comments', language 'ru'
 *
 * @package   block_wpfeed
 * @copyright 2016 Igor Sazonov <sovletig@yandex.ru> {@link http://lms-service.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname']                                 = 'WordPress Feed';
$string['wpfeed:addinstance']                         = 'Добавить новый блок WPFeed';
$string['wpfeed:myaddinstance']                       = 'Добавить новый HTML block to the My Moodle page';

$string['block_wpfeed_update_cache']                  = 'Обновить кеш WPFeed';

/*DEBUG*/
$string['block_wpfeed_debug_title']                   = 'Отладочная информация';
$string['block_wpfeed_request_title']                 = 'Данные запроса';
$string['block_wpfeed_response_title']                = 'Данные ответа';
$string['block_wpfeed_empty_response']                = 'Пустой запрос';
$string['block_wpfeed_api_url_title']                 = 'URL-адрес запроса WordPress REST API';
$string['block_wpfeed_settings_url_title']            = 'Перейти к настройкам блока WPFeed';
$string['block_wpfeed_clear_cache']                   = 'Если Вы обновили настройки блока, не забудьте';
$string['block_wpfeed_error_string']                  = 'Описание ошибки';

/*ERRORS DESCRIPTION*/
$string['block_wpfeed_no_posts']                      = 'В ответе API нет данных';

/*SETTINGS STRINGS*/
$string['block_wpfeed_settings_title']                = 'Заголовок блока';
$string['block_wpfeed_settings_wp_url']               = 'Адрес WordPress-сайта';
$string['block_wpfeed_settings_wp_url_desc']          = 'URL-адрес Вашего WordPress-сайта <strong>без слешей</strong>. Например: <em>http://mysite.ru</em>';
$string['block_wpfeed_settings_wp_api_prefix']        = 'Префикс WordPress REST API URI';
$string['block_wpfeed_settings_wp_api_prefix_desc']   = 'Префикс после URL-адреса сайта для запросов <strong>без слешей</strong>.<br />Префикс по-умолчанию для API v2: <strong>{$a->default_api_prefix}</strong><br />URL-адрес запроса к API в результате будет такого вида <strong>http://yoursite.ru/wp-json/wp/v2</strong>';
$string['block_wpfeed_settings_post_type']            = 'Нужный тип записи WordPress';
$string['block_wpfeed_settings_post_type_desc']       = 'Необходимый тип записи из Вашего WordPress-сайта. По умолчанию <strong>{$a->default_post_type}</strong>';
$string['block_wpfeed_settings_cache_interval']       = 'Интервал кеширования';
$string['block_wpfeed_settings_cache_interval_desc']  = 'Интревал кеширования ответа WP API в МИНУТАХ. Минимальное значение {$a->min_cache_time}, 0 = без кеширования';
$string['block_wpfeed_settings_session_store']        = 'Хранить результаты ответа API в сессии пользователя';
$string['block_wpfeed_settings_session_store_desc']   = 'Вы можете использовать этот вариант чтобы хранить ответ API для текущего пользователя. Каждый раз как пользователь посетит Ваш Moodle-сайт - ответ будет единожды сохраняться в объект $SESSION';
$string['block_wpfeed_settings_categories']           = 'ID категорий записей WordPress';
$string['block_wpfeed_settings_categories_desc']      = 'Несколько категорий должны быть разделены ",". 0 = все категории';
$string['block_wpfeed_settings_thumbnail_show']       = 'Показывать миниатюры записей';
$string['block_wpfeed_settings_thumbnail_size']       = 'Размер миниатюры';
$string['block_wpfeed_settings_thumbnail_size_desc']  = 'Встроенные в WordPress обработчики размеров миниатюр. Применяются если включена опция показывания миниатюр.<br />Встроенные обработчики размеров миниатюр WordPress:<br /><em>thumbnail</em><br /><em>medium</em><br /><em>large</em><br /><em>small</em>';
$string['block_wpfeed_settings_thumbnail_width']      = 'Ширина миниатюры';
$string['block_wpfeed_settings_thumbnail_width_desc'] = 'HTML аттрибут ширины миниатюры (px) указанный администратором. 0 = авто';
$string['block_wpfeed_settings_thumbnail_link']       = 'Миниатюра является ссылкой';
$string['block_wpfeed_settings_thumbnail_link_desc']  = 'Сделать изображение миниатюры ссылкой на запись';
$string['block_wpfeed_settings_posts_limit']          = 'Лимит записей';
$string['block_wpfeed_settings_posts_limit_desc']     = 'Количество последних записей, которые надо вывести';
$string['block_wpfeed_settings_post_date']            = 'Формат даты записей';
$string['block_wpfeed_settings_post_date_desc']       = 'Формат вывода даты записи, основанный на PHP-функции <strong>date</strong>';
$string['block_wpfeed_settings_excerpt_length']       = 'Длина текста-превью';
$string['block_wpfeed_settings_excerpt_length_desc']  = 'Размер (в символах) обрезания текста для показа превью';
$string['block_wpfeed_settings_skin']                 = 'Шаблон вывода';
$string['block_wpfeed_settings_new_window']           = 'Открывать ссылки/записи в новом окне/закладке браузера';
$string['block_wpfeed_settings_dev_mode']             = 'Режим разработки';
$string['block_wpfeed_settings_dev_mode_desc']        = 'Подробные данные для разработчика, работает только в нативном режиме отладки РАЗРАБОТЧИК';
$string['block_wpfeed_settings_noindex']              = 'Применить noindex';
$string['block_wpfeed_settings_noindex_desc']         = 'Добавить тег <strong>&lt;noindex&gt;</strong> чтобы сделать содержание блока неиндексируемым для поисковых систем таких как Яндекс или Google. Иногда полезно для SEO.';