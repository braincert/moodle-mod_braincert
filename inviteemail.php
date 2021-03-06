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
 * Invite by E-mail form for upcoming class.
 *
 * @package    mod_braincert
 * @author BrainCert <support@braincert.com>
 * @copyright  BrainCert (https://www.braincert.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/mod/braincert/classes/invite_by_email_form.php');
require_once($CFG->dirroot.'/mod/braincert/locallib.php');

$bcid = required_param('bcid', PARAM_INT);   // Virtual Class ID.

$PAGE->set_url('/mod/braincert/inviteemail.php', array('bcid' => $bcid));

$braincertrec = $DB->get_record('braincert', array('class_id' => $bcid));
if (!$course = $DB->get_record('course', array('id' => $braincertrec->course))) {
    print_error('invalidcourseid');
}

require_login($course);
$coursecontext = context_course::instance($course->id);
require_capability('mod/braincert:addinstance', $coursecontext);
$PAGE->set_pagelayout('incourse');
$PAGE->navbar->add(get_string('pluginname', 'braincert'));
$inviteemail = get_string('inviteemail', 'braincert');
$PAGE->navbar->add($inviteemail);

$PAGE->requires->css('/mod/braincert/css/styles.css', true);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('inviteemail', 'braincert'));

$mform = new \mod_braincert\invite_by_email_form($CFG->wwwroot.'/mod/braincert/inviteemail.php?bcid='.$bcid);

if ($invitebyemail = $mform->get_data()) {
    $emailmessage = braincert_invitation_email_body($braincertrec, $invitebyemail->emailmessage['text']);
    $emaillists = explode(",", $invitebyemail->emailto);
    foreach ($emaillists as $emailid) {
        if ($emailuserrec = $DB->get_record('user', array('email' => $emailid))) {
            $emailuser = $emailuserrec;
        } else {
            $emailuser = new stdClass();
            $emailuser->email             = $emailid;
            $emailuser->firstname         = '';
            $emailuser->lastname          = '';
            $emailuser->maildisplay       = true;
            $emailuser->mailformat        = 1;
            $emailuser->id                = -1;
            $emailuser->firstnamephonetic = '';
            $emailuser->lastnamephonetic  = '';
            $emailuser->middlename        = '';
            $emailuser->alternatename     = '';
        }
        $mailresults = email_to_user($emailuser, $USER, $invitebyemail->emailsubject, $emailmessage, $emailmessage);
        if ($mailresults == 1) {
            echo '<div class="alert alert-success">'.get_string('emailsent', 'braincert').' - '
                .$emailid.'.</strong></div>';
        } else {
            echo '<div class="alert alert-danger">'.get_string('emailnotsent', 'braincert').' '
                .$emailid.'.</strong></div>';
        }
    }
    $mform->display();
} else {
    $mform->display();
}

echo $OUTPUT->footer();
