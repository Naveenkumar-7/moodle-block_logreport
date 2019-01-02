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
defined('MOODLE_INTERNAL') || die();

class block_logreport extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_logreport');
    }

    public function get_content() {
        global $CFG;
        if ($this->content !== null) {
            return $this->content;
        }
        
        $this->page->requires->css('/blocks/logreport/style/datatables.min.css');
        $this->page->requires->css('/blocks/logreport/style/select2.min.css');
        $this->page->requires->jquery_plugin('ui-css');
        $this->page->requires->js_call_amd('block_logreport/logreport', 'Init');
        $this->page->requires->js_call_amd('block_logreport/logreport', 'ProcessFilter');
        $this->page->requires->js_call_amd('block_logreport/logreport', 'InitDatatable');
        
        $output = $this->page->get_renderer('block_logreport');

        $this->content = new stdClass;
        $this->content->text = '';
        $content = $output->charts();
        $this->content->text .= $content;
        $this->content->text .= html_writer::link($CFG->wwwroot . '/blocks/logreport/index.php',get_string('viewreport',  'block_logreport'),['id' => 'viewreport']);

        return $this->content;
    }
}
