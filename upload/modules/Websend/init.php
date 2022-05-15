<?php
/*
 *	Made by Samerton
 *  https://github.com/samerton
 *  NamelessMC version 2.0.0-pr6
 *
 *  License: MIT
 *
 *  Websend initialisation file
 */

// Language
$websend_language = new Language(ROOT_PATH . '/modules/Websend/language');

// Load classes
spl_autoload_register(function ($class) {
    $path = join(DIRECTORY_SEPARATOR, array(ROOT_PATH, 'modules', 'Websend', 'classes', $class . '.php'));
    if (file_exists($path)) {
        require_once($path);
    }
});

require_once(ROOT_PATH . '/modules/Websend/module.php');
$module = new Websend_Module($pages, $language, $websend_language, $queries, $cache, $endpoints);