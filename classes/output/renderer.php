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
namespace block_logreport\output;

defined('MOODLE_INTERNAL') || die;

use context_system;
use html_writer;
use moodle_url;
use plugin_renderer_base;
use stdClass;

class renderer extends plugin_renderer_base {

    public function render_renderreport($page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_logreport/renderreport', $data);
    }
    /**
     * This function is used to generate and display selector form
     *
     * @param report_log_renderable $reportlog log report.
     */
    public function report_selector_form($reportlog) {
        $output = html_writer::start_tag('form', array('class' => 'logselecform', 'action' => $reportlog->url, 'method' => 'get'));
        $output .= html_writer::start_div();
        $output .= html_writer::empty_tag('input', array('type' => 'hidden',
                                                         'name' => 'chooselog',
                                                         'value' => '1'));
        $output .= html_writer::empty_tag('input', array('type' => 'hidden',
                                                         'name' => 'showusers',
                                                         'value' => $reportlog->showusers));
        $output .= html_writer::empty_tag('input', array('type' => 'hidden',
                                                         'name' => 'showcourses',
                                                         'value' => $reportlog->showcourses));

        $selectedcourseid = empty($reportlog->course) ? 0 : $reportlog->course->id;

        // Add course selector.
        $sitecontext = context_system::instance();
        $courses = $reportlog->get_course_list();
        if (!empty($courses) && $reportlog->showcourses) {
            $output .= html_writer::label(get_string('selectacourse'), 'menuid', false, array('class' => 'accesshide'));
            $output .= html_writer::select($courses, "id", $selectedcourseid, null, ['class' => 'cousrefilter',
                                                                                     'data-select2' => true]);
        } else {
            $courses = array();
            $courses[$selectedcourseid] = get_course_display_name_for_list($reportlog->course) .
                                          (($selectedcourseid == SITEID) ? ' (' . get_string('site') . ') ' : '');
            $output .= html_writer::label(get_string('selectacourse'), 'menuid', false, array('class' => 'accesshide'));
            $output .= html_writer::select($courses, "id", $selectedcourseid, false, ['class' => 'cousrefilter',
                                                                                      'data-select2' => true]);
            // Check if user is admin and this came because of limitation on number of courses to show in dropdown.
            if (has_capability('report/log:view', $sitecontext)) {
                $a = new stdClass();
                $a->url = new moodle_url('/report/log/index.php', array('chooselog' => 0,
                                                                        'group' => $reportlog->get_selected_group(),
                                                                        'user' => $reportlog->userid,
                                                                        'id' => $selectedcourseid,
                                                                        'date' => $reportlog->date,
                                                                        'modid' => $reportlog->modid,
                                                                        'showcourses' => 1,
                                                                        'showusers' => $reportlog->showusers));
                $a->url = $a->url->out(false);
                print_string('logtoomanycourses', 'moodle', $a);
            }
        }

        // Add group selector.
        $groups = $reportlog->get_group_list();
        if (!empty($groups)) {
            $output .= html_writer::label(get_string('selectagroup'), 'menugroup', false, array('class' => 'accesshide'));
            $output .= html_writer::select($groups, "group", $reportlog->groupid, get_string("allgroups"), ['data-select2' => true]);
        }

        // Add user selector.
        $users = $reportlog->get_user_list();

        if ($reportlog->showusers) {
            $output .= html_writer::label(get_string('selctauser'), 'menuuser', false, array('class' => 'accesshide'));
            $output .= html_writer::select($users, "user", $reportlog->userid, get_string("allparticipants"), ['data-select2' => true]);
        } else {
            $users = array();
            if (!empty($reportlog->userid)) {
                $users[$reportlog->userid] = $reportlog->get_selected_user_fullname();
            } else {
                $users[0] = get_string('allparticipants');
            }
            $output .= html_writer::label(get_string('selctauser'), 'menuuser', false, array('class' => 'accesshide'));
            $output .= html_writer::select($users, "user", $reportlog->userid, false, ['data-select2' => true]);
            $a = new stdClass();
            $a->url = new moodle_url('/report/log/index.php', array('chooselog' => 0,
            'group' => $reportlog->get_selected_group(), 'user' => $reportlog->userid,
            'id' => $selectedcourseid, 'date' => $reportlog->date, 'modid' => $reportlog->modid,
            'showusers' => 1, 'showcourses' => $reportlog->showcourses));
            $a->url = $a->url->out(false);
            $output .= html_writer::start_span('m-x-1');
            print_string('logtoomanyusers', 'moodle', $a);
            $output .= html_writer::end_span();
        }

        // Add date selector.
        $dates = $reportlog->get_date_options();
        $output .= html_writer::label(get_string('date'), 'menudate', false, array('class' => 'accesshide'));
        $output .= html_writer::select($dates, "date", $reportlog->date, get_string("alldays"), ['data-select2' => true]);

        // Add activity selector.
        $activities = $reportlog->get_activities_list();
        $output .= html_writer::label(get_string('activities'), 'menumodid', false, array('class' => 'accesshide'));
        $output .= html_writer::select($activities, "modid", $reportlog->modid, get_string("allactivities"), ['data-select2' => true]);

        // Add actions selector.
        $output .= html_writer::label(get_string('actions'), 'menumodaction', false, array('class' => 'accesshide'));
        $output .= html_writer::select($reportlog->get_actions(), 'modaction', $reportlog->action, get_string("allactions"), ['data-select2' => true]);

        // Add origin.
        $origin = $reportlog->get_origin_options();
        $output .= html_writer::label(get_string('origin', 'report_log'), 'menuorigin', false, array('class' => 'accesshide'));
        $output .= html_writer::select($origin, 'origin', $reportlog->origin, false, ['data-select2' => true]);

        // Add edulevel.
        $edulevel = $reportlog->get_edulevel_options();
        $output .= html_writer::label(get_string('edulevel'), 'menuedulevel', false, array('class' => 'accesshide'));
        $output .= html_writer::select($edulevel, 'edulevel', $reportlog->edulevel, false, ['data-select2' => true]) . $this->help_icon('edulevel');

        // Add reader option.
        // If there is some reader available then only show submit button.
        $readers = $reportlog->get_readers(true);
        if (!empty($readers)) {
            if (count($readers) == 1) {
                $attributes = array('type' => 'hidden', 'name' => 'logreader', 'value' => key($readers));
                $output .= html_writer::empty_tag('input', $attributes);
            } else {
                $output .= html_writer::label(get_string('selectlogreader', 'report_log'), 'menureader', false,
                                              array('class' => 'accesshide'));
                $output .= html_writer::select($readers, 'logreader', $reportlog->selectedlogreader, false,
                                               ['data-select2' => true]);
            }
            $output .= html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('clear', 'block_logreport'),
            'class' => 'btn btn-secondary', 'id' => 'lr_clearfilter'));
            $output .= html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('gettheselogs'),
            'class' => 'btn btn-primary'));
        }
        $output .= html_writer::end_div();
        $output .= html_writer::end_tag('form');
        return $output;
    }
    public function charts(){
        global $CFG;
            $tabsdata = (new \block_logreport\dataprovider)->generate_graphdata();
            foreach ($tabsdata as $key => $tab) {
                $chart = new \core\chart_line();
                $series = new \core\chart_series('Number of hits', array_values($tab));
                $chart->add_series($series);
                $chart->set_labels(array_keys($tab));
                $tabs[] = ['id' => $key,
                           'name' => get_string($key, 'block_logreport'),
                           'content' => html_writer::tag('div', $this->render_chart($chart, false),
                                                         ['class' => 'blocktimeline'])];
            }
            $data = ['tabs' => $tabs];
            $renderable = new \block_logreport\output\renderreport($data);
            return $this->render($renderable);
    }
}
