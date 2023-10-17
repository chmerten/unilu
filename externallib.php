<?php

// WebService for UNILU Enrolments
// Christophe Mertens (ULB) : christophe.mertens@ulb.be

require_once($CFG->libdir . '/externallib.php');

class enrol_unilu_external extends external_api
{
    ////////////////////// CREATE COURSE ////////////////////////
    public static function sync_course_parameters() {
        return new external_function_parameters(
            array(
                'fullname' => new external_value(PARAM_TEXT, 'Titre complet du cours. ex : Biologie'),
                'shortname'  => new external_value(PARAM_TEXT, 'Titre abrégé du cours : BIO104-0-202324'),
                'idnumber' => new external_value(PARAM_TEXT, 'Identifiant du cours (dépend du système LMD et de l annee academique). ex : BIO104-0-202324'),
                'categoryidnumber'  => new external_value(PARAM_TEXT, 'Identifiant de la promotion au sein de laquelle le cours est donné. Exemple : BAC1_SBM_0101')
            )
        );
    }

    public static function sync_course(String $fullname, String $shortname, String $idnumber, String $categoryidnumber)
    {
        global $DB;
        #require_once($CFG->dirroot . '/user/lib.php');
        #require_once($CFG->dirroot . '/user/profile/lib.php');

        $params_array = array(
            'fullname' => $fullname,
            'shortname' => $shortname,
            'idnumber' => trim($idnumber),
            'categoryidnumber' => $categoryidnumber,
        );

        // Validate parameters
        $params = self::validate_parameters(self::sync_course_parameters(),$params_array);

        // Retrieve the enrolment plugin.
        $enrol = enrol_get_plugin('unilu');
        if (empty($enrol)) {
            throw new moodle_exception('pluginnotinstalled', 'enrol_unilu');
        }
        // Check if the plugin is enabled.
        if (!enrol_is_enabled($enrol->get_name())) {
            throw new moodle_exception('plugindisabled', 'enrol_unilu');
        }
        // This function does sanity and security checks on the context that was passed to the external function. This is required for
        // almost all external functions.
        $context = context_system::instance();
        self::validate_context($context);
        $course = $DB->get_record('course', array('idnumber' => $idnumber));

        $categoryidnumber = strtoupper(trim($categoryidnumber));
        $category = $DB->get_record('course_categories', array('idnumber' => $categoryidnumber));

        if (!$category) {
            $errorparams = new stdClass();
            $errorparams->categoryidnumber = $categoryidnumber;
            throw new moodle_exception('nocoursecategory', 'enrol_unilu', '', $errorparams);
        }

        $course_basic_fields = array(
            'fullname' => $fullname,
            'shortname' => $shortname,
            'idnumber' => trim($idnumber),
            'category' => $category->id
        );


        if ($course) {
            $context = context_course::instance($course->id, MUST_EXIST);
            self::validate_context($context);
            require_capability('moodle/course:update', $context);
            foreach ($course_basic_fields as $fieldid => $fieldvalue) {
                if ($fieldvalue !== $course->$fieldid) {
                    $course->$fieldid = $fieldvalue;
                }
            }
            return $course->id;
        }
        else{
            $context = context_coursecat::instance($category->id, MUST_EXIST);
            self::validate_context($context);
            require_capability('moodle/course:create', $context);

            // Create the course.
            $course = new stdClass();
            foreach ($course_basic_fields as $fieldid => $fieldvalue) {
                $course->$fieldid = $fieldvalue;
            }
            $course->format = 'topics'; // Set the desired course format.
            $courseid = $DB->insert_record('course', $course);
            if (!$courseid) {
                throw new moodle_exception('createcoursefailed');
            }
            return $courseid;
        }
    }

    public static function sync_course_returns() {
        return new external_value(PARAM_INT, 'Course id');
    }

}


