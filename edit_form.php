<?php
require_once("$CFG->libdir/formslib.php");

class local_greetings_edit_form extends moodleform {
    public function definition() {
       
        $mform = $this->_form;

        $mform->addElement('textarea', 'newmessage', 'update message');
        $mform->setType('newmessage', PARAM_TEXT);
        $mform->setDefault('newmessage',  $this->_customdata['message']);

        $this->add_action_buttons(true);
    }
}
