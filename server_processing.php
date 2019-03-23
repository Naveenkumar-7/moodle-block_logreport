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
global $CFG, $PAGE,$DB;

require_once($CFG->dirroot.'/lib/tablelib.php');

$id          = optional_param('id', 0, PARAM_INT);// Course ID.
$group       = optional_param('group', 0, PARAM_INT); // Group to display.
$user        = optional_param('user', 0, PARAM_INT); // User to display.
$date        = optional_param('date', 0, PARAM_INT); // Date to display.
$modid       = optional_param('modid', 0, PARAM_ALPHANUMEXT); // Module id or 'site_errors'.
$modaction   = optional_param('modaction', '', PARAM_ALPHAEXT); // An action as recorded in the logs.
$showcourses = optional_param('showcourses', false, PARAM_BOOL); // Whether to show courses if we're over our limit.
$showusers   = optional_param('showusers', false, PARAM_BOOL); // Whether to show users if we're over our limit.
$chooselog   = optional_param('chooselog', false, PARAM_BOOL);
$logformat   = optional_param('download', '', PARAM_ALPHA);
$logreader   = optional_param('logreader', 'logstore_standard', PARAM_COMPONENT);
$edulevel    = optional_param('edulevel', -1, PARAM_INT); // Educational level.
$origin      = optional_param('origin', '', PARAM_TEXT); // Event origin.
$draw = optional_param('draw',  0,  PARAM_INT);
$start = optional_param('start',  0,  PARAM_INT);
$length = optional_param('length',  10,  PARAM_INT);

// Get course details.
$course = null;
if ($id) {
    $course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
    require_login($course);
    $context = context_course::instance($course->id);
} else {
    require_login();
    $context = context_system::instance();
    $PAGE->set_context($context);
}

require_capability('report/log:view', $context);

$reportlog = new report_log_renderable($logreader, $course, $user, $modid, $modaction, $group, $edulevel, $showcourses, $showusers,
        $chooselog, true, $url, $date, $logformat, $page, $perpage, 'timecreated DESC', $origin);
$readers = $reportlog->get_readers();
$filter = new stdClass;

if (!empty($course)) {
    $filter->courseid = $course->id;
} else {
    $filter->courseid = 0;
}

$filter->userid = $user;
$filter->modid = $modid;
$filter->groupid = $group;
$filter->logreader = $readers[$reportlog->selectedlogreader];
$filter->edulevel = $edulevel;
$filter->action = $modaction;
$filter->date = $date;
$filter->orderby = $order;
$filter->origin = $origin;
// If showing site_errors.
if ('site_errors' === $modid) {
    $filter->siteerrors = true;
    $filter->modid = 0;
}

$tablelog = new \block_logreport\dataprovider('report_log', $filter);
$tablelog->currpage = $start / $length;
$tablelog->pagesize = $length;

$tablelog->query_db($length);
$formattedrow = array();
$tablelog->define_columns(array('time', 'fullnameuser', 'relatedfullnameuser', 'context', 'component',
        'eventname', 'description', 'origin', 'ip'));
foreach ($tablelog->rawdata as $row) {
    $formattedrow[] = array_values($tablelog->format_row($row));
}

echo json_encode(["draw" => $draw,
                  "recordsTotal" => $tablelog->total,
                  "recordsFiltered" => $tablelog->total,
                  "data" => $formattedrow]);
