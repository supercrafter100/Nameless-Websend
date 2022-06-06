<?php
/*
 *	Originally made by Samerton (https://github.com/samerton)
 *  Fork by Supercrafter100 (https://github.com/supercrafter100)
 *
 *  NamelessMC version 2.0.0-pr13
 *
 *  License: MIT
 *
 *  Websend editing of hooks page
 */

// Can the user view the panel?
if (!$user->handlePanelPageLoad('admincp.websend.events')) {
    require_once(ROOT_PATH . '/403.php');
    die();
}

const PAGE = 'panel';
const PARENT_PAGE = 'websend';
const PANEL_PAGE = 'websend/hooks';
$page_title = $websend_language->get('language', 'websend');
require_once(ROOT_PATH . '/core/templates/backend_init.php');

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

$server_id = $_GET['id'];
if (!isset($server_id) || !is_numeric($server_id)) {
    Redirect::to(URL::build('/panel/websend/servers&to=/panel/websend/hooks'));
}
$server_id = intval($server_id);

// If no hook is present in the URL, redirect to the hooks page
if (!isset($_GET['hook']) || sizeof(EventHandler::getEvent($_GET['hook'])) == 0) {
    Redirect::to(URL::build('/panel/websend/hooks'));
}

// Get the hook data
$hook = EventHandler::getEvent($_GET['hook']);

$db_hook = DB::getInstance()->query("SELECT * FROM nl2_websend_commands WHERE hook = ? AND server_id = ?", [$_GET['hook'], $server_id])->first();


// Check if data got posted to the website
if (Input::exists() && Token::check(Input::get('token'))) {

    $enabled = $_POST['enable_hook'];
    $commands = [];

    $commandId = 1;
    while (true) {
        if (isset($_POST['cmd' . $commandId])) {
            $commands[] = $_POST['cmd' . $commandId];
            $commandId++;
        } else {
            break;
        }
    }
    $commands = implode('\n', $commands);

    // Update the data in the database
    if (is_null($db_hook)) {
        DB::getInstance()->insert('websend_commands', array(
            'hook' => $_GET['hook'],
            'commands' => $commands,
            'enabled' => $enabled,
            'server_id' => $server_id
        ));
    } else {
        DB::getInstance()->query("UPDATE nl2_websend_commands SET commands = ?, enabled = ? WHERE hook = ? AND server_id = ?", [$commands, $enabled, $_GET['hook'], $server_id]);
    }

    // Update the data in the hook handler
    WSHook::setEvent($_GET['hook'], $server_id, $commands);
    WSHook::setEnabled($_GET['hook'], $server_id, boolval($enabled));

    // Update the db_hook variable with the latest data
    $db_hook = DB::getInstance()->query("SELECT * FROM nl2_websend_commands WHERE hook = ? AND server_id = ?", [$_GET['hook'], $server_id])->first();
    $success = $websend_language->get('language', 'hook_updated_successfully');
}

$hook_data = [];
if (count($hook['params'])) {
    foreach($hook['params'] as $param => $desc) {
        $hook_data[Output::getClean($param)] = Output::getClean($desc);
    }
}

// Page specific
$smarty->assign(array(
    'HOOK_NAME' => Output::getClean($_GET['hook']),
    'HOOK_DESCRIPTION' => Output::getClean($hook['description']),
    'ENABLE_HOOK' => $websend_language->get('language', 'enable_hook'),
    'HOOK_ENABLED' => (!is_null($db_hook) && $db_hook->enabled == 1),
    'COMMANDS_INFO' => $websend_language->get('language', 'commands_information'),
    'HOOKS' => $hook_data,
    'COMMANDS' => $websend_language->get('language', 'commands'),
    'COMMANDS_VALUE' => (!is_null($db_hook)) ? Output::getClean($db_hook->commands) : '',
    'INFO' => $language->get('general', 'info'),
    'BACK' => $language->get('general', 'back'),
    'BACK_LINK' => URL::build('/panel/websend/hooks', '&id=' . $server_id),
    'PHP_EOL' => '\n',
));

// Success
if (isset($success)) {
    $smarty->assign([
        'SUCCESS' => $success,
        'SUCCESS_TITLE' => $language->get('general', 'success')
    ]);
}

// Error
if(isset($errors) && count($errors)) {
    $smarty->assign([
        'ERRORS' => $errors,
        'ERRORS_TITLE' => $language->get('general', 'error')
    ]);
}

// Statistics & general values
$smarty->assign([
    'PARENT_PAGE' => PARENT_PAGE,
    'DASHBOARD' => $language->get('admin', 'dashboard'),
    'WEBSEND' => $websend_language->get('language', 'websend'),
    'PAGE' => PANEL_PAGE,
    'SUBMIT' => $language->get('general', 'submit'),
    'TOKEN' => Token::get(),
]);

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

// Display template
$template->onPageLoad();
require(ROOT_PATH . '/core/templates/panel_navbar.php');

$template_file = 'websend/websend_hooks_edit.tpl';
$template->displayTemplate($template_file, $smarty);