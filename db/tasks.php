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
 * Definition of WPFeed tasks
 *
 * @package   block_wpfeed
 * @category  task
 * @copyright 2016 Igor Sazonov <sovletig@yandex.ru> {@link http://lms-service.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined( 'MOODLE_INTERNAL' ) || die();

$instance       = block_wpfeed::get_instance();
$cache_interval = $instance->block_wpfeed_get_cache_interval();

if ( $cache_interval > 0 ) {

    $tasks = array(
        array(
            'classname' => '\block_wpfeed\task\update_cache_task',
            'blocking'  => 0,
            'minute'    => "*/{$cache_interval}",
            'hour'      => '*',
            'day'       => '*',
            'month'     => '*',
            'dayofweek' => '*'
        )
    );

}