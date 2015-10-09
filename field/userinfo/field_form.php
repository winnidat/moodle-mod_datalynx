<?php
// This file is part of Moodle - http://moodle.org/.
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 *
 * @package datalynxfield
 * @subpackage userinfo
 * @copyright 2012 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once ("$CFG->dirroot/mod/datalynx/field/field_form.php");


class datalynxfield_userinfo_form extends datalynxfield_form {

    /**
     */
    function field_definition() {
        global $DB;
        
        $mform = & $this->_form;
        
        // -------------------------------------------------------------------------------
        $mform->addElement('header', 'fieldattributeshdr', 
                get_string('fieldattributes', 'datalynx'));
        
        // Info field
        $options = $DB->get_records_menu('user_info_field', array(), 'shortname', 'id,shortname');
        $options = array('' => get_string('choosedots')
        ) + $options;
        $mform->addElement('select', "param1", get_string('infofield', 'datalynxfield_userinfo'), 
                $options);
    }
}
