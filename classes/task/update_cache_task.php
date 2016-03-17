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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * A scheduled task for WPFeed cache update.
 *
 * @package    block_wpfeed
 * @copyright  2016 Igor Sazonov <sovletig@yandex.ru> {@link http://lms-service.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_wpfeed\task;

defined( 'MOODLE_INTERNAL' ) || die();

/**
 * A scheduled task class for WPFeed block.
 *
 * @copyright  2016 Igor Sazonov <sovletig@yandex.ru> {@link http://lms-service.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class update_cache_task extends \core\task\scheduled_task{

    public function get_name() {
        return get_string( 'block_wpfeed_update_cache', 'block_wpfeed' );
    }

    public function execute() {
        $cache = \cache::make( 'block_wpfeed', 'cache' );
        $cache->delete( 'posts' );
        $cache->delete( 'error' );
    }
}