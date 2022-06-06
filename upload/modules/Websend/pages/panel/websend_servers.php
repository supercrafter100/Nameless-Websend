<?php
/*
 *	Originally made by Samerton (https://github.com/samerton)
 *  Fork by Supercrafter100 (https://github.com/supercrafter100)
 *
 *  NamelessMC version 2.0.0-pr13
 *
 *  License: MIT
 *
 *  Websend servers page
 */

// Can the user view the panel?
if (!$user->handlePanelPageLoad("admincp.websend.events")) {
    require_once(ROOT_PATH . '/403.php');
    die();
}

const PAGE = 'Websend';
const PARENT_PAGE = "Websend";
const PANEL_PAGE = "panel";
$page_title = $websend_language->get('language', 'websend');
require_once(ROOT_PATH . '/core/templates/backend_init.php');

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

// Get all available servers
$servers = DB::getInstance()->orderAll('mc_servers', '`order`', 'ASC')->results();
$default = 0;
$template_array = [];

if (count($servers)) {
    foreach ($servers as $server) {
        if ($server->is_default == 1) {
            $default = $server->id;
        }

        $template_array[] = [
            'name' => Output::getClean($server->name),
            'id' => Output::getClean($server->id),
            'server_id' => $language->get('admin', 'server_id_x', ['serverId' => Output::getClean($server->id)]),
            'edit_link' => URL::build('/panel/websend/hooks/', 'id=' . urlencode($server->id)),
            'is_default' => $server->is_default
        ];
    }
} else {
    $smarty->assign('NO_SERVERS', $language->get('admin', 'no_servers_defined'));
}

// Page specific
$smarty->assign([
    'SERVERS' => $template_array,
    'VIEW' => $language->get('general', 'view')
]);

// Statistics & general values
$smarty->assign([
    'PARENT_PAGE' => PARENT_PAGE,
    'DASHBOARD' => $language->get('admin', 'dashboard'),
    'WEBSEND' => $websend_language->get('language', 'websend'),
    'PAGE' => PANEL_PAGE,
    'INFO' => $websend_language->get('language', 'servers-list')
]);

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

// Display template
$template->onPageLoad();
require(ROOT_PATH . '/core/templates/panel_navbar.php');

$template_file = 'websend/websend_servers.tpl';
$template->displayTemplate($template_file, $smarty);