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

require_once( 'skins.class.php' );

class block_wpfeed extends block_base{
    
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
    public $absPath;
    
    /**
     * HTTP URI to the plugin folder
     *
     * @access public
     * @var    string
     */
    public $httpPath;
    
    /**
     * config.ini data array
     *
     * @access public
     * @var    array
     */
    public $staticConfig = array();
    
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
    public $externalSkins = array();
    
    /**
     * Absolute path to external skins folder
     *
     * @access public
     * @var    string
     */
    public $externalSkinsFolder;
    
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
    private static $pluginInstance;
    
    public function __construct() {
        global $CFG, $USER, $SESSION;
        
        $this->_cfg           = $CFG;
        $this->_user          = $USER;
        $this->_session       = $SESSION;
        $this->location       = str_ireplace( $this->_cfg->dirroot , '', dirname( __FILE__ ) );
        $this->absPath        = $this->_cfg->dirroot . $this->location;
        $this->httpPath       = $this->_cfg->wwwroot . $this->location;
        
        $this->staticConfig   = $this->block_wpfeed_get_static_config();
        $this->_config        = get_config( 'block_wpfeed' );
        $this->_api_namespace = !empty( $this->_config->block_wpfeed_prefix ) ? 
                $this->_config->block_wpfeed_prefix : 
                $this->staticConfig['default_api_prefix'];
        $this->_post_type     = $this->_block_wpfeed_get_post_type();
        
        $this->skin           = $this->_block_wpfeed_get_skin();
        $this->externalSkinsFolder = $this->_cfg->dirroot . '/' . $this->staticConfig['external_skins_folder'];
        $this->externalSkins = $this->block_wpfeed_external_skins();
        
        $this->_filter        = $this->_block_wpfeed_get_filter();
        $this->cache          = cache::make( 'block_wpfeed', 'cache' );
        $this->_posts         = $this->_block_wpfeed_get_posts();
        $this->title          = ( isset( $this->_config->block_wpfeed_title ) && 
                !empty( $this->_config->block_wpfeed_title ) ) ? 
                $this->_config->block_wpfeed_title : 
                $this->staticConfig['default_block_title'];
    }
    
