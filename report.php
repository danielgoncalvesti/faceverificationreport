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

require 'vendor/autoload.php';
use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\DropboxFile;
use Kunnu\Dropbox\Models\Thumbnail;


class quiz_faceverificationreport_report extends quiz_default_report {

    public function display($quiz, $cm, $course) {
        global $OUTPUT, $DB;

        //Configure Dropbox Application
        $app = new DropboxApp("9d8ukwyihgvpmyl", "00v4mdt2xc5zpj4", "ysIiD9OB6K0AAAAAAAAAAa5mO0TRGhL8j7ouX6ORHiG2YabMxG-2m2jyDypS-bMQ");
        //Configure Dropbox service
        $dropbox = new Dropbox($app);


        $results = new stdClass();
        $this->context = context_module::instance($cm->id);

        $courseid = $quiz->course;
        $quizid =  $quiz->id;
        
        $sql = "SELECT v.id, v.username, v.facedetectionscore, v.euclidean_distance, v.pathfiledropbox, v.timecreated FROM {fvquiz_validation} v WHERE courseid = $courseid AND quizid = $quizid";
        $validations = $DB->get_records_sql($sql);


        foreach ($validations as $key => $value) {
        
            $validations[$key]->timecreated = date('d-M-Y H:m',$validations[$key]->timecreated);
            
            if ($validations[$key]->pathfiledropbox != null) {
                $validations[$key]->pathfiledropbox = base64_encode($dropbox->getThumbnail($validations[$key]->pathfiledropbox, $format = 'png')->getContents());

                $username = $validations[$key]->username;
                $fvquiz_registed = $DB->get_record('fvquiz_registered', array('username'=>$username, 'courseid'=>$courseid), '*', MUST_EXIST);

                if ($fvquiz_registed->pathfiledropbox != null && !empty($fvquiz_registed->pathfiledropbox)){
                    $validations[$key]->pathfiledropboxregistered = base64_encode($dropbox->getThumbnail($fvquiz_registed->pathfiledropbox, $format = 'png')->getContents());
                } else {
                    $validations[$key]->pathfiledropboxregistered = "iVBORw0KGgoAAAANSUhEUgAAAAUA
                    AAAFCAYAAACNbyblAAAAHElEQVQI12P4//8/w38GIAXDIBKE0DHxgljNBAAO
                        9TXL0Y4OHwAAAABJRU5ErkJggg==";
                }
            } else {
                $validations[$key]->pathfiledropbox = "iVBORw0KGgoAAAANSUhEUgAAAAUA
                AAAFCAYAAACNbyblAAAAHElEQVQI12P4//8/w38GIAXDIBKE0DHxgljNBAAO
                    9TXL0Y4OHwAAAABJRU5ErkJggg==";
                $validations[$key]->pathfiledropboxregistered = "iVBORw0KGgoAAAANSUhEUgAAAAUA
                    AAAFCAYAAACNbyblAAAAHElEQVQI12P4//8/w38GIAXDIBKE0DHxgljNBAAO
                        9TXL0Y4OHwAAAABJRU5ErkJggg==";    
            }
        }
        

        // Start output.
        $this->print_header_and_tabs($cm, $course, $quiz, 'quiz_faceverificationreport');

        $results->data = array_values($validations);

        echo $OUTPUT->render_from_template('quiz_faceverificationreport/searchresults', $results);
        echo $OUTPUT->download_dataformat_selector('Download', 'report/faceverificationreport/download.php', 'dataformat', array('courseid' => $courseid, 'quizid' => $quizid));

    }
}
