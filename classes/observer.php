<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Event observer.
 *
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   moodlefreak-block_mfavatar
 * @copyright 2018 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/

namespace block_mfavatar;
defined('MOODLE_INTERNAL') || die();

class observer {

    /**
     * User updated
     *
     * @param \core\event\user_updated $event
     *
     * @throws \dml_exception
     */
    public static function user_updated(\core\event\user_updated $event) {

        $enabled = get_config('block_mfavatar' , 'avatar_initials');
        if(empty($enabled)){
            return;
        }

        // Check if we need to override there profile image.
        if ($event->userid > 1){
            $avatargenerator = new avatargenerator();
            $avatargenerator->set_avatar_single_user(\core_user::get_user($event->userid));
        }
    }
}