<?php

// Can the user view the panel?
if(!$user->handlePanelPageLoad('admincp.websend')) {
    require_once(ROOT_PATH . '/403.php');
    die();
}


$server_id = $_GET['server_id'];
if (!is_numeric($server_id)) {
    die('Invalid server id');
}

// Save the output to cache so we can retrieve it again later
$lines = WSDBInteractions::getConsoleOutput($server_id);

// Return the response
die(json_encode(['content' => $lines]));