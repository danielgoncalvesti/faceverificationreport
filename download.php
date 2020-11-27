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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.require_once '../../config.php';

require_once '../../../../config.php';
global $USER, $DB, $CFG;

require_login();

$dataformat = optional_param('dataformat', '', PARAM_ALPHA);
$courseid = optional_param('courseid', '', PARAM_INT);
$quizid = optional_param('quizid', '', PARAM_INT);

$sql = "SELECT v.id, v.username, v.facedetectionscore, v.euclidean_distance, v.timecreated FROM {fvquiz_validation} v WHERE courseid = $courseid AND quizid = $quizid";
$validations = $DB->get_records_sql($sql);


$obj = new ArrayObject( $validations);
$it = $obj->getIterator();

$columns = array(
    'username' => get_string('username', 'quiz_faceverificationreport'),
    'facedetectionscore' => get_string('facedetectionscore', 'quiz_faceverificationreport'),
    'euclidean_distance' => get_string('euclidean_distance', 'quiz_faceverificationreport'),
    'timecreated' => get_string('timecreated', 'quiz_faceverificationreport'),
);

\core\dataformat::download_data('graderdata', $dataformat, $columns, $it, function($record) {

    $record->timecreated = date('d-M-Y H:m',  $record->timecreated);
    unset($record->id);
    // unset($record->tmodified);
      // Process the data in some way.
      // You can add and remove columns as needed
      // as long as the resulting data matches the $column metadata.
      return $record;
});
