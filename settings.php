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

defined( 'MOODLE_INTERNAL' ) || die;

if ( $ADMIN->fulltree ) {

    $blockwpfeedinstance = block_wpfeed::get_instance();

    $settings->add(
            new admin_setting_configtext( 'block_wpfeed/block_wpfeed_title',
                    get_string( 'block_wpfeed_settings_title', 'block_wpfeed' ),
            '',
            get_string( 'block_wpfeed_default_title', 'block_wpfeed' ) ) );

    $settings->add(
            new admin_setting_configcheckbox( 'block_wpfeed/block_wpfeed_hide_header',
                    get_string( 'block_wpfeed_settings_hide_header', 'block_wpfeed' ),
            get_string( 'block_wpfeed_settings_hide_header_desc', 'block_wpfeed' ),
            B_WPFEED_DEFAULT_HIDE_HEADER ) );

    $settings->add(
            new admin_setting_configtext( 'block_wpfeed/block_wpfeed_wp_url',
                    get_string( 'block_wpfeed_settings_wp_url', 'block_wpfeed' ),
            get_string( 'block_wpfeed_settings_wp_url_desc', 'block_wpfeed' ),
            '',
            PARAM_URL ) );

    $settings->add(
            new admin_setting_configselect( 'block_wpfeed/block_wpfeed_api_version',
                    get_string( 'block_wpfeed_api_version', 'block_wpfeed' ),
            get_string( 'block_wpfeed_api_version_desc', 'block_wpfeed' ),
            B_WPFEED_DEFAULT_API_VERSION,
            $blockwpfeedinstance::$blockwpfeedapiversions ) );

    $a1 = new stdClass();
    $a1->default_api_prefix_v1 = B_WPFEED_DEFAULT_API_PREFIX_V1;
    $a1->default_api_prefix_v2 = B_WPFEED_DEFAULT_API_PREFIX_V2;
    $settings->add(
            new admin_setting_configtext( 'block_wpfeed/block_wpfeed_prefix',
                    get_string( 'block_wpfeed_settings_wp_api_prefix', 'block_wpfeed' ),
            get_string( 'block_wpfeed_settings_wp_api_prefix_desc', 'block_wpfeed', $a1 ),
            B_WPFEED_DEFAULT_API_PREFIX_V2 ) );

    $a2 = new stdClass();
    $a2->default_post_type = B_WPFEED_DEFAULT_POST_TYPE;
    $settings->add(
            new admin_setting_configtext( 'block_wpfeed/block_wpfeed_post_type',
                    get_string( 'block_wpfeed_settings_post_type', 'block_wpfeed' ),
            get_string( 'block_wpfeed_settings_post_type_desc', 'block_wpfeed', $a2 ),
            B_WPFEED_DEFAULT_POST_TYPE ) );

    $a3 = new stdClass();
    $a3->min_cache_time = B_WPFEED_MIN_CACHE_TIME;
    $settings->add(
            new admin_setting_configtext( 'block_wpfeed/block_wpfeed_cache_interval',
                    get_string( 'block_wpfeed_settings_cache_interval', 'block_wpfeed' ),
            get_string( 'block_wpfeed_settings_cache_interval_desc', 'block_wpfeed', $a3 ),
            B_WPFEED_DEFAULT_CACHE_INTERVAL,
            PARAM_INT ) );

    $settings->add(
            new admin_setting_configcheckbox( 'block_wpfeed/block_wpfeed_session_store',
                    get_string( 'block_wpfeed_settings_session_store', 'block_wpfeed' ),
            get_string( 'block_wpfeed_settings_session_store_desc', 'block_wpfeed' ),
            B_WPFEED_DEFAULT_SESSION_STORE ) );

    $settings->add(
            new admin_setting_configtext( 'block_wpfeed/block_wpfeed_categories',
                    get_string( 'block_wpfeed_settings_categories', 'block_wpfeed' ),
            get_string( 'block_wpfeed_settings_categories_desc', 'block_wpfeed' ),
            0 ) );

    $settings->add(
            new admin_setting_configcheckbox( 'block_wpfeed/block_wpfeed_thumbnail_show',
                    get_string( 'block_wpfeed_settings_thumbnail_show', 'block_wpfeed' ),
            '',
            B_WPFEED_DEFAULT_THUMBNAIL_SHOW ) );

    $settings->add(
            new admin_setting_configtext( 'block_wpfeed/block_wpfeed_thumbnail_size',
                    get_string( 'block_wpfeed_settings_thumbnail_size', 'block_wpfeed' ),
            get_string( 'block_wpfeed_settings_thumbnail_size_desc', 'block_wpfeed' ),
            B_WPFEED_DEFAULT_THUMBNAIL_SIZE ) );

    $settings->add(
            new admin_setting_configtext( 'block_wpfeed/block_wpfeed_thumbnail_width',
                    get_string( 'block_wpfeed_settings_thumbnail_width', 'block_wpfeed' ),
            get_string( 'block_wpfeed_settings_thumbnail_width_desc', 'block_wpfeed' ),
            B_WPFEED_DEFAULT_THUMBNAIL_WIDTH,
            PARAM_INT ) );

    $settings->add(
            new admin_setting_configcheckbox( 'block_wpfeed/block_wpfeed_thumbnail_link',
                    get_string( 'block_wpfeed_settings_thumbnail_link', 'block_wpfeed' ),
            get_string( 'block_wpfeed_settings_thumbnail_link_desc', 'block_wpfeed' ),
            B_WPFEED_DEFAULT_THUMBNAIL_LINK ) );

    $settings->add(
            new admin_setting_configtext( 'block_wpfeed/block_wpfeed_posts_limit',
                    get_string( 'block_wpfeed_settings_posts_limit', 'block_wpfeed' ),
            get_string( 'block_wpfeed_settings_posts_limit_desc', 'block_wpfeed' ),
            B_WPFEED_DEFAULT_POSTS_LIMIT,
            PARAM_INT ) );

    $settings->add(
            new admin_setting_configtext( 'block_wpfeed/block_wpfeed_post_date',
                    get_string( 'block_wpfeed_settings_post_date', 'block_wpfeed' ),
            get_string( 'block_wpfeed_settings_post_date_desc', 'block_wpfeed' ),
            B_WPFEED_DEFAULT_POST_DATE ) );

    $settings->add(
            new admin_setting_configtext( 'block_wpfeed/block_wpfeed_excerpt_length',
                    get_string( 'block_wpfeed_settings_excerpt_length', 'block_wpfeed' ),
            get_string( 'block_wpfeed_settings_excerpt_length_desc', 'block_wpfeed' ),
            B_WPFEED_DEFAULT_EXCERPT_LENGTH,
            PARAM_INT ) );

    $settings->add(
            new admin_setting_configselect( 'block_wpfeed/block_wpfeed_skin',
                    get_string( 'block_wpfeed_settings_skin', 'block_wpfeed' ),
            null,
            B_WPFEED_DEFAULT_SKIN_NAME,
            $blockwpfeedinstance->block_wpfeed_get_skins( true ) ) );

    $settings->add(
            new admin_setting_configcheckbox( 'block_wpfeed/block_wpfeed_new_window',
                    get_string( 'block_wpfeed_settings_new_window', 'block_wpfeed' ),
            '',
            B_WPFEED_DEFAULT_NEW_WINDOW ) );

    $settings->add(
            new admin_setting_configcheckbox( 'block_wpfeed/block_wpfeed_noindex',
                    get_string( 'block_wpfeed_settings_noindex', 'block_wpfeed' ),
            get_string( 'block_wpfeed_settings_noindex_desc', 'block_wpfeed' ),
            0 ) );

    $settings->add(
            new admin_setting_configcheckbox( 'block_wpfeed/block_wpfeed_dev_mode',
                    get_string( 'block_wpfeed_settings_dev_mode', 'block_wpfeed' ),
            get_string( 'block_wpfeed_settings_dev_mode_desc', 'block_wpfeed' ),
            0 ) );

}