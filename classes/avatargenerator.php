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
 *
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   moodlefreak-block_mfavatar
 * @copyright 2018 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/

namespace block_mfavatar;

defined('MOODLE_INTERNAL') || die();

use context_user;
use Intervention\Image\Image;
use Laravolt\Avatar\Avatar;
use stdClass;

class avatargenerator {

    protected $config = [
        // Supported: "gd", "imagick"
        'driver' => 'gd',

        // Initial generator class
        'generator' => \Laravolt\Avatar\Generator\DefaultGenerator::class,

        // Whether all characters supplied must be replaced with their closest ASCII counterparts
        'ascii' => true,

        // Image shape: circle or square
        'shape' => 'circle',
        'width' => 100,
        'height' => 100,

        // Number of characters used as initials. If name consists of single word, the first N character will be used
        'chars' => 2,

        // font size
        'fontSize' => 48,

        // convert initial letter to uppercase
        'uppercase' => true,

        // Fonts used to render text.
        // If contains more than one fonts, randomly selected based on name supplied
        'fonts' => [__DIR__ . '/../fonts/OpenSans-Bold.ttf'],

        // List of foreground colors to be used, randomly selected based on name supplied
        'foregrounds' => [
            '#FFFFFF',
        ],

        // List of background colors to be used, randomly selected based on name supplied
        'backgrounds' => [
            '#f44336',
            '#E91E63',
            '#9C27B0',
            '#673AB7',
            '#3F51B5',
            '#2196F3',
            '#03A9F4',
            '#00BCD4',
            '#009688',
            '#4CAF50',
            '#8BC34A',
            '#CDDC39',
            '#FFC107',
            '#FF9800',
            '#FF5722',
        ],

        'border' => [
            'size' => 0,

            // border color, available value are:
            // 'foreground' (same as foreground color)
            // 'background' (same as background color)
            // or any valid hex ('#aabbcc')
            'color' => 'foreground',
        ],
    ];

    /**
     * @var Avatar
     */
    protected $avatar;

    /**
     * Allow overriding user picture.
     *
     * @var bool
     */
    protected $allowOverride = false;

    /**
     * avatargenerator constructor.
     */
    public function __construct() {
        global $CFG;
        require_once __DIR__ . '/../vendor/autoload.php';
        require_once("$CFG->libdir/gdlib.php");
        $this->avatar = new Avatar($this->config);
    }

    /**
     * set_avatar_for_all_users
     *
     * @param string $parts
     *
     * @throws \dml_exception
     */
    public function set_avatar_for_all_users($parts = 'fullname') {

        global $DB;
        $rs = $DB->get_recordset('user');

        foreach ($rs as $row) {

            switch ($parts) {
                default:
                    // fullname.
                    $imageObject = $this->get_avatar(fullname($row));
                    $this->save($row, $imageObject);
            }

        }

        $rs->close();
    }

    /**
     * @param $string
     *
     * @return Avatar
     */
    private function get_avatar(string $string) : Avatar {
        return $this->avatar->create($string);
    }

    /**
     * Save user image.
     *
     * @param stdClass $user
     * @param   Avatar $avatar
     *
     * @throws \dml_exception
     */
    private function save(stdClass $user, Avatar $avatar) {
        global $DB;

        $context = context_user::instance($user->id, MUST_EXIST);

        //
        $tempfile = tempnam(sys_get_temp_dir(), 'mfavatar') . '.png';

        // Save to temp.
        $avatar->save($tempfile, 100);

        // Set new picture.
        $newpicture = (int)process_new_icon($context, 'user', 'icon', 0, $tempfile, true);
        if ($newpicture != $user->picture) {
            $DB->set_field('user', 'picture', $newpicture, ['id' => $user->id]);
        }

        // Remove temp.
        @unlink($tempfile);
    }

}