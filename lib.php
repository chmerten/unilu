<?php

// WebService for UNILU Enrolments
// Christophe Mertens (ULB) : christophe.mertens@ulb.be

defined('MOODLE_INTERNAL') || die();

class enrol_unilu_plugin extends enrol_plugin {
    //private $logfile = false;

    //public function allow_enrol(stdClass $instance) { return true; }
    //public function allow_unenrol(stdClass $instance) { return true; }
    //public function allow_manage(stdClass $instance) { return true; }

    public function get_course_id($idnumber, $strictness=IGNORE_MISSING) {
        global $DB;
        return $DB->get_field('course', 'id', array('shortname' => $idnumber), $strictness);
    }

    public function get_user_id($username, $strictness=IGNORE_MISSING) {
        global $DB;
        return $DB->get_field('user', 'id', array('username' => $username), $strictness);
    }

    public function get_instance($courseid) {
        global $DB;
        $instance = $DB->get_record('enrol', array('courseid' => $courseid, 'enrol' => 'unilu'));
        if (!$instance) {
            if ($course = $DB->get_record('course', array('id' => $courseid))) {
                $this->add_instance($course);
                $instance = $DB->get_record('enrol', array('courseid' => $courseid, 'enrol' => 'unilu'));
            }
        }
        if (!$instance) {
            $errorparams = new stdClass();
            $errorparams->courseid = $courseid;
            throw new moodle_exception('noinstance', 'enrol_unilu', $errorparams);
        }
        return $instance;
    }


}
