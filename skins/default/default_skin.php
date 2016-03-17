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
        $return .= html_writer::div( html_writer::link( $post['link'],
                $post['title'], $linkattrs ),
                'block_wpfeed_title block_wpfeed_title_' . $this->name );
        $return .= html_writer::tag( 'small', $post['date_time'] );
        $return .= html_writer::tag( 'p', $post['excerpt_trimmed'] );

        return $return;
    }

}