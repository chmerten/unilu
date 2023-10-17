<?php
defined('MOODLE_INTERNAL') || die();


if ($ADMIN->fulltree) {

    $settings = new admin_settingpage('enrol_unilu', get_string('pluginname', 'enrol_unilu'));

    $settings->add(new admin_setting_configtext(
        'enrol_unilu/academicyear',
        get_string('academicyear', 'enrol_unilu'),
        get_string('academicyearhelp', 'enrol_unilu'),
        '',
        '/^[0-9]{6}$/'
    ));
}

