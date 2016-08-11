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
 * Strings for component 'block_wpfeed', language 'kz'
 *
 * @package   block_wpfeed
 * @copyright 2016 Igor Sazonov <sovletig@yandex.ru> {@link http://lms-service.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname']                                 = 'WordPress Feed';
$string['wpfeed:addinstance']                         = 'WPFeed жаңа блогын қосу';
$string['wpfeed:myaddinstance']                       = 'Жаңа HTML block to the My Moodle page қосу';

$string['block_wpfeed_default_title']                 = 'WordPress жаңалықтары';

$string['block_wpfeed_update_cache']                  = 'WPFeed кешін жаңарту';

/*DEBUG*/
$string['block_wpfeed_debug_title']                   = 'Өңдегіш ақпарат';
$string['block_wpfeed_request_title']                 = 'Сұраныстың деректері';
$string['block_wpfeed_response_title']                = 'Жауаптың деректері';
$string['block_wpfeed_empty_response']                = 'Бос сұраныс';
$string['block_wpfeed_api_url_title']                 = 'WordPress REST API сұранысының URL-мекенжайы';
$string['block_wpfeed_settings_url_title']            = 'WPFeed блогының баптауларына көшу';
$string['block_wpfeed_clear_cache']                   = 'Егер Сіз блоктың баптауларын жаңартқан болсаңыз, ұмытпаңыз';
$string['block_wpfeed_error_string']                  = 'Қателіктің сипаты';

/*ERRORS DESCRIPTION*/
$string['block_wpfeed_no_posts']                      = 'API жауабында деректер жоқ';

/*SETTINGS STRINGS*/
$string['block_wpfeed_settings_title']                = 'Блоктың атауы';
$string['block_wpfeed_settings_hide_header']          = 'Блоктың атауын/шапкасын көрсетпеу';
$string['block_wpfeed_settings_hide_header_desc']     = 'Егер Сіз блоктың атауын жасырғыңыз келсе, осы опцияны таңдаңыз';
$string['block_wpfeed_settings_wp_url']               = 'WordPress-сайтының мекенжайы';
$string['block_wpfeed_settings_wp_url_desc']          = 'Сіздің WordPress-сайттың <strong>слештерсіз</strong> URL-мекенжайы. Мысалы: <em>http://mysite.kz</em>';
$string['block_wpfeed_api_version']                   = 'WordPress сайттың API нұсқасы';
$string['block_wpfeed_api_version_desc']              = 'WP API <strong>1 нұсқасы</strong> плагиннің URL репозиториясы: <a href="https://wordpress.org/plugins/json-rest-api/" target="_blank">https://wordpress.org/plugins/json-rest-api/</a> (<em>арналған нұсқалар WP 3.9&ndash;4.4.2</em>)<br />WP API <strong>2 нұсқа</strong> плагиннің URL репозиториясы: <a href="https://wordpress.org/plugins/rest-api/" target="_blank">https://wordpress.org/plugins/rest-api/</a> (<em>арналған нұсқалар WP 4.4+</em>)';
$string['block_wpfeed_settings_wp_api_prefix']        = 'WordPress REST API URI префиксі';
$string['block_wpfeed_settings_wp_api_prefix_desc']   = 'Сұраныстар үшін сайттың <strong>слештерсіз</strong> URL-мекенжайы.<br />API v1 арналған өздігінше префикс: <code>{$a->default_api_prefix_v1}</code><br />API v2 арналған өздігінше префикс: <code>{$a->default_api_prefix_v2}</code><br />API арналған сұраныстың URL-мекенжайы нәтижесінде мынадай түрде болады <code>http://yoursite.kz/{$a->default_api_prefix_v1}</code> немесе <code>http://yoursite.kz/{$a->default_api_prefix_v2}</code>';
$string['block_wpfeed_settings_post_type']            = 'WordPress жазбаларының керекті типі';
$string['block_wpfeed_settings_post_type_desc']       = 'Сіздің WordPress-сайттың жазбасының қажетті типі. Өздігінше <code>{$a->default_post_type}</code>';
$string['block_wpfeed_settings_cache_interval']       = 'Кештеу аралығы';
$string['block_wpfeed_settings_cache_interval_desc']  = 'WP API жауабының МИНУТТАРМЕН кештеу аралығы. Минималды мәні <code>{$a->min_cache_time}</code>, 0 = кештеусіз';
$string['block_wpfeed_settings_session_store']        = 'API жауаптарының нәтижелерін пайдаланушының сессиясында сақтау керек';
$string['block_wpfeed_settings_session_store_desc']   = 'Сіз осы амалды API жауабын сақтау үшін ағымдағы пайдаланушыны сақтау үшін қолдана аласыз. Сіздің Moodle-сайтқа пайдаланушы кірген сайын - жауабы бірретік мына объектіде сақталады <code>$SESSION</code>';
$string['block_wpfeed_settings_categories']           = 'WordPress жазбаларының ID санаттары';
$string['block_wpfeed_settings_categories_desc']      = 'Бірнеше санат үлестірілуге тиіс болады",". <code>0 = барлық санаттар</code>';
$string['block_wpfeed_settings_thumbnail_show']       = 'Жазбалардың миниатюраларын көрсету';
$string['block_wpfeed_settings_thumbnail_size']       = 'Миниатюраның мөлшері';
$string['block_wpfeed_settings_thumbnail_size_desc']  = 'WordPress кіріктірілген миниатюралар мөлшерлерін өңдегіштер. Егер де миниатюраларды көрсету опциясы қосулы болғанда көрсетіледі.<br />WordPress кіріктірілген миниатюралар мөлшерлерін өңдегіштер:<br /><code>thumbnail</code><br /><code>medium</code><br /><code>large</code><br /><code>small</code>';
$string['block_wpfeed_settings_thumbnail_width']      = 'Миниатюраның ені';
$string['block_wpfeed_settings_thumbnail_width_desc'] = 'Әкімгер көрсетіп жазған миниатюралардың (px) енінің HTML аттрибуты. <code>0 = авто</code>';
$string['block_wpfeed_settings_thumbnail_link']       = 'Миниатюра сілтеме болып табылады';
$string['block_wpfeed_settings_thumbnail_link_desc']  = 'Миниатюраның суреттемесін жазбаға сілтеме ретінде жасау';
$string['block_wpfeed_settings_posts_limit']          = 'Жазбалар лимиті';
$string['block_wpfeed_settings_posts_limit_desc']     = 'Шығаруға жатқызылатын соңғы жазбалардың саны';
$string['block_wpfeed_settings_post_date']            = 'Жазбалар күнінің форматы';
$string['block_wpfeed_settings_post_date_desc']       = 'PHP-функцияға негізделген жазба күнін шығару форматы <code>date</code>';
$string['block_wpfeed_settings_excerpt_length']       = 'Превью-мәтіннің ұзындығы';
$string['block_wpfeed_settings_excerpt_length_desc']  = 'Превью көрсетуге арналған кесілу мөлшері (таңбалармен)';
$string['block_wpfeed_settings_skin']                 = 'Шығару шаблоны';
$string['block_wpfeed_settings_new_window']           = 'Сілтемелерді/жазбаларды браузердің жаңа терезесінде/бетбелгісінде ашу';
$string['block_wpfeed_settings_dev_mode']             = 'Әзірлеу режимі';
$string['block_wpfeed_settings_dev_mode_desc']        = 'Әзірлеушіге арналған егжей-тегжейлі деректері, тек оңдаудың нативтік режимінде ғана жұмыс істейді <code>ӘЗІРЛЕУШІ</code>';
$string['block_wpfeed_settings_noindex']              = 'Noindex қолдану';
$string['block_wpfeed_settings_noindex_desc']         = 'Яндекс немесе Google сияқты іздестіру жүйелеріне арналған индекстелмейтін блок мазмұнын жасау үшін тег қосу <code>&lt;noindex&gt;</code>. Кейде SEO үшін пайдалы.';