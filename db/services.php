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
    )
);

// Services to install as pre-build services. A pre-build service is not editable by administrator. This step is optional.
$services = array(
    'UNILU' => array(
        'shortname' => 'unilu',
        'functions' => array('enrol_unilu_sync_course'),
        'requiredcapability' => '',
        'restrictedusers' => 1,
        'enabled' => 1
    )
);
