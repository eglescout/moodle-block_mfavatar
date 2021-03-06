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
 * html render class
 *
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_mfavatar
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 **/
class block_mfavatar_renderer extends plugin_renderer_base {

    /**
     * add_javascript_module
     *
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function add_javascript_module() {
        global $PAGE, $CFG, $USER;

        $config = get_config('block_mfavatar');

        // load swfobject 2.2
        if (empty($config->webrtc_enabled)) {
            $PAGE->requires->js(new moodle_url($CFG->wwwroot . '/blocks/mfavatar/js/swfobject.js'), true);
        }

        $jsmodule = [
            'name' => 'block_mfavatar',
            'fullpath' => '/blocks/mfavatar/module.js',
            'requires' => ['io-base',],
        ];

        $PAGE->requires->js_init_call('M.block_mfavatar.init', [
            $CFG->wwwroot . '/blocks/mfavatar/swf/snapshot.swf?' . time(),
            $CFG->wwwroot . '/blocks/mfavatar/swf/expressInstall.swf',
            [
                'sessionid' => $USER->sesskey,
                'uploadPath' => $CFG->wwwroot . '/blocks/mfavatar/ajax.php',
                'text_select_device' => get_string('flash:textselectdevice', 'block_mfavatar'),
                'text_make_snapshot' => get_string('flash:text_make_snapshot', 'block_mfavatar'),
                'text_result_field' => get_string('flash:text_result_field', 'block_mfavatar'),
                'text_feed_field' => get_string('flash:text_feed_field', 'block_mfavatar'),
                'failed_saving' => get_string('flash:failed_saving', 'block_mfavatar'),
                'success_saving' => get_string('flash:success_saving', 'block_mfavatar'),
            ],
            $config->webrtc_enabled,
        ], false, $jsmodule);
    }

    /**
     * Add the snapshot tool
     *
     * @return string
     * @throws coding_exception
     */
    public function snapshot_tool() {
        // @TODO convert to mustache.
        global $USER; // used for the profile link

        $html = '<div id="snapshotholder" style="display: none;">
                    <div id="snapshot">
                        <h1>' . get_string('installflash', 'block_mfavatar') . '</h1>
                        <p><a href="https://www.adobe.com/go/getflashplayer"><img src="https://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a></p>
                    </div>
                </div>';

        // Add webrtc container.
        $html .= '<div id="snapshotholder_webrtc" style="display: none;">
                    <video autoplay></video>
                    <div id="previewholder">
                        <canvas id="render"></canvas>
                        <canvas id="preview"></canvas>
                    </div>
                 </div>
                 <div class="pt-3 clearboth">
                    <button id="snapshot" class="btn btn-primary">' . get_string('flash:text_make_snapshot', 'block_mfavatar') . '</button>
                    <a href="/user/profile.php?id=' . $USER->id . '" class="btn btn-info">' . get_string('returntoprofile', 'block_mfavatar') . '</a>
                 </div>';

        return $html;
    }

}
