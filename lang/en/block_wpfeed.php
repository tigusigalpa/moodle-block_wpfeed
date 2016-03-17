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
 * Strings for component 'block_comments', language 'en'
 *
 * @package   block_wpfeed
 * @copyright 2016 Igor Sazonov <sovletig@yandex.ru> {@link http://lms-service.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname']                                 = 'WordPress Feed';
$string['wpfeed:addinstance']                         = 'Add a new WPFeed block';
$string['wpfeed:myaddinstance']                       = 'Add a new simple HTML block to the My Moodle page';

$string['block_wpfeed_default_title']                 = 'WordPress Feed';

$string['block_wpfeed_update_cache']                  = 'WPFeed cache update';

/*DEBUG*/
$string['block_wpfeed_debug_title']                   = 'Debug info';
$string['block_wpfeed_request_title']                 = 'Request data';
$string['block_wpfeed_response_title']                = 'Response data';
$string['block_wpfeed_empty_response']                = 'Empty response data';
$string['block_wpfeed_api_url_title']                 = 'Request WordPress REST API URL';
$string['block_wpfeed_settings_url_title']            = 'Go to the WPFeed block settings';
$string['block_wpfeed_clear_cache']                   = 'If you updated block settings, do not forget';
$string['block_wpfeed_error_string']                  = 'Error description';

/*ERRORS DESCRIPTION*/
$string['block_wpfeed_no_posts']                      = 'No posts in API response';

/*SETTINGS STRINGS*/
$string['block_wpfeed_settings_title']                = 'Block title';
$string['block_wpfeed_settings_wp_url']               = 'WordPress site URL';
$string['block_wpfeed_settings_wp_url_desc']          = 'Your WordPress site URL address <strong>without slashes</strong>. Example: <em>http://mysite.com</em>';
$string['block_wpfeed_settings_wp_api_prefix']        = 'WordPress REST API URI prefix';
$string['block_wpfeed_settings_wp_api_prefix_desc']   = 'Prefix after site URL for requests <strong>without slashes</strong>.<br />Default prefix for API v2: <strong>{$a->default_api_prefix}</strong><br />Request API URL at result will be like <strong>http://yoursite.com/wp-json/wp/v2</strong>';
$string['block_wpfeed_settings_post_type']            = 'WordPress post type';
$string['block_wpfeed_settings_post_type_desc']       = 'Needle Post type of your WordPress site. Default is <strong>{$a->default_post_type}</strong>';
$string['block_wpfeed_settings_cache_interval']       = 'Caching interval';
$string['block_wpfeed_settings_cache_interval_desc']  = 'WP API response caching in MINUTES. Min value is {$a->min_cache_time}, 0 = no caching';
$string['block_wpfeed_settings_session_store']        = 'Store response in user session';
$string['block_wpfeed_settings_session_store_desc']   = 'You can use this case to store saved API response for current user. Every time user visit your Moodle site - response is stored to the $SESSION object once';
$string['block_wpfeed_settings_categories']           = 'WordPress posts categories id-s';
$string['block_wpfeed_settings_categories_desc']      = 'Several categories must be separated by ",". 0 = all categories';
$string['block_wpfeed_settings_thumbnail_show']       = 'Show posts thumbnails';
$string['block_wpfeed_settings_thumbnail_size']       = 'Thumbnail size';
$string['block_wpfeed_settings_thumbnail_size_desc']  = 'WordPress-based thumbnail size handler. Works if show posts thumbnails enabled.<br />Default WordPress-based sizes:<br /><em>thumbnail</em><br /><em>medium</em><br /><em>large</em><br /><em>small</em>';
$string['block_wpfeed_settings_thumbnail_width']      = 'Thumbnail width';
$string['block_wpfeed_settings_thumbnail_width_desc'] = 'Thumbnail HTML width (px) attribute specified by admin. 0 = auto';
$string['block_wpfeed_settings_thumbnail_link']       = 'Thumbnail is a link';
$string['block_wpfeed_settings_thumbnail_link_desc']  = 'Make thumbnail image as a link to the post';
$string['block_wpfeed_settings_posts_limit']          = 'Posts limit';
$string['block_wpfeed_settings_posts_limit_desc']     = 'Posts number to output';
$string['block_wpfeed_settings_post_date']            = 'Post date format';
$string['block_wpfeed_settings_post_date_desc']       = 'Output post PHP function <strong>date</strong> format';
$string['block_wpfeed_settings_excerpt_length']       = 'Content excerpt length';
$string['block_wpfeed_settings_excerpt_length_desc']  = 'Length of content to trim';
$string['block_wpfeed_settings_skin']                 = 'Output skin';
$string['block_wpfeed_settings_new_window']           = 'Open links/posts in new window/tab';
$string['block_wpfeed_settings_dev_mode']             = 'Developer mode';
$string['block_wpfeed_settings_dev_mode_desc']        = 'Advanced data for developers, works only with development mode';
$string['block_wpfeed_settings_noindex']              = 'Enable noindex';
$string['block_wpfeed_settings_noindex_desc']         = 'Add <strong>&lt;noindex&gt;</strong> tag to make block content invisible for Search Engines like Google. Sometimes useful for SEO.';