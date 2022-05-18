<?php
/*
 *	Made by Samerton
 *  https://github.com/samerton
 *  NamelessMC version 2.0.0-pr6
 *
 *  License: MIT
 *
 *  Websend module for NamelessMC
 */

class Websend_Module extends Module {
	private $_language, $_websend_language, $_queries, $_cache, $_endpoints;

	public function __construct($pages, $language, $websend_language, $queries, $cache, $endpoints){
		$this->_language = $language;
		$this->_websend_language = $websend_language;
		$this->_queries = $queries;
		$this->_cache = $cache;
        $this->_endpoints = $endpoints;

		$name = 'Websend';
		$author = '<a href="https://samerton.me" target="_blank" rel="nofollow noopener">Samerton</a>';
		$module_version = '1.1.2';
		$nameless_version = '2.0.0-pr13';

		parent::__construct($this, $name, $author, $module_version, $nameless_version);

		// Define URLs which belong to this module
		$pages->add('Websend', '/panel/websend', 'pages/panel/websend.php');
        $pages->add('Websend', '/panel/websend/servers', 'pages/panel/websend_servers.php');
        $pages->add('Websend', '/panel/websend/hooks', 'pages/panel/websend_hooks.php');
        $pages->add('Websend', '/panel/websend/hooks/edit', 'pages/panel/websend_hooks_edit.php');
        $pages->add('Websend', '/panel/websend/settings', 'pages/panel/websend_settings.php');
        $pages->add('Websend', '/queries/console', 'queries/GetConsoleContent.php');
        $pages->add('Websend', '/queries/console/clear', 'queries/ClearConsole.php');

        // Loading API endpoins
        $endpoints->loadEndpoints(join(DIRECTORY_SEPARATOR, array(ROOT_PATH, 'modules', 'Websend', 'includes', 'endpoints')), $endpoints);

        // Loading events
        $this->_cache->setCache('websend_module');
        if ($this->_cache->retrieve('installed')) {
            WSHook::registerEvents();
        };
	}

	public function onInstall() {

		try {
			$engine = Config::get('mysql/engine');
			$charset = Config::get('mysql/charset');
		} catch(Exception $e){
			$engine = 'InnoDB';
			$charset = 'utf8mb4';
		}

        if(!$engine || is_array($engine))
            $engine = 'InnoDB';

        if(!$charset || is_array($charset))
            $charset = 'latin1';

        $queries = new Queries();

        if (!$queries->tableExists('websend_commands')) {
            try {
                $queries->createTable('websend_commands', ' `id` int(11) NOT NULL AUTO_INCREMENT, `hook` varchar(64) NOT NULL, `server_id` int(11) NOT NULL, `commands` mediumtext NOT NULL, `enabled` tinyint(1) NOT NULL DEFAULT \'0\', PRIMARY KEY (`id`)', "ENGINE=$engine DEFAULT CHARSET=$charset");
            } catch (Exception $e) {
                // Error
            }
        }

        if (!$queries->tableExists('websend_pending_commands')) {
            try {
                $queries->createTable('websend_pending_commands', '`id` int(11) NOT NULL AUTO_INCREMENT, `server_id` int(11) NOT NULL, `command` varchar(2048) NOT NULL, `status` tinyint(1) NOT NULL DEFAULT \'0\', PRIMARY KEY (`id`)', "ENGINE=$engine DEFAULT CHARSET=$charset");
            } catch (Exception $e) {
                // Error
            }
        }

        if (!$queries->tableExists('websend_console_output')) {
            try {
                $queries->createTable('websend_console_output', '`id` int(11) NOT NULL AUTO_INCREMENT, `server_id` int(11) NOT NULL, `content` text, PRIMARY KEY (`id`)', "ENGINE=$engine DEFAULT CHARSET=$charset");
                DB::getInstance()->query('CREATE INDEX idx_websend_console_output_server_id ON nl2_websend_console_output (server_id)');
            } catch (Exception $e) {
                // Error
            }
        }

        if (!$queries->tableExists('websend_settings')) {
            try {
                $queries->createTable('websend_settings', ' `id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(64) NOT NULL, `value` text, PRIMARY KEY (`id`)', "ENGINE=$engine DEFAULT CHARSET=$charset");
            } catch (Exception $e) {
                // Error
            }
        }

        $this->_cache->setCache('websend_settings');

        $queries->create('websend_settings', [
            'name' => 'console_max_lines',
            'value' => 500
        ]);
        $this->_cache->store('console_max_lines', 500);

        $queries->create('websend_settings', [
            'name' => 'console_request_interval',
            'value' => 5
        ]);
        $this->_cache->store('console_request_interval', 5);

        $queries->create('websend_settings', [
            'name' => 'max_displayed_records',
            'value' => 200
        ]);
        $this->_cache->store('max_displayed_records', 200);

        try {
            // Update main admin group permissions
            $admin_permissions = $queries->getWhere('groups', array('id', '=', 2));
            $admin_permissions = $admin_permissions[0]->permissions;

            $admin_permissions = json_decode($admin_permissions, true);
            $admin_permissions['admincp.websend.console'] = 1;
            $admin_permissions['admincp.websend.events'] = 1;
            $admin_permissions['admincp.websend.settings'] = 1;

            $admin_permissions_updated = json_encode($admin_permissions);

            $queries->update('groups', 2, array(
                'permissions' => $admin_permissions_updated
            ));
        } catch (Exception $e) {
            // Error
        }

        // Set installed
        $this->_cache->setCache('websend_module');
        $this->_cache->store('installed', true);
	}

