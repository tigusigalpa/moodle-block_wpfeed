<?php
defined( 'MOODLE_INTERNAL' ) || die();

class block_wpfeed_skin_bootstrap extends block_wpfeed_skins{

    /**
     * Custom item wrapper start HTML code
     *
     * @since  1.0.0
     * @access protected
     * @return string
     */
    protected function _item_wrapper_start() {
        return html_writer::start_div( 'media' );
    }

    /**
     * Custom item wrapper end HTML code
     *
     * @since  1.0.0
     * @access protected
     * @return string
     */
    protected function _item_wrapper_end() {
        return html_writer::end_div();
    }

    /**
     * Get HTML-code of post thumbnail
     *
     * @since  1.0.0
     * @param  array $post Prepared post item array by @method _block_wpfeed_prepare_data
     * @param  boolean|int $link Make thumbnail as a link to the post or not
     * @param  array $linkattrs Link attributes, ie target=_blank
     * @param  array $imgattrs Image attributes, ie width=100
     * @access protected
     * @author Igor Sazonov <sovletig@yandex.ru> {@link http://lms-service.org}
     * @return string HTML-code of thumbnail image
     */
    protected function _thumbnail( array $post, $link = true, $linkattrs = array(), $imgattrs = array() ) {
        $return = '';
        if ( $this->_thumbnail_show() ) {
            if ( $post['thumbnail_url'] ) {
                $imgattrs['class'] = 'media-object';
                $return .= html_writer::start_div( 'media-left block_wpfeed_thumbnail block_wpfeed_thumbnail_' . $this->name );
                if ( $link ) {
                    $return .= html_writer::link( $post['link'], html_writer::img( $post['thumbnail_url'], $post['title'], $imgattrs ), $linkattrs );
                } else {
                    $return .= html_writer::img( $post['thumbnail_url'], $post['title'], $imgattrs );
                }
                $return .= html_writer::end_div();
            }
        }

        return $return;
    }

    /**
     * Skin post item output
     *
     * @since  1.0.0
     * @param  array $post Structured post data array
     * @param  array $linkattrs Link attributes, ie target=_blank
     * @access protected
     * @author Igor Sazonov <sovletig@yandex.ru> {@link http://lms-service.org}
     * @return string Post item output
     */
    protected function _output_item( array $post, $linkattrs = array() ) {
        $return = '';
        $return .= html_writer::start_div( 'media-body' );
        $return .= html_writer::tag( 'h4', html_writer::link( $post['link'], $post['title'], $linkattrs ), array( 'class' => 'media-heading block_wpfeed_title block_wpfeed_title_' . $this->name ) );
        $return .= html_writer::tag( 'p', $post['excerpt_trimmed'] );
        $return .= html_writer::end_div();

        return $return;
    }

}