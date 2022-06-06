<?php
/*
 *	Originally made by Samerton (https://github.com/samerton)
 *  Fork by Supercrafter100 (https://github.com/supercrafter100)
 *
 *  NamelessMC version 2.0.0-pr13
 *
 *  License: MIT
 *
 *  Api route for requesting pending commands of a server
 */

class GetCommands extends KeyAuthEndpoint {

    public function __construct() {
        $this->_route = 'websend/commands';
        $this->_module = 'Websend';
        $this->_description = 'Get pending commands';
        $this->_method = 'GET';
    }

    public function execute(Nameless2API $api)
    {
        $dbCommands = WSDBInteractions::getPendingCommands($_GET['server_id']);

        $commands_array = [];
        foreach($dbCommands as $command) {
            $commands_array[] = array(
                'id' => $command->id,
                'command' => $command->command,
            );
        }

        // Mark the commands as processed
        $api->getDb()->query('UPDATE nl2_websend_pending_commands SET status = 1 WHERE server_id = ?', [$_GET['server_id']]);
        $api->returnArray(['commands' => $commands_array]);
    }
}