	public function onUninstall() {
		// Not implemented yet
	}

	public function onEnable() {
		// Not necessary
	}

	public function onDisable() {
		// Not necessary
	}

	public function onPageLoad(User $user, Pages $pages, Cache $cache, Smarty $smarty, $navs, Widgets $widgets, ?TemplateBase $template) {

		// Permissions
		PermissionHandler::registerPermissions('Websend', [
            'admincp.websend.console' => $this->_language->get('moderator', 'staff_cp') . ' &raquo; ' . $this->_websend_language->get('language', 'websend_console'),
			'admincp.websend.events' => $this->_language->get('moderator', 'staff_cp') . ' &raquo; ' . $this->_websend_language->get('language', 'websend_events'),
            'admincp.websend.settings' => $this->_language->get('moderator', 'staff_cp') . ' &raquo; ' . $this->_websend_language->get('language', 'websend_settings'),
		]);

		if(defined('BACK_END')){

            if (
                $user->hasPermission('admincp.websend.console') ||
                $user->hasPermission('admincp.websend.events') ||
                $user->hasPermission('admincp.websend.settings')
            ) {

                // Set the order of the navigation
                $cache->setCache('panel_sidebar');
                $order = $cache->isCached('websend_order') ? $cache->retrieve('websend_order') : 51;
                $cache->store('websend_order', $order);

                // Divider
                $navs[2]->add('websend_divider', mb_strtoupper($this->_websend_language->get('language', 'websend')), 'divider', 'top', null, $order, '');

                // Add the navigation links
                if ($user->hasPermission('admincp.websend.console')) {
                    $main_icon = $cache->isCached('websend_icon_main') ? $cache->retrieve('websend_icon_main') : '<i class="nav-icon fas fa-terminal"></i>';
                    $cache->store('websend_icon_main', $main_icon);
                    $navs[2]->add('websend_main', $this->_websend_language->get('language', 'websend_console'), URL::build('/panel/websend'), 'top', null, ($order + 0.1), $main_icon);
                }

                if ($user->hasPermission('admincp.websend.events')) {
                    $hooks_icon = $cache->isCached('websend_icon_hooks') ? $cache->retrieve('websend_icon_hooks') : '<i class="nav-icon fas fa-bell"></i>';
                    $cache->store('websend_icon_hooks', $hooks_icon);
                    $navs[2]->add('websend_hooks', $this->_websend_language->get('language', 'websend_events'), URL::build('/panel/websend/servers'), 'top', null, ($order + 0.2), $hooks_icon);
                }

                if ($user->hasPermission('admincp.websend.settings')) {
                    $settings_icon = $cache->isCached('websend_icon_settings') ? $cache->retrieve('websend_icon_settings') : '<i class="nav-icon fas fa-cogs"></i>';
                    $cache->store('websend_icon_settings', $settings_icon);
                    $navs[2]->add('websend_settings', $this->_websend_language->get('language', 'websend_settings'), URL::build('/panel/websend/settings'), 'top', null, ($order + 0.3), $settings_icon);
                }
            }
		}
	}

    public function getDebugInfo(): array {
        return [];
    }
}