<?php
defined('MOODLE_INTERNAL') || die();

require_once('adminlib.php');

$hidden = $settings->hidden;

$settings = new admin_category('enroluniluroot', get_string('pluginname', 'enrol_unilu'), $hidden);


if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_heading('enrol_unilu_settings', '', get_string('pluginname_descr', 'enrol_unilu')));

    //--- course settings -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('enrol_unilu_coursesettings', get_string('course_settings', 'enrol_unilu'), ''));
    $settingspage->add(new admin_setting_configtext('enrol_unilu/academicyear', get_string('academicyear', 'enrol_unilu'),
        get_string('academicyearhelp', 'enrol_unilu'), '', '/^[0-9]{6}$/'));
}
