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
 * Search area for block_html blocks
 *
 * @package block_course_modules
 * @copyright 2022 Rizwana
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_course_modules\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Modules of a course
 *
 * @package block_course_modules
 * @copyright 2022 Rizwana
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content {

    public function get_course_modules ($course) {
        global $OUTPUT, $CFG, $USER, $DB;
        require_once($CFG->dirroot.'/course/lib.php');

        $modinfo = get_fast_modinfo($course);
        $data = array();
        foreach ($modinfo->cms as $cm) {
            $context = array();
            // Exclude activities that aren't visible or have no view link (e.g. label). Account for folder being displayed inline.
            if (!$cm->uservisible || (!$cm->has_view() && strcmp($cm->modname, 'folder') !== 0)) {
                continue;
            }

            $context['cmid'] = $cm->id;
            $context['modulename'] = $cm->name;
            $context['module'] = $cm->modname;
            $context['cmadded'] = date("d-M-Y", $cm->added);
            $completionstatus = $DB->get_record_sql("SELECT id FROM {course_modules_completion} WHERE coursemoduleid = $cm->id AND userid = $USER->id AND completionstate > 0");

            if (empty($completionstatus)) {
                $context['completed'] = false;
            } else {
                $context['completed'] = true;
            }

            $data['data'][] = $context;
        }

        return $OUTPUT->render_from_template('block_course_modules/content', $data);

    }
}
