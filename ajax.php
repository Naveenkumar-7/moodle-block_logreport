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

$reportlog = new report_log_renderable('logstore_standard', $courseid);
$reportlog->showusers = true;

$groups = $reportlog->get_group_list();
$users = $reportlog->get_user_list();
$activities = $reportlog->get_activities_list();

echo json_encode([ 'activities' => $activities]);
// echo json_encode(compact('groups', 'users', 'activities'));

die();