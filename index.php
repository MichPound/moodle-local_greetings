<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Disply greetings plugin page.
 *
 * @package     local_greetings
 * @copyright   2022 Michael Pound <michael@brickfieldlabs.ie>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');
require_once(__DIR__ . '/message_form.php');

require_login();

if (isguestuser()) {
    throw new moodle_exception('noguest');
}

$context = context_system::instance();

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/greetings/index.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string('pluginname', 'local_greetings'));
$PAGE->set_title($SITE->fullname);

$messageform = new local_greetings_message_form();

echo $OUTPUT->header();

if (isloggedin()) {
    echo local_greetings_get_greeting($USER);
} else {
    echo get_string('greetinguser', 'local_greetings');
}

if (has_capability('local/greetings:postmessages', $context)) {
    $messageform->display();
}

$action = optional_param('action', '', PARAM_TEXT);

$PAGE->requires->js_call_amd('local_greetings/modal', 'initialise');

if ($action == 'del') {
    require_sesskey();
    $id = required_param('id', PARAM_TEXT);

    if (has_capability('local/greetings:deleteownmessage', $context) ||
        has_capability('local/greetings:deleteanymessage', $context)) {
        $params = array('id' => $id);

        if (!has_capability('local/greetings:deleteanymessage', $context)) {
            $params += ['userid' => $USER->id];
        }

        $DB->delete_records('local_greetings_messages', $params);

        redirect($PAGE->url);
    }
}

$userfields = \core_user\fields::for_name()->with_identity($context);
$userfieldssql = $userfields->get_sql('u');

if (has_capability('local/greetings:viewmessages', $context)) {
    $sql = "SELECT m.id, m.message, m.timecreated, m.userid {$userfieldssql->selects}
            FROM {local_greetings_messages} m
        LEFT JOIN {user} u ON u.id = m.userid
        ORDER BY timecreated DESC";

    $messages = $DB->get_records_sql($sql);

    echo $OUTPUT->box_start('card-columns');

    foreach ($messages as $m) {

        echo html_writer::start_tag('div', array('class' => 'card'));
        echo html_writer::start_tag('div', array('class' => 'card-body'));

        echo html_writer::start_tag('div', array('id' => 'modal_card', 'data-name' => $m->firstname,
             'data-message' => $m->message, 'data-date' => $m->timecreated));
        echo html_writer::tag('p', format_text($m->message, FORMAT_PLAIN), array('class' => 'card-text'));
        echo html_writer::tag('p', get_string('postedby', 'local_greetings', $m->firstname), array('class' => 'card-text'));
        echo html_writer::end_tag('div');

        if ((has_capability('local/greetings:deleteownmessage', $context) && $m->userid == $USER->id) ||
            has_capability('local/greetings:deleteanymessage', $context)) {
            echo html_writer::start_tag('p', array('class' => 'card-footer text-center'));
            echo html_writer::link(
                new moodle_url(
                    '/local/greetings/edit.php',
                    array('action' => 'edit', 'id' => $m->id, 'sesskey' => sesskey())
                ),
                $OUTPUT->pix_icon('t/editstring', ''),
                array('role' => 'button', 'id' => 'edit_button', 'aria-label' => get_string('edit'), 'title' => get_string('edit'))
            );
            echo html_writer::link(
                new moodle_url(
                    '/local/greetings/index.php',
                    array('action' => 'del', 'id' => $m->id, 'sesskey' => sesskey())
                ),
                $OUTPUT->pix_icon('t/delete', ''),
                array('role' => 'button', 'aria-label' => get_string('delete'), 'title' => get_string('delete'))
            );
            echo html_writer::end_tag('p');
        }

        echo html_writer::start_tag('p', array('class' => 'card-text'));
        echo html_writer::tag('small', userdate($m->timecreated), array('class' => 'text-muted'));
        echo html_writer::end_tag('p');
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('div');
    }

    echo $OUTPUT->box_end();
}

if ($data = $messageform->get_data()) {
    require_capability('local/greetings:postmessages', $context);

    $message = required_param('message', PARAM_TEXT);

    if (!empty($message)) {
        $record = new stdClass;
        $record->message = $message;
        $record->timecreated = time();
        $record->userid = $USER->id;

        $DB->insert_record('local_greetings_messages', $record);

        redirect($PAGE->url);
    }
}

echo $OUTPUT->footer();
