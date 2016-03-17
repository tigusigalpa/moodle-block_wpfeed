<?php
defined( 'MOODLE_INTERNAL' ) || die();

class block_wpfeed_skin_default extends block_wpfeed_skins{

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
        $return .= html_writer::div( html_writer::link( $post['link'], $post['title'], $linkattrs ), 'block_wpfeed_title block_wpfeed_title_' . $this->name );
        $return .= html_writer::tag( 'small', $post['date_time'] );
        $return .= html_writer::tag( 'p', $post['excerpt_trimmed'] );

        return $return;
    }

}