    /**
     * Singleton
     * @return object
     */
    public static function get_instance() {
        if ( ! isset( self::$pluginInstance ) && ! ( self::$pluginInstance instanceof block_wpfeed ) ) {
            self::$pluginInstance = new self();
        }
        return self::$pluginInstance;
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
            if ( $this->_config->block_wpfeed_cache_interval >= $this->staticConfig['min_cache_time'] ) {
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
        return !empty( $this->_config->block_wpfeed_post_type ) ? 
                $this->_config->block_wpfeed_post_type : 
                $this->staticConfig['default_post_type'];
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
        return parse_ini_file( $this->absPath . '/config.ini' );
    }
    
    /**
     * Function prepare filter array for API request
     *
     * @since  1.0.0
     * @access private
     * @return array Filter array for API request
     */
    private function _block_wpfeed_get_filter() {
        $postsLimitPre = ( isset( $this->_config->block_wpfeed_posts_limit ) && 
                $this->_config->block_wpfeed_posts_limit > 0 ) ? 
                intval( $this->_config->block_wpfeed_posts_limit ) : 
                $this->staticConfig['default_posts_limit'];
        $postsLimit = ( $postsLimitPre > 0 ) ? $postsLimitPre : 5;
        $retArray = array(
            'filter' => array(
                'posts_per_page' => $postsLimit
            )
        );
        
        $categories = ( isset( $this->_config->block_wpfeed_categories ) && 
                !empty( $this->_config->block_wpfeed_categories ) ) ? 
                $this->_config->block_wpfeed_categories : 0;
        if ( !empty( $categories ) ) {
            $categoriesArray = explode( ',', $categories );
            if ( !empty( $categoriesArray ) && is_array( $categoriesArray ) ) {
                $cats = array();
                foreach ( $categoriesArray as $category ) {
                    $cat = intval( $category );
                    if ( !empty( $cat ) ) {
                        $cats[] = $cat;
                    }
                }
                $retArray['filter']['cat'] = join( ',', $cats );
            }
        }
        
        return $retArray;
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
        $skinPath = $this->_block_wpfeed_get_skin_filepath();
        if ( $skinPath ) {
            require_once( $skinPath );
            $skinClassname = $this->_block_wpfeed_get_skin_classname();
            if ( class_exists( $skinClassname ) ) {
                global $PAGE;
                
                $cssFiles = $this->_block_wpfeed_get_skin_frontend_files( 'css' );
                if ( !empty( $cssFiles ) && is_array( $cssFiles ) ) {
                    foreach ( $cssFiles as $cssFile ) {
                        $PAGE->requires->css( new moodle_url( $cssFile ) );
                    }
                }
                
                $jsFiles  = $this->_block_wpfeed_get_skin_frontend_files( 'js' );
                if ( !empty( $jsFiles ) && is_array( $jsFiles ) ) {
                    foreach ( $jsFiles as $jsFile ) {
                        $PAGE->requires->js( new moodle_url( $jsFile ) );
                    }
                }
                
                $skinObj = new $skinClassname;
                $output  = $skinObj->skin_output( $this->_posts );
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
        return ( isset( $this->_config->block_wpfeed_skin ) && !empty( $this->_config->block_wpfeed_skin ) ) ? $this->_config->block_wpfeed_skin : $this->staticConfig['default_skin_name'];
    }
    
    /**
     * Check for external skins folders
     *
     * @since  1.0.0
     * @access public
     * @return array|boolean
     */
    public function block_wpfeed_external_skins() {
        if ( file_exists( $this->externalSkinsFolder ) && is_dir( $this->externalSkinsFolder ) ) {
            $externalSkinsFolders = glob( $this->externalSkinsFolder . '/*', GLOB_ONLYDIR );
            if ( !empty( $externalSkinsFolders ) && is_array( $externalSkinsFolders ) ) {
                return $externalSkinsFolders;
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
        $retArray = array();
        $namesArray = array();
        $skinsFolders = glob( $this->absPath . '/skins/*', GLOB_ONLYDIR );
        
        if ( $this->externalSkins ) {
            $skinsFolders = array_merge( $skinsFolders, $this->externalSkins );
        }
        
        if ( !empty( $skinsFolders ) && is_array( $skinsFolders ) ) {
            foreach ( $skinsFolders as $skinFolder ) {
                $skinName = str_ireplace( $this->absPath . '/skins/', '', $skinFolder );
                if ( $skinName == $skinFolder && $this->externalSkins ) {
                    $skinName = str_ireplace( $this->externalSkinsFolder . '/', '', $skinFolder );
                }
                $skinClassFile = $this->_block_wpfeed_get_skin_filepath( $skinName );
                if ( $skinClassFile ) {
                    $retArray[] = array(
                        'name'  => $skinName
                        ,'file' => $skinClassFile
                    );
                    if ( $names ) {
                        $namesArray[$skinName] = $skinName;
                    }
                }
            }
            if ( $names ) {
                return $namesArray;
            }
        }
        
        return $retArray;
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
        $retArray = array();
        $file = strtolower( $file );
        if ( !in_array( $file , array( 'css', 'js' ) ) ) {
            return $retArray;
        }
        
        $skinName = $skin ? $skin : $this->skin;
        $dir = $this->location . '/skins/' . $skinName . '/' . $file;
        if ( !file_exists( $dir ) ) {
            $dir = $this->externalSkinsFolder . '/' . $skinName . '/' . $file;
        }
        
        if ( file_exists( $dir ) && is_dir( $dir ) ) {
            $files = glob( $dir . '/*.' . $file );
            if ( !empty( $files ) && is_array( $files ) ) {
                foreach ( $files as $file ) {
                    $retArray[] = str_ireplace( $this->_cfg->dirroot, '', $file );
                }
            }
        }
        
        return $retArray;
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
        $skinName = $skin ? $skin : $this->skin;
        $file = $this->absPath . '/skins/' . $skinName . '/' . $skinName . '_skin.php';
        if ( !file_exists( $file ) && $this->externalSkins ) {
            $file = $this->externalSkinsFolder . '/' . $skinName . '/' . $skinName . '_skin.php';
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
        $skinName = $skin ? $skin : $this->skin;
        return 'block_wpfeed_skin_' . $skinName;
    }
    
    /**
     * This function generate URL for WordPress posts request
     *
     * @since  1.0.0
     * @param  int|boolean $id ID of needle WordPress post
     * @param  string $postType Instance of request endpoint type
     * @access protected
     * @return string WordPress REST API request URL
     */
    protected function _block_wpfeed_get_wp_api_url( $id = false, $postType = '' ) {
        $return = '';
        if ( isset( $this->_config->block_wpfeed_wp_url ) && !empty( $this->_config->block_wpfeed_wp_url ) ) {
            if ( empty( $postType ) ) {
                $postType = $this->_post_type;
            }
            $return = clean_param( trim( $this->_config->block_wpfeed_wp_url, '/' ), PARAM_URL ) . '/' . trim( $this->_api_namespace, '/' ) . '/' . $postType;

            if ( !empty( $id ) ) {
                switch ( $postType ) {
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
        
        $retArray = array(
            'posts'  => ''
            ,'error' => ''
        );
        
        if ( $postsUrl = $this->_block_wpfeed_get_wp_api_url( $id ) ) {
        
            $postsResponse = $curl->get( $postsUrl, $this->_filter );

            if ( $postsResponse ) {
                $this->_response = json_decode( $postsResponse, true );
                $error = $this->_block_wpfeed_errors_handler();
                $retArray['error'] = $error;
                $retArray['posts'] = $error ? array() : $this->_response;

                if ( is_array( $retArray['posts'] ) && empty( $error ) ) {
                    foreach ( $retArray['posts'] as $k => $post ) {
                        if ( !empty( $this->_config->block_wpfeed_thumbnail_show ) ) {
                            $postMediaUrl = $this->_block_wpfeed_get_wp_api_url( $post['id'], 'media' );
                            $postMediaResponse = $curl->get( $postMediaUrl );
                            $postMediaArray = json_decode( $postMediaResponse, true );
                            if ( !empty( $postMediaArray ) && is_array( $postMediaArray ) ) {
                                $retArray['posts'][$k]['media'] = $postMediaArray[0];
                            }
                        }

                        $postCommentsUrl = $this->_block_wpfeed_get_wp_api_url( $post['id'], 'comments' );
                        $postCommentsResponse = $curl->get( $postCommentsUrl );
                        $postCommentsArray = json_decode( $postCommentsResponse, true );
                        if ( !empty( $postCommentsArray ) && is_array( $postCommentsArray ) ) {
                            foreach ( $postCommentsArray as $k2 => $postComment ) {
                                $retArray['posts'][$k]['wpf_comments'][$k2]['id']   = $postComment['id'];
                                $retArray['posts'][$k]['wpf_comments'][$k2]['text'] = $postComment['content']['rendered'];
                            }
                        }

                        $postCategoryUrl = $this->_block_wpfeed_get_wp_api_url( $post['id'], 'categories' );
                        $postCategoryResponse = $curl->get( $postCategoryUrl );
                        $postCategoryArray = json_decode( $postCategoryResponse, true );
                        if ( !empty( $postCategoryArray ) && is_array( $postCategoryArray ) ) {
                            foreach ( $postCategoryArray as $k3 => $postCategory ) {
                                $retArray['posts'][$k]['wpf_cats'][$k3]['id']          = $postCategory['id'];
                                $retArray['posts'][$k]['wpf_cats'][$k3]['name']        = $postCategory['name'];
                                $retArray['posts'][$k]['wpf_cats'][$k3]['link']        = $postCategory['link'];
                                $retArray['posts'][$k]['wpf_cats'][$k3]['slug']        = $postCategory['slug'];
                                $retArray['posts'][$k]['wpf_cats'][$k3]['description'] = $postCategory['description'];
                            }
                        }
                    }
                }
            }
        }
        
        return $retArray;
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
        if ( $cacheInterval = $this->block_wpfeed_get_cache_interval() ) {
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
            if ( !empty( $cacheInterval ) && ( !empty( $posts ) || !empty( $error ) ) ) {
                $this->cache->set_many(
                    array(
                        'posts'  => json_encode( $posts )
                        ,'error' => json_encode( $error )
                    )
                );
            }
            
            if ( !empty( $session_store ) && !empty( $posts ) ) {
                $this->_session->wpfeed_response_posts = $this->_session->wpfeed_response_posts = json_encode( $posts );
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
        $retArray = array();
        if ( !empty( $this->_response ) && is_array( $this->_response ) && isset( $this->_response['code'], $this->_response['message'], $this->_response['data'], $this->_response['data']['status'] ) ) {
            $retArray[] = html_writer::tag( 'strong', get_string( 'block_wpfeed_error_string', 'block_wpfeed' ) ) . ':';
            $retArray[] = html_writer::tag( 'em', $this->_response['data']['status'] . ': ' . $this->_response['message'] . ' (' . $this->_response['code'] . ')' );
        }
        
        return join( '<br />', $retArray );
    }
    
    /**
     * Output debug data if error exists
     *
     * @since  1.0.0
     * @access public
     * @return string HTML-code of debug info
     */
    public function block_wpfeed_debug_info() {
        $retArray = array();
        
        $title = '<h5><u>' . get_string( 'block_wpfeed_debug_title', 'block_wpfeed' ) . ':</u></h5>';
        
        $apiUrl     = $this->_block_wpfeed_get_wp_api_url();
        $retArray[] = html_writer::tag( 'strong', get_string( 'block_wpfeed_api_url_title', 'block_wpfeed' ) ) . ':';
        $retArray[] = html_writer::tag( 'code',   html_writer::link( $apiUrl, $apiUrl, array( 'target' => '_blank' ) ) );
        $retArray[] = html_writer::tag( 'strong', get_string( 'block_wpfeed_request_title', 'block_wpfeed' ) ) . ':';
        $retArray[] = html_writer::tag( 'code',   print_r( $this->_filter, true ) );
        $retArray[] = html_writer::tag( 'strong', get_string( 'block_wpfeed_response_title', 'block_wpfeed' ) ) . ':';
        if ( !empty( $this->_response ) && is_array( $this->_response ) ) {
            $retArray[] = html_writer::tag( 'code', print_r( $this->_response, true ) );
        } else {
            $retArray[] = html_writer::tag( 'code', get_string( 'block_wpfeed_empty_response', 'block_wpfeed' ) );
        }
        
        return $title . join( '<br />', $retArray );
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