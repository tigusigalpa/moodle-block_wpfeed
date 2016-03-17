<?php

defined( 'MOODLE_INTERNAL' ) || die;

if ( $ADMIN->fulltree ) {

    $block_wpfeed_instance = block_wpfeed::get_instance();
    $static_config_obj = (object) $block_wpfeed_instance->staticconfig;

    $settings->add( 
            new admin_setting_configtext( 'block_wpfeed/block_wpfeed_title', get_string( 'block_wpfeed_settings_title', 'block_wpfeed' )
            ,''
            ,$block_wpfeed_instance->staticconfig['default_block_title'] ) );


    $settings->add( 
            new admin_setting_configtext( 'block_wpfeed/block_wpfeed_wp_url', get_string( 'block_wpfeed_settings_wp_url', 'block_wpfeed' )
            ,get_string( 'block_wpfeed_settings_wp_url_desc', 'block_wpfeed' )
            ,''
            ,PARAM_URL ) );
    
    $settings->add( 
            new admin_setting_configtext( 'block_wpfeed/block_wpfeed_prefix', get_string( 'block_wpfeed_settings_wp_api_prefix', 'block_wpfeed' )
            ,get_string( 'block_wpfeed_settings_wp_api_prefix_desc', 'block_wpfeed', $static_config_obj )
            ,$block_wpfeed_instance->staticconfig['default_api_prefix'] ) );
    
    $settings->add( 
            new admin_setting_configtext( 'block_wpfeed/block_wpfeed_post_type', get_string( 'block_wpfeed_settings_post_type', 'block_wpfeed' )
            ,get_string( 'block_wpfeed_settings_post_type_desc', 'block_wpfeed', $static_config_obj )
            ,$block_wpfeed_instance->staticconfig['default_post_type'] ) );
    
    $settings->add( 
            new admin_setting_configtext( 'block_wpfeed/block_wpfeed_cache_interval', get_string( 'block_wpfeed_settings_cache_interval', 'block_wpfeed' )
            ,get_string( 'block_wpfeed_settings_cache_interval_desc', 'block_wpfeed', $static_config_obj )
            ,$block_wpfeed_instance->staticConfig['default_cache_interval']
            ,PARAM_INT ) );
    
    $settings->add( 
            new admin_setting_configcheckbox( 'block_wpfeed/block_wpfeed_session_store', get_string( 'block_wpfeed_settings_session_store', 'block_wpfeed' )
            ,get_string( 'block_wpfeed_settings_session_store_desc', 'block_wpfeed' )
            ,$block_wpfeed_instance->staticconfig['default_session_store'] ) );
    
    $settings->add( 
            new admin_setting_configtext( 'block_wpfeed/block_wpfeed_categories', get_string( 'block_wpfeed_settings_categories', 'block_wpfeed' )
            ,get_string( 'block_wpfeed_settings_categories_desc', 'block_wpfeed' )
            ,0 ) );
    
    $settings->add( 
            new admin_setting_configcheckbox( 'block_wpfeed/block_wpfeed_thumbnail_show', get_string( 'block_wpfeed_settings_thumbnail_show', 'block_wpfeed' )
            ,''
            ,$block_wpfeed_instance->staticconfig['default_thumbnail_show'] ) );
    
    $settings->add( 
            new admin_setting_configtext( 'block_wpfeed/block_wpfeed_thumbnail_size', get_string( 'block_wpfeed_settings_thumbnail_size', 'block_wpfeed' )
            ,get_string( 'block_wpfeed_settings_thumbnail_size_desc', 'block_wpfeed' )
            ,$block_wpfeed_instance->staticconfig['default_thumbnail_size'] ) );
    
    $settings->add( 
            new admin_setting_configtext( 'block_wpfeed/block_wpfeed_thumbnail_width', get_string( 'block_wpfeed_settings_thumbnail_width', 'block_wpfeed' )
            ,get_string( 'block_wpfeed_settings_thumbnail_width_desc', 'block_wpfeed' )
            ,$block_wpfeed_instance->staticconfig['default_thumbnail_width']
            ,PARAM_INT ) );
    
    $settings->add( 
            new admin_setting_configcheckbox( 'block_wpfeed/block_wpfeed_thumbnail_link', get_string( 'block_wpfeed_settings_thumbnail_link', 'block_wpfeed' )
            ,get_string( 'block_wpfeed_settings_thumbnail_link_desc', 'block_wpfeed' )
            ,$block_wpfeed_instance->staticconfig['default_thumbnail_link'] ) );
    
    $settings->add( 
            new admin_setting_configtext( 'block_wpfeed/block_wpfeed_posts_limit', get_string( 'block_wpfeed_settings_posts_limit', 'block_wpfeed' )
            ,get_string( 'block_wpfeed_settings_posts_limit_desc', 'block_wpfeed' )
            ,$block_wpfeed_instance->staticconfig['default_posts_limit']
            ,PARAM_INT ) );
    
    /*$settings->add( 
            new admin_setting_configtext( 'block_wpfeed/block_wpfeed_posts_offset', get_string( 'block_wpfeed_settings_posts_offset', 'block_wpfeed' )
            ,get_string( 'block_wpfeed_settings_posts_offset_desc', 'block_wpfeed' )
            ,$block_wpfeed_instance->staticconfig['default_posts_offset']
            ,PARAM_INT ) );*/
    
    $settings->add( 
            new admin_setting_configtext( 'block_wpfeed/block_wpfeed_post_date', get_string( 'block_wpfeed_settings_post_date', 'block_wpfeed' )
            ,get_string( 'block_wpfeed_settings_post_date_desc', 'block_wpfeed' )
            ,$block_wpfeed_instance->staticconfig['default_post_date'] ) );
    
    $settings->add( 
            new admin_setting_configtext( 'block_wpfeed/block_wpfeed_excerpt_length', get_string( 'block_wpfeed_settings_excerpt_length', 'block_wpfeed' )
            ,get_string( 'block_wpfeed_settings_excerpt_length_desc', 'block_wpfeed' )
            ,$block_wpfeed_instance->staticconfig['default_excerpt_length']
            ,PARAM_INT ) );
    
    $settings->add( 
            new admin_setting_configselect( 'block_wpfeed/block_wpfeed_skin', get_string( 'block_wpfeed_settings_skin', 'block_wpfeed' )
            ,null
            ,$block_wpfeed_instance->staticconfig['default_skin_name']
            ,$block_wpfeed_instance->block_wpfeed_get_skins( true ) ) );
    
    $settings->add( 
            new admin_setting_configcheckbox( 'block_wpfeed/block_wpfeed_new_window', get_string( 'block_wpfeed_settings_new_window', 'block_wpfeed' )
            ,''
            ,$block_wpfeed_instance->staticconfig['default_new_window'] ) );
    
    $settings->add( 
            new admin_setting_configcheckbox( 'block_wpfeed/block_wpfeed_noindex', get_string( 'block_wpfeed_settings_noindex', 'block_wpfeed' )
            ,get_string( 'block_wpfeed_settings_noindex_desc', 'block_wpfeed' )
            ,0 ) );
    
    $settings->add( 
            new admin_setting_configcheckbox( 'block_wpfeed/block_wpfeed_dev_mode', get_string( 'block_wpfeed_settings_dev_mode', 'block_wpfeed' )
            ,get_string( 'block_wpfeed_settings_dev_mode_desc', 'block_wpfeed' )
            ,0 ) );
    
}