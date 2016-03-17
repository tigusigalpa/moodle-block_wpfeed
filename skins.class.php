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
 * WPFeed skins output class
 *
 * @package    block_wpfeed
 * @copyright  2016 Igor Sazonov <sovletig@yandex.ru> {@link http://lms-service.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

abstract class block_wpfeed_skins{
    
    /**
     * Skin name
     *
     * @access public
     * @var    string
     */
    public $name;
    
    /**
     * WPFeed block class instance
     *
     * @access private
     * @var    object
     */
    private $_block_wpfeed;
    
    public function __construct(  ) {
        
        $this->name          = $this->_block_wpfeed_skin_get_name();
        $this->_block_wpfeed = block_wpfeed::get_instance();
        
    }
    
    /**
     * Check for settings about thumbnail show
     *
     * @since  1.0.0
     * @access private
     * @return int|boolean
     */
    private function _thumbnail_show() {
        return $this->_block_wpfeed->block_wpfeed_thumbnail_show();
    }
    
    /**
     * Check for settings about thumbnail link
     *
     * @since  1.0.0
     * @access private
     * @return int|boolean
     */
    private function _thumbnail_link() {
        return $this->_block_wpfeed->block_wpfeed_thumbnail_link();
    }
    
    /**
     * Thumbnail width if admin specified
     *
     * @since  1.0.0
     * @access private
     * @return int
     */
    private function _thumbnail_width() {
        return $this->_block_wpfeed->block_wpfeed_thumbnail_width();
    }
    
    /**
     * Check for settings about open links in a new window
     *
     * @since  1.0.0
     * @access private
     * @return int|boolean
     */
    private function _new_window() {
        return $this->_block_wpfeed->block_wpfeed_new_window();
    }
    
    /**
     * Check for settings about noindex tag
     *
     * @since  1.0.0
     * @access private
     * @return int|boolean
     */
    private function _noindex() {
        return $this->_block_wpfeed->block_wpfeed_noindex();
    }
    
    /**
     * Custom item wrapper start HTML code
     *
     * @since  1.0.0
     * @access protected
     * @return string
     */
    protected function _item_wrapper_start() {
        return '';
    }
    
    /**
     * Custom item wrapper end HTML code
     *
     * @since  1.0.0
     * @access protected
     * @return string
     */
    protected function _item_wrapper_end() {
        return '';
    }
    
    /**
     * Main HTML skin output for the WPFeed block
     *
     * @since  1.0.0
     * @param  array|boolean $posts Posts array from WordPress REST API
     * @access public
     * @return string Skin HTML code of WPFeed block
     */
    final public function skin_output( array $posts ) {
        $return = '';
        $errors = $this->_block_wpfeed->errors;
        
        if ( empty( $posts ) ) {
            $return .= html_writer::start_tag( 'noindex' );
            if ( empty( $errors ) ) {
                if ( $this->_block_wpfeed->block_wpfeed_is_dev_mode() ) {
                    if ( $this->_block_wpfeed->block_wpfeed_is_admin() ) {
                        $errors[] = html_writer::tag( 'em', get_string( 'block_wpfeed_no_posts', 'block_wpfeed' ) );
                        $errors[] = $this->_block_wpfeed->block_wpfeed_debug_info();
                        $return = join( '<br />', $errors );
                    } else {
                        $return = '';
                    }
                }
            } else {
                if ( $this->_block_wpfeed->block_wpfeed_is_admin() ) {
                    $errors[] = $this->_block_wpfeed->block_wpfeed_debug_info();
                    $return = join( '<br />', $errors );
                } else {
                    $return = '';
                }
            }
            $return .= html_writer::end_tag( 'noindex' );
        } else {
            if ( empty( $errors ) ) {
                $return .= $this->_noindex() ? html_writer::start_tag( 'noindex' ) : '';
                $skin    = $this->_block_wpfeed->skin;
                $return .= html_writer::start_div( 'block_wpfeed_wrapper block_wpfeed_wrapper_' . $skin );
                $return .= html_writer::start_tag( 'ul', array( 'class' => 'block_wpfeed_list' ) );
                $link_attrs = $this->_new_window() ? array( 'target' => '_blank' ) : array();
                $_thumbnail_width = $this->_thumbnail_width();
                $img_attrs  = ( $_thumbnail_width > 0 ) ? array( 'width' => $_thumbnail_width ) : array();
                foreach ( $posts as $pre_post ) :
                    $post    = $this->_block_wpfeed_prepare_data( $pre_post );
                    $return .= html_writer::start_tag( 'li', array( 'id' => 'block_wpfeed_list_item_' . $post['id'], 'class' => 'block_wpfeed_list_item block_wpfeed_list_item_' . $skin ) );
                    $return .= html_writer::start_div( 'block_wpfeed_list_item_wrapper block_wpfeed_list_item_wrapper_' . $skin );
                    $return .= $this->_item_wrapper_start();
                    $return .= $this->_thumbnail( $post, $this->_thumbnail_link(), $link_attrs, $img_attrs );
                    $return .= $this->_output_item( $post, $link_attrs );
                    $return .= $this->_item_wrapper_end();
                    $return .= html_writer::end_div();
                    $return .= html_writer::div( '', 'block_wpfeed_clear' );
                    $return .= html_writer::end_tag( 'li' );
                endforeach;
                $return .= html_writer::end_tag( 'ul' );
                $return .= html_writer::end_div();
            }
            $return .= $this->_noindex() ? html_writer::end_tag( 'noindex' ) : '';
        }
        
        if ( $this->_block_wpfeed->block_wpfeed_is_admin() ) {
            $return .= html_writer::start_tag( 'noindex' );
            $return .= html_writer::tag( 'strong', html_writer::link( new moodle_url( '/admin/settings.php', array( 'section' => 'blocksettingwpfeed' ) ), get_string( 'block_wpfeed_settings_url_title', 'block_wpfeed' ) . ' >>>' ) );
            $return .= html_writer::empty_tag( 'br' );
            $return .= html_writer::tag( 'em', get_string( 'block_wpfeed_clear_cache', 'block_wpfeed' ) . ' ' . html_writer::link( new moodle_url( '/admin/purgecaches.php' ), get_string( 'purgecaches', 'admin' ), array( 'style' => 'color:red' ) ) );
            $return .= html_writer::end_tag( 'noindex' );
        }
        
        return $return;
    }
    
    /**
     * Get HTML-code of post thumbnail
     *
     * @since  1.0.0
     * @param  array $post Prepared post item array by @method _block_wpfeed_prepare_data
     * @param  boolean|int $link Make thumbnail as a link to the post or not
     * @param  array $linkAttrs Link attributes, ie target=_blank
     * @param  array $img_attrs Image attributes, ie width=100
     * @access private
     * @return string HTML-code of thumbnail image
     */
    private function _thumbnail( array $post, $link = true, $linkAttrs = array(), $imgAttrs = array() ) {
        $return = '';
        if ( $this->_thumbnail_show() ) {
            if ( $post['thumbnail_url'] ) {
                $return .= html_writer::start_div( 'block_wpfeed_thumbnail block_wpfeed_thumbnail_' . $this->name );
                if ( $link ) {
                    $return .= html_writer::link( $post['link'], html_writer::img( $post['thumbnail_url'], $post['title'], $imgAttrs ), $linkAttrs );
                } else {
                    $return .= html_writer::img( $post['thumbnail_url'], $post['title'], $imgAttrs );
                }
                $return .= html_writer::end_div();
            }
        }
        
        return $return;
    }
    
    /**
     * Skin post item output
     *
     * @abstract
     * @since  1.0.0
     * @param  array $post Structured post data array
     * @access protected
     * @return string Post item output
     */
    abstract protected function _output_item( array $post );
    
    /**
     * Get skin name by the class name
     *
     * @since  1.0.0
     * @access private
     * @return string
     */
    private function _block_wpfeed_skin_get_name() {
        $classNameArray = explode( '_' , get_called_class() );
        return $classNameArray[ count( $classNameArray ) - 1 ];
    }
    
    /**
     * Handle input post data array from WordPress REST API to structured array
     *
     * @since  1.0.0
     * @param  array $post Input post item data from WordPress REST API
     * @access protected
     * @return array Structured array with needle post data
     */
    protected function _block_wpfeed_prepare_data( array $post ) {
        $id              = intval( $post['id'] );
        $type            = $post['type'];
        $link            = $post['link'];
        $title           = $post['title']['rendered'];
        $sticky          = $post['sticky'];
        $content         = $post['content']['rendered'];
        $excerpt         = $post['excerpt']['rendered'];
        $excerptTrimmed  = self::trim_text( $content, $this->_block_wpfeed->block_wpfeed_get_excerpt_length() );
        $date            = $post['date'];
        $dateTimeStr     = strtotime( $date );
        $dateTime        = date( $this->_block_wpfeed->block_wpfeed_get_date_format(), $dateTimeStr );
        $dateGmt         = $post['date_gmt'];
        $dateGmtTime     = strtotime( $dateGmt );
        $thumbnailSize   = $this->_block_wpfeed->block_wpfeed_get_thumbnail_size();
        $thumbnailUrl    = ( !empty( $thumbnailSize ) && 
                isset( $post['media'], $post['media']['media_details']['sizes'][$thumbnailSize] ) ) ? 
                $post['media']['media_details']['sizes'][$thumbnailSize]['source_url'] : '';
        $categories      = ( !empty( $post['wpf_cats'] ) && isset( $post['wpf_cats'] ) ) ? $post['wpf_cats'] : array();
        $comments        = ( !empty( $post['wpf_comments'] ) && isset( $post['wpf_comments'] ) ) ? $post['wpf_comments'] : array();
        
        return array(
            'id'               => $id
            ,'type'            => $type
            ,'link'            => $link
            ,'title'           => $title
            ,'sticky'          => $sticky
            ,'content'         => $content
            ,'excerpt'         => $excerpt
            ,'excerpt_trimmed' => $excerptTrimmed
            ,'date'            => $date
            ,'date_time'       => $dateTime
            ,'date_time_str'   => $dateTimeStr
            ,'date_gmt'        => $dateGmt
            ,'date_gmt_time'   => $dateGmtTime
            ,'thumbnail_url'   => $thumbnailUrl
            ,'categories'      => $categories
            ,'comments'        => $comments
            ,'comments_count'  => count( $comments )
        );
    }
    
    /**
     * Helper function to trim text to excerpt
     *
     * @since  1.0.0
     * @param  string $text Input text data
     * @param  int $length Trim text length
     * @access public
     * @return string Trimmed text by params
     */
    public static function trim_text( $text, $length = 80 ) {
        $text = strip_tags( $text );
        if ( mb_strlen( $text, 'UTF-8' ) > $length ) {
            $offset = ( $length - 3 ) - strlen( $text );
            return substr( $text, 0, strrpos( $text, ' ', $offset ) ) . '...';
        }
        
        return $text;
    }
    
}