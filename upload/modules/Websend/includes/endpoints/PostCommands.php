<?php
class PostCommands extends KeyAuthEndpoint {

    public function __construct() {
        $this->_route='websend/markCommandsExecuted';
        $this->_module='Websend';
        $this->_description='Mark commands as complete';
        $this->_method = 'POST';
    }

    public function execute(Nameless2API $api)
    {
        $query = 'UPDATE `nl2_websend_pending_commands` SET `status`=1';
        $where = ' WHERE';

        if (isset($_POST['server_id']) && is_numeric($_POST['server_id'])) {
            $where .= ' server_id = ' . $_POST['server_id'];
        }

        if (isset($_POST['command_ids'])) {
            $where .= ' AND id IN ( ';
            foreach ($_POST['command_ids'] as $id) {
                if (is_numeric($id)) {
                    $where .= $id;
                    if ($id != end($_POST['command_ids'])) {
                        $where .= ', ';
                    }
                }
            }
            $where .= ' )';
        }

        // Run the query
        $api->getDb()->createQuery($query . $where);

        // Return the response
        $api->returnArray(array('success' => true));
    }
}