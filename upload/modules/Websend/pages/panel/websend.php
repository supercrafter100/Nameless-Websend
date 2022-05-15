<?php
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr6
 *
 *  License: MIT
 *
 *  Websend configuration page
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

if(isset($_GET['hook'])) {

    // Check if the hook actually exists
	$hook = EventHandler::getEvent($_GET['hook']);

	if(!$hook) {
		Redirect::to(URL::build('/panel/websend'));
		die();
	}

    // Get the available commands that the current hook has
	$db_hook = $queries->getWhere('websend_commands', array('hook', '=', Output::getClean($_GET['hook'])));
	if(count($db_hook))
		$db_hook = $db_hook[0];
	else
		$db_hook = null;

    // If data was submitted & token is valid
	if(Input::exists()) {
		if(Token::check(Input::get('token'))) {

            // Check if the hook is attempted to be enabled or disabled
			if(isset($_POST['enable_hook']) && $_POST['enable_hook'] == 'on')
				$enabled = 1;
			else
				$enabled = 0;

            // Check the submitted commands
			if(isset($_POST['commands']))
				$commands = $_POST['commands'];
			else
				$commands = '';

            // Set the commands in the database
			if(is_null($db_hook)) {
				$queries->create('websend_commands', array(
					'hook' => $_GET['hook'],
					'commands' => $commands,
					'enabled' => $enabled
				));
			} else {
				$queries->update('websend_commands', $db_hook->id, array(
					'commands' => $commands,
					'enabled' => $enabled ?? 0
				));
			}

            // Set the commands we have to execute for the event
            WSHook::setEvent($_GET['hook'], $commands);

            // Save data
            $db_hook = $queries->getWhere('websend_commands', array('hook', '=', Output::getClean($_GET['hook'])));
            $db_hook = $db_hook[0];

		} else
			$errors = array($language->get('general', 'invalid_token'));
	}

	$hooks = array();
	if(count($hook['params'])){
		foreach($hook['params'] as $param => $desc){
			$hooks[Output::getClean($param)] = Output::getClean($desc);
		}
	}

	$smarty->assign(array(
		'HOOK_DESCRIPTION' => Output::getClean($hook['description']),
		'ENABLE_HOOK' => $websend_language->get('language', 'enable_hook'),
		'HOOK_ENABLED' => (!is_null($db_hook) && $db_hook->enabled == 1),
		'COMMANDS_INFO' => $websend_language->get('language', 'commands_information'),
		'HOOKS' => $hooks,
		'COMMANDS' => $websend_language->get('language', 'commands'),
		'COMMANDS_VALUE' => (!is_null($db_hook)) ? Output::getClean($db_hook->commands) : '',
		'INFO' => $language->get('general', 'info'),
		'BACK' => $language->get('general', 'back'),
		'BACK_LINK' => URL::build('/panel/websend'),
	));

	$template->addCSSFiles(array(
		(defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/switchery/switchery.min.css' => array()
	));

    $template->addJSFiles([
        (defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/ckeditor/ckeditor.js' => [],
        (defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/toastr/toastr.min.js' => [],
        (defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/switchery/switchery.min.js' => []
    ]);

	$template->addJSScript('
		var elems = Array.prototype.slice.call(document.querySelectorAll(\'.js-switch\'));

		elems.forEach(function(html) {
		  var switchery = new Switchery(html, {color: \'#23923d\', secondaryColor: \'#e56464\'});
		});
	');

	$template_file = 'websend/websend_hooks_edit_old.tpl';

} else {

    // Send command to console
    if (isset($_POST['command'])) {
        $command = $_POST['command'];
        if(Input::exists() && Token::check(Input::get('token'))) {
            WSDBInteractions::insertPendingCommand(1, $command);
        } else {
            $errors = array($language->get('general', 'invalid_token'));
        }
    }

	// Get hooks
	$hookQuery = $queries->getWhere('websend_commands', array('enabled', '=', 1));

	$hooks = array();
	foreach($hookQuery as $hook){
		$hooks[] = $hook->hook;
	}

	$all_hooks = EventHandler::getEvents();
	$template_hooks = array();

	foreach($all_hooks as $hook => $description){
		$template_hooks[] = array(
			'link' => URL::build('/panel/websend/', 'hook=' . Output::getClean($hook)),
			'description' => Output::getClean($description),
			'enabled' => in_array($hook, $hooks)
		);
	}

    $apiKey = $queries->getWhere('settings', ['name', '=', 'mc_api_key'])[0]->value;

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
}

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