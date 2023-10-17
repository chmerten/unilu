<?php
// WebService for UNILU Enrolments
// Christophe Mertens (ULB) : christophe.mertens@ulb.be
defined('MOODLE_INTERNAL') || die();

$capabilities = [

    // Enrol anybody.
    'enrol/unilu:enrol' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => [
            'manager' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
        ],
    ],

    // Manage enrolments of users.
    'enrol/unilu:manage' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => [
            'manager' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
        ],
    ],

    // Unenrol anybody (including self) - watch out for data loss.
    'enrol/unilu:unenrol' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => [
            'manager' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
        ],
    ],

    // Unenrol self - watch out for data loss.
    'enrol/unilu:unenrolself' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => [],
    ],
];