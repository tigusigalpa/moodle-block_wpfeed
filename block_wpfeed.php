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
 * The WordPress Feed block
 *
 * @package   block_wpfeed
 * @copyright 2016 Igor Sazonov <sovletig@yandex.ru> {@link http://lms-service.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once 'skins.class.php';

class block_wpfeed extends block_base {
    
    /**
     * Moodle based $CFG object
     * 
     * @var    object
     * @access private
     */
    private $_cfg;
    
    /**
     * Internal current user object $USER
     * 
     * @var    object
     * @access private
     */
    private $_user;
    
    /**
     * User session Moodle-based object
     * 
     * @var    object
     * @access private
     */
    private $_session;
    
    /**
     * Relative plugin path
     * 
     * @access public
     * @var    string
     */
    public $location;
    
    /**
     * Absolute server path to the plugin folder
     * 
     * @access public
     * @var    string
     */
    public $abs_path;
    
    /**
     * HTTP URI to the plugin folder
     * 
     * @access public
     * @var    string
     */
    public $http_path;
    
    /**
     * config.ini data array
     * 
     * @access public
     * @var    array
     */
    public $static_config = array();
    
    /**
     * The object with plufin configs
     * 
     * @var    object
     * @access private
     */
    private $_config;
    
    /**
     * WordPress API prefix for request URL
     * 
     * @var    string
     * @access private
     */
    private $_api_namespace;
    
    /**
     * WordPress request post type
     * 
     * @var    string
     * @access private
     */
    private $_post_type;
    
    /**
     * Plugin cache object
     * 
     * @var    object
     * @access public
     */
    public $cache;
    
    /**
     * Response posts direct from API or from cache
     * 
     * @var    array
     * @access private
     */
    private $_posts = array();
    
    /**
     * WordPress REST API response error
     * 
     * @var    array
     * @access public
     */
    public $errors = array();
    
    /**
     * Current skin name
     * 
     * @access public
     * @var    string
     */
    public $skin;
    
    /**
     * Get folders in external skins folder
     * 
     * @access protected
     * @var    array|boolean
     */
    public $external_skins = array();
    
    /**
     * Absolute path to external skins folder
     * 
     * @access public
     * @var    string
     */
    public $external_skins_folder;
    
    /**
     * WP_Query request filter posts array
     * 
     * @var    array
     * @access protected
     */
    protected $_filter = array();
    
    /**
     * WordPress REST API response
     * 
     * @var    string
     * @access private
     */
    private $_response;
    
    /**
     * Singleton object
     * 
     * @var    object
     * @access private
     */
    private static $_instance;
    
    public function __construct() {
        global $CFG, $USER, $SESSION;
        
        $this->_cfg           = $CFG;
        $this->_user          = $USER;
        $this->_session       = $SESSION;
        $this->location       = str_ireplace( $this->_cfg->dirroot , '', dirname( __FILE__ ) );
        $this->abs_path       = $this->_cfg->dirroot . $this->location;
        $this->http_path      = $this->_cfg->wwwroot . $this->location;
        
        $this->static_config  = $this->block_wpfeed_get_static_config();
        $this->_config        = get_config( 'block_wpfeed' );
        $this->_api_namespace = !empty( $this->_config->block_wpfeed_prefix ) ? $this->_config->block_wpfeed_prefix : $this->static_config['default_api_prefix'];
        $this->_post_type     = $this->_block_wpfeed_get_post_type();
        
        $this->skin           = $this->_block_wpfeed_get_skin();
        $this->external_skins_folder = $this->_cfg->dirroot . '/' . $this->static_config['external_skins_folder'];
        $this->external_skins = $this->block_wpfeed_external_skins();
        
        $this->_filter        = $this->_block_wpfeed_get_filter();
        $this->cache          = cache::make( 'block_wpfeed', 'cache' );
        $this->_posts         = $this->_block_wpfeed_get_posts();
        $this->title          = ( isset( $this->_config->block_wpfeed_title ) && !empty( $this->_config->block_wpfeed_title ) ) ? $this->_config->block_wpfeed_title : $this->static_config['default_block_title'];
    }
    
    /**
     * Singleton
     * @return object
     */
    public static function getInstance() {
        if ( ! isset( self::$_instance ) && ! ( self::$_instance instanceof block_wpfeed ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * Check for plugin in development mode from settings
     * 
     * @since  1.0.0
     * @access public
     * @return boolean
     */
    public function block_wpfeed_is_dev_mode() {
        if ( $this->_cfg->debugdeveloper == 1 && $this->block_wpfeed_is_admin() ) {
            return $this->_config->block_wpfeed_dev_mode;
        }
        
        return false;
    }
    
    /**
     * Plugin cache interval
     * 
     * @since  1.0.0
     * @access public
     * @return int
     */
    public function block_wpfeed_get_cache_interval() {
        if ( !empty( $this->_config->block_wpfeed_cache_interval ) ) {
            if ( $this->_config->block_wpfeed_cache_interval >= $this->static_config['min_cache_time'] ) {
                return $this->_config->block_wpfeed_cache_interval;
            }
        }
        
        return 0;
    }
    
    /**
     * WordPress Post Type to get for
     * 
     * @since  1.0.0
     * @access private
     * @return string
     */
    private function _block_wpfeed_get_post_type() {
        return !empty( $this->_config->block_wpfeed_post_type ) ? $this->_config->block_wpfeed_post_type : $this->static_config['default_post_type'];
    }
    
    /**
     * Check plugin settings about show thumbnail
     * 
     * @since  1.0.0
     * @access public
     * @return boolean|int Has thumbnail from the plugin settings
     */
    public function block_wpfeed_thumbnail_show() {
        return $this->_config->block_wpfeed_thumbnail_show;
    }
    
    /**
     * Check plugin settings about thumbnail size handler
     * 
     * @since  1.0.0
     * @access public
     * @return string Thumbnail WordPress-based size handler from the plugin settings
     */
    public function block_wpfeed_get_thumbnail_size() {
        return $this->_config->block_wpfeed_thumbnail_size;
    }
    
    /**
     * Check plugin settings about thumbnail link
     * 
     * @since  1.0.0
     * @access public
     * @return int|boolean Has thumbnail link or not
     */
    public function block_wpfeed_thumbnail_link() {
        return $this->_config->block_wpfeed_thumbnail_link;
    }
    
    /**
     * Thumbnail HTML width if specified and more than 20px
     * 
     * @since  1.0.0
     * @access public
     * @return int Thumbnail width HTML attribute
     */
    public function block_wpfeed_thumbnail_width() {
        if ( $this->_config->block_wpfeed_thumbnail_width >= 20 ) {
            return $this->_config->block_wpfeed_thumbnail_width;
        }
        return 0;
    }
    
    /**
     * Get settings about date format
     * 
     * @since  1.0.0
     * @access public
     * @return string Post date format
     */
    public function block_wpfeed_get_date_format() {
        return $this->_config->block_wpfeed_post_date;
    }
    
    /**
     * Get settings about excerpt length
     * 
     * @since  1.0.0
     * @access public
     * @return int
     */
    public function block_wpfeed_get_excerpt_length() {
        return $this->_config->block_wpfeed_excerpt_length;
    }
    
    /**
     * Check plugin settings about link new window open
     * 
     * @since  1.0.0
     * @access protected
     * @return boolean|int Has link new window open from the plugin settings
     */
    public function block_wpfeed_new_window() {
        return $this->_config->block_wpfeed_new_window;
    }
    
    /**
     * Check plugin settings about noindex tag enabled
     * 
     * @since  1.0.0
     * @access public
     * @return int|boolean
     */
    public function block_wpfeed_noindex() {
        return $this->_config->block_wpfeed_noindex;
    }
    
    /**
     * Get static plugin config (defaults) from config.ini file
     * 
     * @since  1.0.0
     * @access public
     * @return array Parsed config.ini array with static config data
     */
    public function block_wpfeed_get_static_config() {
        return parse_ini_file( $this->abs_path . '/config.ini' );
    }
    
    /**
     * Function prepare filter array for API request
     * 
     * @since  1.0.0
     * @access private
     * @return array Filter array for API request
     */
    private function _block_wpfeed_get_filter() {
        $posts_limit_pre = ( isset( $this->_config->block_wpfeed_posts_limit ) && $this->_config->block_wpfeed_posts_limit > 0 ) ? intval( $this->_config->block_wpfeed_posts_limit ) : $this->static_config['default_posts_limit'];
        $posts_limit = ( $posts_limit_pre > 0 ) ? $posts_limit_pre : 5;
        $ret_array = array(
            'filter' => array(
                'posts_per_page' => $posts_limit
            )
        );
        
        $categories = ( isset( $this->_config->block_wpfeed_categories ) && !empty( $this->_config->block_wpfeed_categories ) ) ? $this->_config->block_wpfeed_categories : 0;
        if ( !empty( $categories ) ) {
            $categories_array = explode( ',', $categories );
            if ( !empty( $categories_array ) && is_array( $categories_array ) ) {
                $cats = array();
                foreach ( $categories_array as $category ) {
                    $cat = intval( $category );
                    if ( !empty( $cat ) ) {
                        $cats[] = $cat;
                    }
                }
                $ret_array['filter']['cat'] = join( ',', $cats );
            }
        }
        
        return $ret_array;
    }
    
    public function has_config() {
        return true;
    }
    
    public function get_content() {
        
        require_once( $this->_cfg->libdir . '/filelib.php' );
        
        if ( $this->content !== null ) {
            return $this->content;
        }
        
        $output = '';
        $skin_path = $this->_block_wpfeed_get_skin_filepath();
        if ( $skin_path ) {
            require_once $skin_path;
            $skin_classname = $this->_block_wpfeed_get_skin_classname();
            if ( class_exists( $skin_classname ) ) {
                global $PAGE;
                
                $css_files = $this->_block_wpfeed_get_skin_frontend_files( 'css' );
                if ( !empty( $css_files ) && is_array( $css_files ) ) {
                    foreach ( $css_files as $css_file ) {
                        $PAGE->requires->css( new moodle_url( $css_file ) );
                    }
                }
                
                $js_files  = $this->_block_wpfeed_get_skin_frontend_files( 'js' );
                if ( !empty( $js_files ) && is_array( $js_files ) ) {
                    foreach ( $js_files as $js_file ) {
                        $PAGE->requires->js( new moodle_url( $js_file ) );
                    }
                }
                
                $skin_obj   = new $skin_classname;
                $output     = $skin_obj->skin_output( $this->_posts );
            }
        }
        
        $this->content       = new stdClass;
        $this->content->text = $output;
        
        return $this->content;
    }
    
    /**
     * Check for current user is admin
     * 
     * @since  1.0.0
     * @access public
     * @return boolean
     */
    public function block_wpfeed_is_admin() {
        return array_key_exists( $this->_user->id, get_admins() );
    }
    
    /**
     * Get current skin name from settings. Default skin is default (from static config)
     * 
     * @since  1.0.0
     * @access protected
     * @return string Current skin name
     */
    protected function _block_wpfeed_get_skin() {
        return ( isset( $this->_config->block_wpfeed_skin ) && !empty( $this->_config->block_wpfeed_skin ) ) ? $this->_config->block_wpfeed_skin : $this->static_config['default_skin_name'];
    }
    
    /**
     * Check for external skins folders
     * 
     * @since  1.0.0
     * @access public
     * @return array|boolean
     */
    public function block_wpfeed_external_skins() {
        if ( file_exists( $this->external_skins_folder ) && is_dir( $this->external_skins_folder ) ) {
            $external_skins_folders = glob( $this->external_skins_folder . '/*', GLOB_ONLYDIR );
            if ( !empty( $external_skins_folders ) && is_array( $external_skins_folders ) ) {
                return $external_skins_folders;
            }
        }
        
        return false;
    }
    
    /**
     * Get all available skins from skins folder
     * 
     * @since  1.0.0
     * @param  boolean $names true is return only names array as key and value of the array
     * @access public
     * @return array Skins array, each item is an associative array with keys name (skin name) and file (class file)
     */
    public function block_wpfeed_get_skins( $names = false ) {
        $ret_array = array();
        $names_array = array();
        $skins_folders = glob( $this->abs_path . '/skins/*', GLOB_ONLYDIR );
        
        if ( $this->external_skins ) {
            $skins_folders = array_merge( $skins_folders, $this->external_skins );
        }
        
        if ( !empty( $skins_folders ) && is_array( $skins_folders ) ) {
            foreach ( $skins_folders as $skin_folder ) {
                $skin_name = str_ireplace( $this->abs_path . '/skins/', '', $skin_folder );
                if ( $skin_name == $skin_folder && $this->external_skins ) {
                    $skin_name = str_ireplace( $this->external_skins_folder . '/', '', $skin_folder );
                }
                $skin_class_file = $this->_block_wpfeed_get_skin_filepath( $skin_name );
                if ( $skin_class_file ) {
                    $ret_array[] = array(
                        'name'  => $skin_name
                        ,'file' => $skin_class_file
                    );
                    if ( $names ) {
                        $names_array[$skin_name] = $skin_name;
                    }
                }
            }
            if ( $names ) {
                return $names_array;
            }
        }
        
        return $ret_array;
    }
    
    /**
     * Get all skin CSS/JS files
     * 
     * @since  1.0.0
     * @param  string $file Files types. css or js
     * @param  string|boolean $skin Given skin name / false if need to get from the current skin
     * @access protected
     * @return array Array of HTTP paths of skin CSS or JS files
     */
    protected function _block_wpfeed_get_skin_frontend_files( $file = 'css', $skin = false ) {
        $ret_array = array();
        $file = strtolower( $file );
        if ( !in_array( $file , array( 'css', 'js' ) ) ) {
            return $ret_array;
        }
        
        $skin_name = $skin ? $skin : $this->skin;
        $dir = $this->location . '/skins/' . $skin_name . '/' . $file;
        if ( !file_exists( $dir ) ) {
            $dir = $this->external_skins_folder . '/' . $skin_name . '/' . $file;
        }
        
        if ( file_exists( $dir ) && is_dir( $dir ) ) {
            $files = glob( $dir . '/*.' . $file );
            if ( !empty( $files ) && is_array( $files ) ) {
                foreach ( $files as $file ) {
                    $ret_array[] = str_ireplace( $this->_cfg->dirroot, '', $file );
                }
            }
        }
        
        return $ret_array;
    }
    
    /**
     * Get main class file of the given skin, if the one is not exists - false
     * 
     * @since  1.0.0
     * @param  string|boolean $skin Given skin name / false if need to get from the current skin
     * @access protected
     * @return string|boolean Main skin class file. If file not exists - false
     */
    protected function _block_wpfeed_get_skin_filepath( $skin = false ) {
        $skin_name = $skin ? $skin : $this->skin;
        $file = $this->abs_path . '/skins/' . $skin_name . '/' . $skin_name . '_skin.php';
        if ( !file_exists( $file ) && $this->external_skins ) {
            $file = $this->external_skins_folder . '/' . $skin_name . '/' . $skin_name . '_skin.php';
        }
        return file_exists( $file ) ? $file : false;
    }
    
    /**
     * Get skin main class name by skin name
     * 
     * @since  1.0.0
     * @param  string|boolean $skin Given skin name / false if need to get from the current skin
     * @access protected
     * @return string Skin main class name
     */
    protected function _block_wpfeed_get_skin_classname( $skin = false ) {
        $skin_name = $skin ? $skin : $this->skin;
        return 'block_wpfeed_skin_' . $skin_name;
    }
    
    /**
     * This function generate URL for WordPress posts request
     * 
     * @since  1.0.0
     * @param  int|boolean $id ID of needle WordPress post
     * @param  string $post_type Instance of request endpoint type
     * @access protected
     * @return string WordPress REST API request URL
     */
    protected function _block_wpfeed_get_wp_api_url( $id = false, $post_type = '' ) {
        $return = '';
        if ( isset( $this->_config->block_wpfeed_wp_url ) && !empty( $this->_config->block_wpfeed_wp_url ) ) {
            if ( empty( $post_type ) ) {
                $post_type = $this->_post_type;
            }
            $return = clean_param( trim( $this->_config->block_wpfeed_wp_url, '/' ), PARAM_URL ) . '/' . trim( $this->_api_namespace, '/' ) . '/' . $post_type;

            if ( !empty( $id ) ) {
                switch ( $post_type ) {
                    case 'media':
                        $return .= '?parent=' . $id;
                        break;
                    case 'comments':
                    case 'categories':
                        $return .= '?post=' . $id;
                        break;
                    case 'posts':
                    default:
                        $return .= '/' . $id;
                        break;
                }
            }
        }
        
        return $return;
    }
    
    /**
     * This function makes WP API request
     * 
     * @param  string $instance Instance of request endpoint type
     * @param  string $method Request method GET/POST
     * @param  int|boolean $id Needle WordPress post ID
     * @since  1.0.0
     * @access public
     * @return boolean|array WP API response array or false if error issets
     */
    public function block_wpfeed_posts_request( $id = false ) {
        //!!!important to make here global instead $this->_cfg
        global $CFG;
        require_once( $CFG->libdir . '/filelib.php' );
        
        $curl = new curl();
        $curl->resetHeader();
        
        $ret_array = array(
            'posts'  => ''
            ,'error' => ''
        );
        
        if ( $posts_url = $this->_block_wpfeed_get_wp_api_url( $id ) ) {
        
            $posts_response = $curl->get( $posts_url, $this->_filter );

            if ( $posts_response ) {
                $this->_response = json_decode( $posts_response, true );
                $error = $this->_block_wpfeed_errors_handler();
                $ret_array['error'] = $error;
                $ret_array['posts'] = $error ? array() : $this->_response;

                if ( is_array( $ret_array['posts'] ) && empty( $error ) ) {
                    foreach ( $ret_array['posts'] as $k => $post ) {
                        if ( !empty( $this->_config->block_wpfeed_thumbnail_show ) ) {
                            $post_media_url = $this->_block_wpfeed_get_wp_api_url( $post['id'], 'media' );
                            $post_media_response = $curl->get( $post_media_url );
                            $post_media_array = json_decode( $post_media_response, true );
                            if ( !empty( $post_media_array ) && is_array( $post_media_array ) ) {
                                $ret_array['posts'][$k]['media'] = $post_media_array[0];
                            }
                        }

                        $post_comments_url = $this->_block_wpfeed_get_wp_api_url( $post['id'], 'comments' );
                        $post_comments_response = $curl->get( $post_comments_url );
                        $post_comments_array = json_decode( $post_comments_response, true );
                        if ( !empty( $post_comments_array ) && is_array( $post_comments_array ) ) {
                            foreach ( $post_comments_array as $k2 => $post_comment ) {
                                $ret_array['posts'][$k]['wpf_comments'][$k2]['id']   = $post_comment['id'];
                                $ret_array['posts'][$k]['wpf_comments'][$k2]['text'] = $post_comment['content']['rendered'];
                            }
                        }

                        $post_category_url = $this->_block_wpfeed_get_wp_api_url( $post['id'], 'categories' );
                        $post_category_response = $curl->get( $post_category_url );
                        $post_category_array = json_decode( $post_category_response, true );
                        if ( !empty( $post_category_array ) && is_array( $post_category_array ) ) {
                            foreach ( $post_category_array as $k3 => $post_category ) {
                                $ret_array['posts'][$k]['wpf_cats'][$k3]['id']          = $post_category['id'];
                                $ret_array['posts'][$k]['wpf_cats'][$k3]['name']        = $post_category['name'];
                                $ret_array['posts'][$k]['wpf_cats'][$k3]['link']        = $post_category['link'];
                                $ret_array['posts'][$k]['wpf_cats'][$k3]['slug']        = $post_category['slug'];
                                $ret_array['posts'][$k]['wpf_cats'][$k3]['description'] = $post_category['description'];
                            }
                        }
                    }
                }
            }
        }
        
        return $ret_array;
    }
    
    /**
     * Get posts array from WordPress REST API or false if empty posts / API errors
     * If API errors - handles errors to a string and returns false
     * 
     * @since  1.0.0
     * @access protected
     * @return boolean|array Array of posts from WordPress REST API or false if empty posts / API errors
     */
    protected function _block_wpfeed_get_posts() {
        $posts = array();
        $error = array();
        if ( $cache_interval = $this->block_wpfeed_get_cache_interval() ) {
            $posts = json_decode( $this->cache->get( 'posts' ), true );
            $error = json_decode( $this->cache->get( 'error' ), true );
        }
        
        $session_store = isset( $this->_config->block_wpfeed_session_store ) ? intval( $this->_config->block_wpfeed_session_store ) : 0;
        if ( !empty( $session_store ) && !empty( $this->_session->wpfeed_response_posts ) ) {
            $posts = json_decode( $this->_session->wpfeed_response_posts );
        }
        
        if ( empty( $posts ) && empty( $error ) ) {
            $pre_posts = $this->block_wpfeed_posts_request();
            $posts     = !empty( $pre_posts['posts'] ) ? self::block_wpfeed_object_to_array( $pre_posts['posts'] ) : array();
            $error     = !empty( $pre_posts['error'] ) ? self::block_wpfeed_object_to_array( $pre_posts['error'] ) : array();
            if ( !empty( $cache_interval ) && ( !empty( $posts ) || !empty( $error ) ) ) {
                $this->cache->set_many(
                    array(
                        'posts'  => json_encode( $posts )
                        ,'error' => json_encode( $error )
                    )
                );
            }
            
            if ( !empty( $session_store ) && !empty( $posts ) ) {
                global $SESSION;
                $SESSION->wpfeed_response_posts = $this->_session->wpfeed_response_posts = json_encode( $posts );
            }
        }
        
        if ( !empty( $error ) ) {
            $this->errors[] = $error;
        }
        
        if ( !empty( $posts ) ) {
            return self::block_wpfeed_object_to_array( $posts );
        }
        
        return array();
    }
    
    /**
     * The handler of native WordPress REST API error array.
     * If the known error code, plugin will help user to find his mistake.
     * 
     * @since  1.0.0
     * @param  array $error_data Error data array from WordPress REST API
     * @access protected
     * @return boolean|string String with error description or false if no errors
     */
    protected function _block_wpfeed_errors_handler() {
        $ret_array = array();
        if ( !empty( $this->_response ) && is_array( $this->_response ) && isset( $this->_response['code'], $this->_response['message'], $this->_response['data'], $this->_response['data']['status'] ) ) {
            $ret_array[] = html_writer::tag( 'strong', get_string( 'block_wpfeed_error_string', 'block_wpfeed' ) ) . ':';
            $ret_array[] = html_writer::tag( 'em', $this->_response['data']['status'] . ': ' . $this->_response['message'] . ' (' . $this->_response['code'] . ')' );
        }
        
        return join( '<br />', $ret_array );
    }
    
    /**
     * Output debug data if error exists
     * 
     * @since  1.0.0
     * @access public
     * @return string HTML-code of debug info
     */
    public function block_wpfeed_debug_info() {
        $ret_array = array();
        
        $title = '<h5><u>' . get_string( 'block_wpfeed_debug_title', 'block_wpfeed' ) . ':</u></h5>';
        
        $api_url     = $this->_block_wpfeed_get_wp_api_url();
        $ret_array[] = html_writer::tag( 'strong', get_string( 'block_wpfeed_api_url_title', 'block_wpfeed' ) ) . ':';
        $ret_array[] = html_writer::tag( 'code',   html_writer::link( $api_url, $api_url, array( 'target' => '_blank' ) ) );
        $ret_array[] = html_writer::tag( 'strong', get_string( 'block_wpfeed_request_title', 'block_wpfeed' ) ) . ':';
        $ret_array[] = html_writer::tag( 'code',   print_r( $this->_filter, true ) );
        $ret_array[] = html_writer::tag( 'strong', get_string( 'block_wpfeed_response_title', 'block_wpfeed' ) ) . ':';
        if ( !empty( $this->_response ) && is_array( $this->_response ) ) {
            $ret_array[] = html_writer::tag( 'code', print_r( $this->_response, true ) );
        } else {
            $ret_array[] = html_writer::tag( 'code', get_string( 'block_wpfeed_empty_response', 'block_wpfeed' ) );
        }
        
        return $title . join( '<br />', $ret_array );
    }
    
    /**
     * Hekper @static function to deep convert maybe object to associative array
     * 
     * @since  1.0.0
     * @param  array|object $data Input data
     * @access public
     * @return array
     */
    public static function block_wpfeed_object_to_array( $data ) {
        if ( is_array( $data ) || is_object( $data ) ) {
            $result = array();
            foreach ( $data as $key => $value ) {
                $result[$key] = self::block_wpfeed_object_to_array( $value );
            }
            
            return $result;
        }
        
        return $data;
    }
}