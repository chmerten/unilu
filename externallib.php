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
            'idnumber' => $idnumber,
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

    ////////////// SYNC USER //////////////////////////////////////

    public static function sync_user_parameters() {
        return new external_function_parameters(
            array(
                'firstname' => new external_value(PARAM_TEXT, 'Fistname'),
                'lastname'  => new external_value(PARAM_TEXT, 'Name'),
                'username'  => new external_value(PARAM_TEXT, 'Username'),
                'email'     => new external_value(PARAM_TEXT, 'Email'),
                'phone1'     => new external_value(PARAM_TEXT, 'Phone'),
                'aa'        => new external_value(PARAM_TEXT, 'Année Académique'),
                'matricule' => new external_value(PARAM_TEXT, 'Matricule'),
                'faculte'   => new external_value(PARAM_TEXT, 'Faculté'),
                'promotion' => new external_value(PARAM_TEXT, 'Promotion'),
                'statut'    => new external_value(PARAM_TEXT, 'Statut à l Université'),
                'suspended' => new external_value(PARAM_INT, 'Suspended [no: 0, yes: 1]')
            )
        );
    }

    public static function sync_user(String $firstname, String $lastname, String $username, String $email, String $phone, String $aa, String $matricule, String $faculte, String $promotion, String $statut, int $suspended)
    {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/user/lib.php');
        require_once($CFG->dirroot . '/user/profile/lib.php');

        $user_basic_profile_fields = array(
            'firstname' => $firstname,
            'lastname' => $lastname,
            'username' => $username,
            'email' => $email,
            'phone1' => $phone,
            'suspended' => $suspended
        );
        $user_custom_profile_fields= array(
            'aa' => $aa,
            'matricule' => $matricule,
            'faculte' => $faculte,
            'promotion' => $promotion,
            'statut' => $statut
        );

        // Validate parameters
        $params = self::validate_parameters(self::sync_user_parameters(), array_merge($user_basic_profile_fields, $user_custom_profile_fields));

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

        // Open log file.
        //$enrol->open_log_file();

        // *************************************************************************************************************
        // userdata preprocessing :
        // *************************************************************************************************************
        // Here, additional checks could be implemented to test the integrity of data.
        $user_basic_profile_data = new stdClass();
        foreach ($user_basic_profile_fields as $fieldid => $fieldname) {
            $user_basic_profile_data->$fieldid = trim($params[$fieldid]);
        }
        foreach ($user_custom_profile_fields as $fieldid => $fieldname) {
            $user_custom_profile_data->$fieldid = trim($params[$fieldid]);
        }
        // *************************************************************************************************************
        // database update
        // *************************************************************************************************************
        // First, check if the user exists :
        $username = trim($params['username']);
        $user = $DB->get_record('user', array('username' => $user_basic_profile_data->username));
        if ($user) {
            // update user
            // Ensure the current user is allowed to run this function.
            require_capability('moodle/user:update', $context);

            // basic info
            // ----------
            $userchanged = false;
            profile_load_data($user);
            foreach ($user_basic_profile_fields as $fieldid => $fieldname) {
                if ($user_basic_profile_data->$fieldid !== $user->$fieldid) {
                    //$enrol->log($logentry . "- Update " . $fieldid . " from " . $user->$fieldid . " to " . $user_basic_profile_data->$fieldid);
                    $user->$fieldid = $user_basic_profile_data->$fieldid;
                    $userchanged = true;
                }
            }
            if ($userchanged) {
                user_update_user($user, false, false);
            }

            // Custom info
            // -----------
            $userdatachanged = false;
            foreach ($user_custom_profile_fields as $fieldid => $fieldname) {
                $profilefieldid = 'profile_field_' . $fieldid;
                if ($user->{$profilefieldid} !== $user_custom_profile_data->$fieldid) {
                    $user->{$profilefieldid} = $user_custom_profile_data->$fieldid;
                    $userdatachanged = true;
                }
            }
            if ($userdatachanged) {
                profile_save_data($user);
            }

        }
        else {

            // create user
            // Ensure the current user is allowed to run this function.
            require_capability('moodle/user:create', $context);
            $user = new stdClass();
            $user->auth = $enrol->get_config('auth');
            // Make sure auth is valid.
            if (empty($availableauths[$user->auth])) {
                $errorparams = new stdClass();
                $errorparams->auth = $user->auth;
                throw new moodle_exception('invalidauth', 'enrol_unilu', '', $errorparams);
            }

            foreach ($user_basic_profile_fields as $fieldid => $fieldname) {
                $user->$fieldid = $user_basic_profile_data->$fieldid;
            }
            // create the user
            $user->id = user_create_user($user);

            // fill additional custom fields
            foreach ($user_custom_profile_fields as $fieldid => $fieldname) {
                $profilefieldid = 'profile_field_' . $fieldid;
                $user->{$profilefieldid} = $user_custom_profile_data->{$fieldid};
            }
            profile_save_data($user);
        }
        return 'Success';
    }

    public static function sync_user_returns() {
        return new external_value(PARAM_TEXT, 'Status of the synchronization');
    }

}


