<?php
/*
 *	Made by Samerton
 *  https://github.com/samerton
 *  NamelessMC version 2.0.0-pr6
 *
 *  License: MIT
 *
 *  Websend hook class
 */

class WSHook {

	private static $events = array();
    private static $enabled = array();

    public static function setEvent(string $event, string $commands) {
        self::$events[$event] = explode(PHP_EOL, $commands);
    }

    public static function registerEvents() {

        $query = 'SELECT * FROM nl2_websend_commands WHERE enabled = 1';
        $results = DB::getInstance()->selectQuery($query)->results();

        foreach ($results as $event) {
            if (!isset(self::$events[$event->hook])) {
                EventHandler::registerListener($event->hook, 'WSHook::execute');
                self::setEvent($event->hook, $event->commands);
            }
        }
    }

    public static function setEnabled(string $event, bool $enabled) {
        self::$enabled[$event] = $enabled;
    }

	public static function execute($params = array()) : bool {
		if(!isset($params['event'])){
			return false;
		}

        // Check if we know anything about the event
        if (!array_key_exists($params['event'], self::$events)) {
            return false;
        }

        // Check if the event is enabled
        if (!self::$enabled[$params['event']] == false) {
            return false;
        }

		if(array_key_exists($params['event'], self::$events)){
			$event = self::$events[$params['event']];

			if(count($event)){
				$event_params = EventHandler::getEvent($params['event']);
                error_log("[WSHook] Event params: " . json_encode($event_params));
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
                foreach($event as $command){
                    $cmd = str_ireplace($event_param_keys, $event_param_values, $command);
                    WSDBInteractions::insertPendingCommand(1, $cmd);
                }
				return true;
			}
		}

		return false;
	}
}