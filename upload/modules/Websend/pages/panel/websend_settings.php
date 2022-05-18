<?php

// Can the user view the panel?
if(!$user->handlePanelPageLoad('admincp.websend.settings')) {
    require_once(ROOT_PATH . '/403.php');
    die();
}

const PAGE = 'Websend';
const PARENT_PAGE = 'Websend';
const PANEL_PAGE = 'panel';
$page_title = $websend_language->get('language', 'websend');
require_once(ROOT_PATH . '/core/templates/backend_init.php');

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

// Deal with input
if (Input::exists()) {
    if (Token::check()) {


        $validation = Validate::check($_POST, [
           'maxConsoleRecords' => [
               Validate::REQUIRED => true,
               Validate::NUMERIC => true
           ],
           'consoleRequestInterval' => [
               Validate::REQUIRED => true,
               Validate::NUMERIC => true
           ],
           'maxDisplayedRecords' => [
                Validate::REQUIRED => true,
                Validate::NUMERIC => true
            ],
        ])->messages([
            'maxConsoleRecords' => $websend_language->get('language', 'missing_maxconsolerecords'),
            'consoleRequestInterval' => $websend_language->get('language', 'missing_consolerequestinterval'),
            'maxDisplayedRecords' => $websend_language->get('language', 'missing_maxdisplayedrecords'),
        ]);

        if ($validation->passed()) {
            // Update settings
            // max console records
            $maxrecords_id = $queries->getWhere('websend_settings', ['name', '=', 'console_max_lines']);
            $maxrecords_id = $maxrecords_id[0]->id;

            $queries->update('websend_settings', $maxrecords_id, [
                'value' => Output::getClean(Input::get('maxConsoleRecords'))
            ]);

            // Update cache
            $cache->setCache('websend_settings');
            $cache->store('console_max_lines', Output::getClean(Input::get('maxConsoleRecords')));

            // console request interval
            $console_interval_id = $queries->getWhere('websend_settings', ['name', '=', 'console_request_interval']);
            $console_interval_id = $console_interval_id[0]->id;

            $queries->update('websend_settings', $console_interval_id, [
                'value' => Output::getClean(Input::get('consoleRequestInterval'))
            ]);

            // Update cache
            $cache->store('console_request_interval', Output::getClean(Input::get('consoleRequestInterval')));

            // max displayed records
            $maxdisplayed_id = $queries->getWhere('websend_settings', ['name', '=', 'max_displayed_records']);
            $maxdisplayed_id = $maxdisplayed_id[0]->id;

            $queries->update('websend_settings', $maxdisplayed_id, [
                'value' => Output::getClean(Input::get('maxDisplayedRecords'))
            ]);

            // Update cache
            $cache->store('max_displayed_records', Output::getClean(Input::get('maxDisplayedRecords')));

            // Flash success
            Session::flash('general_language', $websend_language->get('language', 'settings_updated_successfully'));

        } else {
            $errors = $validation->errors();
        }
    } else {
        // Invalid token
        $errors = [$language->get('general', 'invalid_token')];
    }
}


// Get all the settings
$console_max_lines = $queries->getWhere('websend_settings', ['name', '=', 'console_max_lines']);
$console_max_lines = $console_max_lines[0]->value;

$console_request_interval = $queries->getWhere('websend_settings', ['name', '=', 'console_request_interval']);
$console_request_interval = $console_request_interval[0]->value;

$max_displayed_records = $queries->getWhere('websend_settings', ['name', '=', 'max_displayed_records']);
$max_displayed_records = $max_displayed_records[0]->value;

if (Session::exists('general_language'))
    $success = Session::flash('general_language');

// General smarty stuff
if (isset($success)) {
    $smarty->assign([
        'SUCCESS_TITLE' => $language->get('general', 'success'),
        'SUCCESS' => $success
    ]);
}

if (isset($errors) && count($errors)) {
    $smarty->assign([
        'ERRORS_TITLE' => $language->get('general', 'error'),
        'ERRORS' => $errors
    ]);
}

$smarty->assign([
    'PARENT_PAGE' => PARENT_PAGE,
    'DASHBOARD' => $language->get('admin', 'dashboard'),
    'WEBSEND' => $websend_language->get('language', 'websend'),
    'PAGE' => PANEL_PAGE,
    'TOKEN' => Token::get(),
    'SUBMIT' => $language->get('general', 'submit')
]);

$smarty->assign([
    'TERM_CONSOLE_MAX_LINES' => $websend_language->get('language', 'settings_console_max_lines'),
    'TERM_CONSOLE_REQUEST_INTERVAL' => $websend_language->get('language', 'settings_console_request_interval'),
    'TERM_MAX_DISPLAYED_RECORDS' => $websend_language->get('language', 'settings_max_displayed_records'),
    'SETTINGS_CONSOLE_MAX_LINES' => $console_max_lines,
    'SETTINGS_CONSOLE_REQUEST_INTERVAL' => $console_request_interval,
    'SETTINGS_MAX_DISPLAYED_RECORDS' => $max_displayed_records
]);

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

// Display template
$template->onPageLoad();
require(ROOT_PATH . '/core/templates/panel_navbar.php');

$template_file = 'websend/websend_settings.tpl';
$template->displayTemplate($template_file, $smarty);
