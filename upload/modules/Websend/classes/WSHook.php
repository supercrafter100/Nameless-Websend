<?php
/*
 *	Originally made by Samerton (https://github.com/samerton)
 *  Fork by Supercrafter100 (https://github.com/supercrafter100)
 *
 *  NamelessMC version 2.0.0-pr13
 *
 *  License: MIT
 *
 *  Websend hook class which handles receiving events
 */

class WSHook {

	private static $events = [];
    private static $enabled = [];

    public static function setEvent(string $event, int $server, string $commands) {
        self::$events[$event][$server] = explode('\n', $commands);
    }

    public static function registerEvents() {

        $results = DB::getInstance()->query('SELECT * FROM nl2_websend_commands WHERE enabled = 1')->results();

        foreach ($results as $event) {
            if (!isset(self::$events[$event->hook])) {
                EventHandler::registerListener($event->hook, 'WSHook::execute');
                self::$events[$event->hook] = [];
            }

            if (!isset(self::$events[$event->hook][$event->server_id])) {
                self::setEvent($event->hook, $event->server_id, $event->commands);
            }
        }
    }

    public static function setEnabled(string $event, int $server, bool $enabled) {
        self::$enabled[$event][$server] = $enabled;
    }

	public static function execute($params = array()) : bool {
		if(!isset($params['event'])){
			return false;
		}

        // Check if we know anything about the event
        if (!array_key_exists($params['event'], self::$events)) {
            return false;
        }


        $event = self::$events[$params['event']];

        if(count($event)){
            $event_params = EventHandler::getEvent($params['event']);
            $event_params = $event_params['params'];

            // Replacing the placeholders
            $event_param_keys = array();
            $event_param_values = array();
            if(count($event_params)){
                foreach($event_params as $key => $event_param){
                    $event_param_keys[] = '{' . $key . '}';
                    $event_param_values[] = $params[$key];
                }
            }

            // Adding the commands to the database so the plugin can execute them
            foreach($event as $server_id => $commands){

                // Check if the event is enabled
                if (!self::$enabled[$params['event'][$server_id]] == false) {
                    continue;
                }

                foreach ($commands as $command) {
                    $cmd = str_ireplace($event_param_keys, $event_param_values, $command);
                    WSDBInteractions::insertPendingCommand($server_id, $cmd);
                }
            }
            return true;
        }


		return false;
	}
}