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
 * WordPress feed block installation.
 *
 * @package    block_wpfeed
 * @copyright  2016 Igor Sazonov <sovletig@yandex.ru> {@link http://lms-service.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function xmldb_block_wpfeed_install() {
    set_config( 'block_wpfeed_title',           get_string( 'block_wpfeed_default_title', 'block_wpfeed' ), 'block_wpfeed' );
    set_config( 'block_wpfeed_wp_url',          '',                              'block_wpfeed' );
    set_config( 'block_wpfeed_api_version',     B_WPFEED_DEFAULT_API_VERSION,    'block_wpfeed' );
    set_config( 'block_wpfeed_prefix',          B_WPFEED_DEFAULT_API_PREFIX_V2,  'block_wpfeed' );
    set_config( 'block_wpfeed_post_type',       B_WPFEED_DEFAULT_POST_TYPE,      'block_wpfeed' );
    set_config( 'block_wpfeed_cache_interval',  B_WPFEED_DEFAULT_CACHE_INTERVAL, 'block_wpfeed' );
    set_config( 'block_wpfeed_session_store',   B_WPFEED_DEFAULT_SESSION_STORE,  'block_wpfeed' );

    set_config( 'block_wpfeed_categories',      0,                               'block_wpfeed' );

    set_config( 'block_wpfeed_thumbnail_show',  B_WPFEED_DEFAULT_THUMBNAIL_SHOW, 'block_wpfeed' );
    set_config( 'block_wpfeed_thumbnail_size',  B_WPFEED_DEFAULT_THUMBNAIL_SIZE, 'block_wpfeed' );
    set_config( 'block_wpfeed_thumbnail_width', 0,                               'block_wpfeed' );
    set_config( 'block_wpfeed_thumbnail_link',  B_WPFEED_DEFAULT_THUMBNAIL_LINK, 'block_wpfeed' );

    set_config( 'block_wpfeed_posts_limit',     B_WPFEED_DEFAULT_POSTS_LIMIT,    'block_wpfeed' );
    set_config( 'block_wpfeed_post_date',       B_WPFEED_DEFAULT_POST_DATE,      'block_wpfeed' );
    set_config( 'block_wpfeed_excerpt_length',  B_WPFEED_DEFAULT_EXCERPT_LENGTH, 'block_wpfeed' );

    set_config( 'block_wpfeed_skin',            B_WPFEED_DEFAULT_SKIN_NAME,      'block_wpfeed' );
    set_config( 'block_wpfeed_new_window',      B_WPFEED_DEFAULT_NEW_WINDOW,     'block_wpfeed' );

    set_config( 'block_wpfeed_dev_mode',        0,                               'block_wpfeed' );
    set_config( 'block_wpfeed_noindex',         0,                               'block_wpfeed' );
}