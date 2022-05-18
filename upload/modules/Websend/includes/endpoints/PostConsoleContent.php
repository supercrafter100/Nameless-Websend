<?php
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
            $api->throwError(6, 'Invalid server_id');
            return;
        }

        // Save everything in the database
        $lines = $_POST['content'];
        foreach ($lines as $line) {
            WSDBInteractions::insertConsoleLine($server_id, $line);
        }

        // If database size is too long, delete the oldest lines with the lowest id
        WSDBInteractions::deleteOversizedConsoleLines();

        // Return the response
        $api->returnArray(array('success' => true));
    }
}