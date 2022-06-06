<?php
/*
 *	Originally made by Samerton (https://github.com/samerton)
 *  Fork by Supercrafter100 (https://github.com/supercrafter100)
 *
 *  NamelessMC version 2.0.0-pr13
 *
 *  License: MIT
 *
 *  Websend hooks list page
 */

// Can the user view the panel?
if(!$user->handlePanelPageLoad('admincp.websend.events')) {
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

$server_id = $_GET['id'];
if (!isset($server_id) || !is_numeric($server_id)) {
    Redirect::to(URL::build('/panel/websend/servers&to=/panel/websend/hooks'));
}
$server_id = intval($server_id);

// Get all available hooks
$hooks = EventHandler::getEvents();

// Get all enabled hooks
$enabled_hooks = DB::getInstance()->query("SELECT * FROM nl2_websend_commands WHERE enabled = 1 AND server_id = ?", [$server_id])->results();
$enabled_hooks = array_map(fn($item) => $item->hook, $enabled_hooks);

// Make proper object that can be read by the website afterwards
$mapped_hooks = [];
foreach($hooks as $hook => $description) {
    $mapped_hooks[] = [
        'link' => URL::build('/panel/websend/hooks/edit', 'hook=' . Output::getClean($hook) . '&id=' . $server_id),
        'description' => Output::getClean($description),
        'enabled' => in_array($hook, $enabled_hooks)
    ];
}

//
// Assign all variables in smarty
//

// Page specific
$smarty->assign([
    'AVAILABLE_HOOKS' => $websend_language->get('language', 'available_hooks'),
    'ENABLED' => $websend_language->get('language', 'enabled'),
    'DISABLED' => $websend_language->get('language', 'disabled'),
    'HOOK' => $websend_language->get('language', 'hook'),
    'STATUS' => $websend_language->get('language', 'status'),
    'HOOKS' => $mapped_hooks,
    'BACK' => $language->get('general', 'back'),
    'BACK_LINK' => URL::build('/panel/websend/servers'),
]);

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
]);

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

// Display template
$template->onPageLoad();
require(ROOT_PATH . '/core/templates/panel_navbar.php');

$template_file = 'websend/websend_hooks.tpl';
$template->displayTemplate($template_file, $smarty);


