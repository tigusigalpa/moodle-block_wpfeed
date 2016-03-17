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
    $config = parse_ini_file( '../config.ini' );

    set_config( 'block_wpfeed_title',           $config['default_block_title'],    'block_wpfeed' );
    set_config( 'block_wpfeed_wp_url',          '',                                'block_wpfeed' );
    set_config( 'block_wpfeed_prefix',          $config['default_api_prefix'],     'block_wpfeed' );
    set_config( 'block_wpfeed_post_type',       $config['default_api_prefix'],     'block_wpfeed' );
    set_config( 'block_wpfeed_cache_interval',  $config['default_post_type'],      'block_wpfeed' );
    set_config( 'block_wpfeed_session_store',   $config['default_session_store'],  'block_wpfeed' );

    set_config( 'block_wpfeed_categories',      0,                                 'block_wpfeed' );

    set_config( 'block_wpfeed_thumbnail_show',  $config['default_thumbnail_show'], 'block_wpfeed' );
    set_config( 'block_wpfeed_thumbnail_size',  $config['default_thumbnail_size'], 'block_wpfeed' );
    set_config( 'block_wpfeed_thumbnail_width', 0,                                 'block_wpfeed' );
    set_config( 'block_wpfeed_thumbnail_link',  $config['default_thumbnail_link'], 'block_wpfeed' );

    set_config( 'block_wpfeed_posts_limit',     $config['default_posts_limit'],    'block_wpfeed' );
    set_config( 'block_wpfeed_post_date',       $config['default_post_date'],      'block_wpfeed' );
    set_config( 'block_wpfeed_excerpt_length',  $config['default_excerpt_length'], 'block_wpfeed' );

    set_config( 'block_wpfeed_skin',            $config['default_skin_name'],      'block_wpfeed' );
    set_config( 'block_wpfeed_new_window',      $config['default_new_window'],     'block_wpfeed' );

    set_config( 'block_wpfeed_dev_mode',        0,                                 'block_wpfeed' );
    set_config( 'block_wpfeed_noindex',         0,                                 'block_wpfeed' );
}