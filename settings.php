<?php
defined('MOODLE_INTERNAL') || die();


if ($ADMIN->fulltree) {

    $settings = new admin_settingpage('enrol_unilu', get_string('pluginname', 'enrol_unilu'));
    // Authentication method
    $plugins = \core\plugininfo\auth::get_enabled_plugins();
    $options = array();
    foreach ($plugins as $plugin) {
        $options[$plugin] = get_string('pluginname', 'auth_' . $plugin);
    }
    $settings->add(new admin_setting_configselect(
        'enrol_unilu/auth',
        get_string('authmethod', 'enrol_unilu'),
        get_string('authmethodhelp', 'enrol_unilu')
        , 'manual',
        $options));

    // Academic year
    $settings->add(new admin_setting_configtext(
        'enrol_unilu/academicyear',
        get_string('academicyear', 'enrol_unilu'),
        get_string('academicyearhelp', 'enrol_unilu'),
        '',
        '/^[0-9]{6}$/'
    ));
}

