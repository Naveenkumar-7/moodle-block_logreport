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
 * Displays different views of the logs.
 *
 * @package    block_logreport
 * @copyright  2018 onwards Naveen kumar(naveen@eabyas.in)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define('AJAX_SCRIPT', true);
require_once('../../config.php');

require_login();
$courseid  = optional_param('courseid', 1, PARAM_INT);// Course ID.

// Get course details.
$course = null;
if ($courseid) {
    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
    require_login($course);
    $context = context_course::instance($course->id);
} else {
    require_login();
    $context = context_system::instance();
    $PAGE->set_context($context);
}

require_capability('report/log:view', $context);

$reportlog = new report_log_renderable('logstore_standard', $courseid);
$reportlog->showusers = true;

$activities = $reportlog->get_activities_list();

echo json_encode([ 'activities' => $activities]);

die();
