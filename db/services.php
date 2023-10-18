<?php

// WebService for UNILU Enrolments
// Christophe Mertens (ULB) : christophe.mertens@ulb.be

// Web service functions to install.
$functions = array(
    'enrol_unilu_sync_course' => array(
        'classname'   => 'enrol_unilu_external',
        'methodname'  => 'sync_course',
        'classpath'   => 'enrol/unilu/externallib.php',
        'description' => 'Create course',
        'type'        => 'write'
    ),
    'enrol_unilu_sync_user' => array(
        'classname'   => 'enrol_unilu_external',
        'methodname'  => 'sync_user',
        'classpath'   => 'enrol/unilu/externallib.php',
        'description' => 'Synchronize a user',
        'type'        => 'write'
    ),
    'enrol_unilu_sync_enrolment' => array(
        'classname'   => 'enrol_unilu_external',
        'methodname'  => 'sync_enrolment',
        'classpath'   => 'enrol/unilu/externallib.php',
        'description' => 'Enrolment into course',
        'type'        => 'write'
    )
);

// Services to install as pre-build services. A pre-build service is not editable by administrator. This step is optional.
$services = array(
    'UNILU' => array(
        'shortname' => 'unilu',
        'functions' => array('enrol_unilu_sync_course', 'enrol_unilu_sync_user', 'enrol_unilu_sync_enrolment'),
        'requiredcapability' => '',
        'restrictedusers' => 1,
        'enabled' => 1
    )
);
