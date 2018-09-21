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
require_once("../../config.php");
global $CFG, $PAGE;

$PAGE->set_url('/blocks/logreport/index.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('report');

require_login();
$context = context_system::instance();
$PAGE->set_context($context);
require_capability('report/log:view', $context);

$title = get_string('pluginname', 'block_logreport');
$PAGE->navbar->add($title);

$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_cacheable(true);


$PAGE->requires->jquery_plugin('ui-css');
$PAGE->requires->css('/blocks/logreport/style/jquery.dataTables.min.css');
$PAGE->requires->css('/blocks/logreport/style/select2.min.css');

$PAGE->requires->js_call_amd('block_logreport/logreport', 'Init');
$PAGE->requires->js_call_amd('block_logreport/logreport', 'ProcessFilter');
$PAGE->requires->js_call_amd('block_logreport/logreport', 'InitDatatable');

echo $OUTPUT->header();
$output = $PAGE->get_renderer('block_logreport');

echo $output->charts();
$reportlog = new report_log_renderable('logstore_standard', 1);
echo $output->report_selector_form($reportlog);
echo $output->render_from_template('block_logreport/reporttable', $context);
echo $OUTPUT->footer();