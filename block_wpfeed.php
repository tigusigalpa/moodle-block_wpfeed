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
    public $abspath;

    /**
     * HTTP URI to the plugin folder
     *
     * @access public
     * @var    string
     */
    public $httppath;

    /**
     * config.ini data array
     *
     * @access public
     * @var    array
     */
    public $staticconfig = array();

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
    public $externalskins = array();

    /**
     * Absolute path to external skins folder
     *
     * @access public
     * @var    string
     */
    public $externalskinsfolder;

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
    private static $plugininstance;

    public function __construct() {
        global $CFG, $USER, $SESSION;

        $this->_cfg           = $CFG;
        $this->_user          = $USER;
        $this->_session       = $SESSION;
        $this->location       = str_ireplace( $this->_cfg->dirroot , '', dirname( __FILE__ ) );
        $this->abspath        = $this->_cfg->dirroot . $this->location;
        $this->httppath       = $this->_cfg->wwwroot . $this->location;

        $this->staticconfig   = $this->block_wpfeed_get_static_config();
        $this->_config        = get_config( 'block_wpfeed' );
        if ( !empty( $this->_config->block_wpfeed_prefix ) ) {
            $this->_api_namespace = $this->_config->block_wpfeed_prefix;
        } else {
            $this->_api_namespace = $this->staticconfig['default_api_prefix'];
        }
        $this->_post_type     = $this->_block_wpfeed_get_post_type();

        $this->skin           = $this->_block_wpfeed_get_skin();
        $this->externalskinsfolder = $this->_cfg->dirroot . '/' . $this->staticconfig['external_skins_folder'];
        $this->externalskins = $this->block_wpfeed_external_skins();

        $this->_filter        = $this->_block_wpfeed_get_filter();
        $this->cache          = cache::make( 'block_wpfeed', 'cache' );
        $this->_posts         = $this->_block_wpfeed_get_posts();
        if ( isset( $this->_config->block_wpfeed_title ) && !empty( $this->_config->block_wpfeed_title ) ) {
            $this->title = $this->_config->block_wpfeed_title;
        } else {
            $this->staticconfig['default_block_title'];
        }
    }

    /**
     * Singleton
     * @return object
     */
    public static function get_instance() {
        if ( ! isset( self::$plugininstance ) && ! ( self::$plugininstance instanceof block_wpfeed ) ) {
            self::$plugininstance = new self();
        }
        return self::$plugininstance;
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
            if ( $this->_config->block_wpfeed_cache_interval >= $this->staticconfig['min_cache_time'] ) {
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
        if ( !empty( $this->_config->block_wpfeed_post_type ) ) {
            return $this->_config->block_wpfeed_post_type;
        }

        return $this->staticconfig['default_post_type'];
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
        return parse_ini_file( $this->abspath . '/config.ini' );
    }

    /**
     * Function prepare filter array for API request
     *
     * @since  1.0.0
     * @access private
     * @return array Filter array for API request
     */
    private function _block_wpfeed_get_filter() {
        if ( isset( $this->_config->block_wpfeed_posts_limit ) && $this->_config->block_wpfeed_posts_limit > 0 ) {
            $postslimitpre = intval( $this->_config->block_wpfeed_posts_limit );
        } else {
            $this->staticconfig['default_posts_limit'];
        }
        $postslimit = ( $postslimitpre > 0 ) ? $postslimitpre : 5;
        $retarray = array(
            'filter' => array(
                'posts_per_page' => $postslimit
            )
        );

        if ( isset( $this->_config->block_wpfeed_categories ) && !empty( $this->_config->block_wpfeed_categories ) ) {
            $categories = $this->_config->block_wpfeed_categories;
        } else {
            $categories = 0;
        }

        if ( !empty( $categories ) ) {
            $categoriesarray = explode( ',', $categories );
            if ( !empty( $categoriesarray ) && is_array( $categoriesarray ) ) {
                $cats = array();
                foreach($categoriesarray as $category):
                    $cat = intval( $category );
                    if ( !empty( $cat ) ) {
                        $cats[] = $cat;
                    }
                endforeach;
                $retarray['filter']['cat'] = join( ',', $cats );
            }
        }

        return $retarray;
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
        $skinpath = $this->_block_wpfeed_get_skin_filepath();
        if ( $skinpath ) {
            require_once( $skinpath );
            $skinclassname = $this->_block_wpfeed_get_skin_classname();
            if ( class_exists( $skinclassname ) ) {
                global $PAGE;

                $cssfiles = $this->_block_wpfeed_get_skin_frontend_files( 'css' );
                if ( !empty( $cssfiles ) && is_array( $cssfiles ) ) {
                    foreach($cssfiles as $cssfile):
                        $PAGE->requires->css( new moodle_url( $cssfile ) );
                    endforeach;
                }

                $jsfiles  = $this->_block_wpfeed_get_skin_frontend_files( 'js' );
                if ( !empty( $jsfiles ) && is_array( $jsfiles ) ) {
                    foreach($jsfiles as $jsfile):
                        $PAGE->requires->js( new moodle_url( $jsfile ) );
                    endforeach;
                }

                $skinobj = new $skinclassname;
                $output  = $skinobj->skin_output( $this->_posts );
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
        if ( isset( $this->_config->block_wpfeed_skin ) && !empty( $this->_config->block_wpfeed_skin ) ) {
            return $this->_config->block_wpfeed_skin;
        }

        return $this->staticconfig['default_skin_name'];
    }

    /**
     * Check for external skins folders
     *
     * @since  1.0.0
     * @access public
     * @return array|boolean
     */
    public function block_wpfeed_external_skins() {
        if ( file_exists( $this->externalskinsfolder ) && is_dir( $this->externalskinsfolder ) ) {
            $externalskinsfolders = glob( $this->externalskinsfolder . '/*', GLOB_ONLYDIR );
            if ( !empty( $externalskinsfolders ) && is_array( $externalskinsfolders ) ) {
                return $externalskinsfolders;
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
        $retarray = array();
        $namesarray = array();
        $skinsfolders = glob( $this->abspath . '/skins/*', GLOB_ONLYDIR );

        if ( $this->externalskins ) {
            $skinsfolders = array_merge( $skinsfolders, $this->externalskins );
        }

        if ( !empty( $skinsfolders ) && is_array( $skinsfolders ) ) {
            foreach($skinsfolders as $skinfolder):
                $skinname = str_ireplace( $this->abspath . '/skins/', '', $skinfolder );
                if ( $skinname == $skinfolder && $this->externalskins ) {
                    $skinname = str_ireplace( $this->externalskinsfolder . '/', '', $skinfolder );
                }
                $skinclassfile = $this->_block_wpfeed_get_skin_filepath( $skinname );
                if ( $skinclassfile ) {
                    $retarray[] = array(
                        'name'  => $skinname,
                        'file' => $skinclassfile
                    );
                    if ( $names ) {
                        $namesarray[$skinname] = $skinname;
                    }
                }
            endforeach;
            if ( $names ) {
                return $namesarray;
            }
        }

        return $retarray;
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
        $retarray = array();
        $file = strtolower( $file );
        if ( !in_array( $file , array( 'css', 'js' ) ) ) {
            return $retarray;
        }

        $skinname = $skin ? $skin : $this->skin;
        $dir = $this->location . '/skins/' . $skinname . '/' . $file;
        if ( !file_exists( $dir ) ) {
            $dir = $this->externalskinsfolder . '/' . $skinname . '/' . $file;
        }

        if ( file_exists( $dir ) && is_dir( $dir ) ) {
            $files = glob( $dir . '/*.' . $file );
            if ( !empty( $files ) && is_array( $files ) ) {
                foreach($files as $file):
                    $retarray[] = str_ireplace( $this->_cfg->dirroot, '', $file );
                endforeach;
            }
        }

        return $retarray;
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
        $skinname = $skin ? $skin : $this->skin;
        $file = $this->abspath . '/skins/' . $skinname . '/' . $skinname . '_skin.php';
        if ( !file_exists( $file ) && $this->externalskins ) {
            $file = $this->externalskinsfolder . '/' . $skinname . '/' . $skinname . '_skin.php';
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
        $skinname = $skin ? $skin : $this->skin;
        return 'block_wpfeed_skin_' . $skinname;
    }

    /**
     * This function generate URL for WordPress posts request
     *
     * @since  1.0.0
     * @param  int|boolean $id ID of needle WordPress post
     * @param  string $posttype Instance of request endpoint type
     * @access protected
     * @return string WordPress REST API request URL
     */
    protected function _block_wpfeed_get_wp_api_url( $id = false, $posttype = '' ) {
        $return = '';
        if ( !empty( $this->_config->block_wpfeed_wp_url ) ) {
            if ( empty( $posttype ) ) {
                $posttype = $this->_post_type;
            }
            $return = clean_param( trim( $this->_config->block_wpfeed_wp_url, '/' ), PARAM_URL ) . '/' . trim( $this->_api_namespace, '/' ) . '/' . $posttype;

            if ( !empty( $id ) ) {
                switch ( $posttype ) {
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
        // Important to make here global instead $this->_cfg.
        global $CFG;
        require_once( $CFG->libdir . '/filelib.php' );

        $curl = new curl();
        $curl->resetHeader();

        $retarray = array(
            'posts'  => '',
            'error' => ''
        );

        if ( $postsurl = $this->_block_wpfeed_get_wp_api_url( $id ) ) {

            $postsresponse = $curl->get( $postsurl, $this->_filter );

            if ( $postsresponse ) {
                $this->_response = json_decode( $postsresponse, true );
                $error = $this->_block_wpfeed_errors_handler();
                $retarray['error'] = $error;
                $retarray['posts'] = $error ? array() : $this->_response;

                if ( is_array( $retarray['posts'] ) && empty( $error ) ) {
                    foreach($retarray['posts'] as $k => $post):
                        if ( !empty( $this->_config->block_wpfeed_thumbnail_show ) ) {
                            $postmediaurl = $this->_block_wpfeed_get_wp_api_url( $post['id'], 'media' );
                            $postmediaresponse = $curl->get( $postmediaurl );
                            $postmediaarray = json_decode( $postmediaresponse, true );
                            if ( !empty( $postmediaarray ) && is_array( $postmediaarray ) ) {
                                $retarray['posts'][$k]['media'] = $postmediaarray[0];
                            }
                        }

                        $postcommentsurl = $this->_block_wpfeed_get_wp_api_url( $post['id'], 'comments' );
                        $postcommentsresponse = $curl->get( $postcommentsurl );
                        $postcommentsarray = json_decode( $postcommentsresponse, true );
                        if ( !empty( $postcommentsarray ) && is_array( $postcommentsarray ) ) {
                            foreach($postcommentsarray as $k2 => $postcomment):
                                $retarray['posts'][$k]['wpf_comments'][$k2]['id']   = $postcomment['id'];
                                $retarray['posts'][$k]['wpf_comments'][$k2]['text'] = $postcomment['content']['rendered'];
                            endforeach;
                        }

                        $postcategoryurl = $this->_block_wpfeed_get_wp_api_url( $post['id'], 'categories' );
                        $postcategoryresponse = $curl->get( $postcategoryurl );
                        $postcategoryarray = json_decode( $postcategoryresponse, true );
                        if ( !empty( $postcategoryarray ) && is_array( $postcategoryarray ) ) {
                            foreach($postcategoryarray as $k3 => $postcategory):
                                $retarray['posts'][$k]['wpf_cats'][$k3]['id']          = $postcategory['id'];
                                $retarray['posts'][$k]['wpf_cats'][$k3]['name']        = $postcategory['name'];
                                $retarray['posts'][$k]['wpf_cats'][$k3]['link']        = $postcategory['link'];
                                $retarray['posts'][$k]['wpf_cats'][$k3]['slug']        = $postcategory['slug'];
                                $retarray['posts'][$k]['wpf_cats'][$k3]['description'] = $postcategory['description'];
                            endforeach;
                        }
                    endforeach;
                }
            }
        }

        return $retarray;
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
        if ( $cacheinterval = $this->block_wpfeed_get_cache_interval() ) {
            $posts = json_decode( $this->cache->get( 'posts' ), true );
            $error = json_decode( $this->cache->get( 'error' ), true );
        }

        $sessionstore = 0;
        if ( isset( $this->_config->block_wpfeed_session_store ) ) {
            $sessionstore = intval( $this->_config->block_wpfeed_session_store );
        }
        if ( !empty( $sessionstore ) && !empty( $this->_session->wpfeed_response_posts ) ) {
            $posts = json_decode( $this->_session->wpfeed_response_posts );
        }

        if ( empty( $posts ) && empty( $error ) ) {
            $preposts = $this->block_wpfeed_posts_request();
            $posts     = !empty( $preposts['posts'] ) ? self::block_wpfeed_object_to_array( $preposts['posts'] ) : array();
            $error     = !empty( $preposts['error'] ) ? self::block_wpfeed_object_to_array( $preposts['error'] ) : array();
            if ( !empty( $cacheinterval ) && ( !empty( $posts ) || !empty( $error ) ) ) {
                $this->cache->set_many(
                    array(
                        'posts'  => json_encode( $posts ),
                        'error' => json_encode( $error )
                    )
                );
            }

            if ( !empty( $sessionstore ) && !empty( $posts ) ) {
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
        $retarray = array();
        if ( !empty( $this->_response ) && is_array( $this->_response ) && isset( $this->_response['code'], $this->_response['message'], $this->_response['data'], $this->_response['data']['status'] ) ) {
            $retarray[] = html_writer::tag( 'strong', get_string( 'block_wpfeed_error_string', 'block_wpfeed' ) ) . ':';
            $retarray[] = html_writer::tag( 'em', $this->_response['data']['status'] . ': ' . $this->_response['message'] . ' (' . $this->_response['code'] . ')' );
        }

        return join( '<br />', $retarray );
    }

    /**
     * Output debug data if error exists
     *
     * @since  1.0.0
     * @access public
     * @return string HTML-code of debug info
     */
    public function block_wpfeed_debug_info() {
        $retarray = array();

        $title = '<h5><u>' . get_string( 'block_wpfeed_debug_title', 'block_wpfeed' ) . ':</u></h5>';

        $apiurl     = $this->_block_wpfeed_get_wp_api_url();
        $retarray[] = html_writer::tag( 'strong',   get_string( 'block_wpfeed_api_url_title', 'block_wpfeed' ) ) . ':';
        $retarray[] = html_writer::tag( 'code',     html_writer::link( $apiurl, $apiurl, array( 'target' => '_blank' ) ) );
        $retarray[] = html_writer::tag( 'strong',   get_string( 'block_wpfeed_request_title', 'block_wpfeed' ) ) . ':';
        $retarray[] = html_writer::tag( 'code',     var_dump( $this->_filter ) );
        $retarray[] = html_writer::tag( 'strong',   get_string( 'block_wpfeed_response_title', 'block_wpfeed' ) ) . ':';
        if ( !empty( $this->_response ) && is_array( $this->_response ) ) {
            $retarray[] = html_writer::tag( 'code', var_dump( $this->_response ) );
        } else {
            $retarray[] = html_writer::tag( 'code', get_string( 'block_wpfeed_empty_response', 'block_wpfeed' ) );
        }

        return $title . join( '<br />', $retarray );
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
            foreach($data as $key => $value):
                $result[$key] = self::block_wpfeed_object_to_array( $value );
            endforeach;

            return $result;
        }

        return $data;
    }
}