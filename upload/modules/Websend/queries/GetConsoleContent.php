<?php

/*
 *	Originally made by Samerton (https://github.com/samerton)
 *  Fork by Supercrafter100 (https://github.com/supercrafter100)
 *
 *  NamelessMC version 2.0.0-pr13
 *
 *  License: MIT
 *
 *  Query file for getting latest console content
 */

// Can the user view the panel?
if(!$user->handlePanelPageLoad('admincp.websend')) {
    require_once(ROOT_PATH . '/403.php');
    die();
}


$server_id = $_GET['id'];
if (!is_numeric($server_id)) {
    die('Invalid server id');
}

// Save the output to cache so we can retrieve it again later
$lines = WSDBInteractions::getConsoleOutput(intval($server_id));

// Return the response
die(json_encode(['content' => $lines]));