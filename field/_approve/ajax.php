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
 * @package dataformfield
 * @subpackage _approve
 * @copyright 2013 Ivan Šakić
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('AJAX_SCRIPT')) {
    define('AJAX_SCRIPT', true);
}

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.php');
require_once("$CFG->dirroot/mod/dataform/mod_class.php");
require_once("$CFG->dirroot/mod/dataform/entries_class.php");

$d = required_param('d', PARAM_INT);
$viewid = required_param('view', PARAM_INT);
$action = required_param('action', PARAM_ALPHA);
$entryid = required_param('entryid', PARAM_INT);
$sesskey = required_param('sesskey', PARAM_TEXT);

$cm = get_coursemodule_from_instance('dataform', $d, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$data = $DB->get_record('dataform', array('id' => $d), '*', MUST_EXIST);

require_sesskey();

$context = context_module::instance($cm->id);
require_login($course, true, $cm);
require_capability('mod/dataform:approve', $context);

global $DB;
$completiontype = COMPLETION_UNKNOWN;
if ($action == 'approve') {
    $DB->set_field('dataform_entries', 'approved', 1, array('id' => $entryid));
    $df = new dataform($d);
    $entriesclass = new dataform_entries($df);
    $processed = $entriesclass->create_approved_entries_for_team(array($entryid));
    if ($processed) {
        $eventdata = (object) array('view' => $df->get_view_from_id($viewid), 'items' => $processed);
        $df->events_trigger("entryupdated", $eventdata);
    }
    $return = $DB->get_field('dataform_entries', 'approved', array('id' => $entryid)) == 1;
    $completiontype = COMPLETION_COMPLETE;
} else if ($action == 'disapprove') {
    $DB->set_field('dataform_entries', 'approved', 0, array('id' => $entryid));
    $return = $DB->get_field('dataform_entries', 'approved', array('id' => $entryid)) == 0;
    $completiontype = COMPLETION_INCOMPLETE;
} else {
    $return = false;
}
// Update completion state
$completion = new completion_info($course);
if($completion->is_enabled($cm) && $cm->completion == COMPLETION_TRACKING_AUTOMATIC && $data->completionentries) {
    $userid = $DB->get_field('dataform_entries', 'userid', array('id' => $entryid));
    $completion->update_state($cm, $completiontype, $userid);
}

ob_clean();
echo json_encode($return);

die;
