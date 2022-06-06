<?php
/*
 *	Originally made by Samerton (https://github.com/samerton)
 *  Fork by Supercrafter100 (https://github.com/supercrafter100)
 *
 *  NamelessMC version 2.0.0-pr13
 *
 *  License: MIT
 *
 *  Api route for posting console content about a server
 */

class PostConsoleContent extends KeyAuthEndpoint {

    public function __construct() {
        $this->_route='websend/console';
        $this->_module='Websend';
        $this->_description='Post console content to the plugin';
        $this->_method = 'POST';
    }

    public function execute(Nameless2API $api)
    {
        $server_id = $_POST['server_id'];
        if (!is_numeric($server_id)) {
            $api->throwError(WebsendApiErrors::ERROR_INVALID_SERVER_ID, 'Invalid server_id');
            return;
        }

        // Check if the database has to be cleared due to a server startup
        if (isset($_POST['clear_previous']) && $_POST['clear_previous'] == true) {
            DB::getInstance()->query('DELETE FROM nl2_websend_console_output WHERE server_id = ?', [$server_id]);
        }

        // Save everything in the database
        $lines = $_POST['content'];
        foreach ($lines as $line) {
            WSDBInteractions::insertConsoleLine($server_id, $line);
        }

        // Return the response
        $api->returnArray(['success' => true]);
    }
}