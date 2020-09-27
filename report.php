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

defined('MOODLE_INTERNAL') || die();


class quiz_faceverificationreport_report extends quiz_default_report {

    public function display($quiz, $cm, $course) {
        global $OUTPUT, $DB;

        $results = new stdClass();
        $this->context = context_module::instance($cm->id);

        $courseid = $quiz->course;
        $quizid =  $quiz->id;

        $sql = "SELECT v.id, v.username, v.facedetectionscore, v.euclidean_distance, v.timecreated FROM {fvquiz_validation} v WHERE courseid = $courseid AND quizid = $quizid";
        $validations = $DB->get_records_sql($sql);

        foreach ($validations as $key => $value) {
            $validations[$key]->timecreated = date('d-M-Y H:m',$validations[$key]->timecreated);
        }

        // Start output.
        $this->print_header_and_tabs($cm, $course, $quiz, 'quiz_faceverificationreport');

        $results->data = array_values($validations);

        echo $OUTPUT->render_from_template('quiz_faceverificationreport/searchresults', $results);
        echo $OUTPUT->download_dataformat_selector('Download', 'report/faceverificationquiz/download.php', 'dataformat', array('couseid' => $courseid, 'quizid' => $quizid));

    }
}