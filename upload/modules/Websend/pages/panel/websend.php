<?php
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr6
 *
 *  License: MIT
 *
 *  Websend home page
 */

// Can the user view the panel?
if(!$user->handlePanelPageLoad('admincp.websend.console')) {
    require_once(ROOT_PATH . '/403.php');
    die();
}

const PAGE = 'panel';
const PARENT_PAGE = 'websend';
const PANEL_PAGE = 'websend';
$page_title = $websend_language->get('language', 'websend');
require_once(ROOT_PATH . '/core/templates/backend_init.php');

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

// Send command to console
if (isset($_POST['command'])) {
    $command = $_POST['command'];
    if(Input::exists() && Token::check(Input::get('token'))) {
        WSDBInteractions::insertPendingCommand(1, $command);
    } else {
        $errors = array($language->get('general', 'invalid_token'));
    }
}

$template->addCSSStyle('
    .terminal p { padding-bottom: 0; margin-bottom: 0; }
    .terminal-logs, .terminal-input { font-size: 0.8em; font-family: monospace; }
    .terminal-logs { white-space: pre-wrap; overflow-y: scroll; overflow-x: hidden; height: 100%; }
    .terminal-input-field { display: flex; width: 100%; padding: 0px 20px 0px 3px; }
    .terminal-input-form { width: 100%; background-color: inherit; font-size: 1em; color: inherit; border-width: 0px; outline: none; }
    .terminal-input-send { width: 13.17px; height: 13.17px; transform: scale(2.8); display: inline-block; transition: transform 150ms ease; }
    .terminal-input-send:hover { transform: scale(3.4); }
    .terminal-input-send:active { transform: scale(3); }
    .terminal-input-send:focus { outline: none; }
    .terminal-input-send i, .terminal-input-send i:hover { transform: scale(0.5); width: 100%; height: 100%; display: inline-block; }
    .status-online { background-color: #4CAF50; }
    .status-offline { background-color: #F44336; }
    .card-header h5 { margin: 0px; background-color: inherit; }
    .circle { height: 8px; width: 8px; transform: translateY(-3px); border-radius: 50%; display: inline-block; }
    span.terminal-status { text-align: right; }
    .terminal { height: calc(100vh - 200px); }
    
    .terminal-element.card-header { background-color: #eeeeee }
    .terminal { background-color: #eeeeee; color: black; } 
    html.dark .terminal { background-color: #161c25 !important; color: white }
    html.dark .terminal-element.card-header { background-color: #161c25 !important; }
');

$cache->setCache('websend_settings');
$interval = $cache->retrieve('console_request_interval');

$smarty->assign(array(
    'AVAILABLE_HOOKS' => $websend_language->get('language', 'available_hooks'),
    'ENABLED' => $websend_language->get('language', 'enabled'),
    'DISABLED' => $websend_language->get('language', 'disabled'),
    'HOOK' => $websend_language->get('language', 'hook'),
    'STATUS' => $websend_language->get('language', 'status'),
    'TOASTR_SENT' => $websend_language->get('language', 'toastr_sent'),
    'HOOKS' => $template_hooks,
    'CONSOLE_URL' => '/queries/console&server_id=1',
    'REQUEST_INTERVAL' => $interval ?? 5,
));

$template_file = 'websend/websend.tpl';

if(isset($success))
	$smarty->assign(array(
		'SUCCESS' => $success,
		'SUCCESS_TITLE' => $language->get('general', 'success')
	));

if(isset($errors) && count($errors))
	$smarty->assign(array(
		'ERRORS' => $errors,
		'ERRORS_TITLE' => $language->get('general', 'error')
	));

$smarty->assign(array(
	'PARENT_PAGE' => PARENT_PAGE,
	'DASHBOARD' => $language->get('admin', 'dashboard'),
	'WEBSEND' => $websend_language->get('language', 'websend'),
	'PAGE' => PANEL_PAGE,
	'TOKEN' => Token::get(),
	'SUBMIT' => $language->get('general', 'submit')
));

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate($template_file, $smarty);