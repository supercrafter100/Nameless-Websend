<?php

class WSDBInteractions {

    public static function getConsoleOutput($id): array
    {
        // Get cached value
        $cache = new Cache(['name' => 'nameless', 'extension' => '.cache', 'path' => ROOT_PATH . '/cache/']);
        $cache->setCache('websend_settings');

        $max_lines = $cache->retrieve('max_displayed_records');

        $query = "SELECT content FROM `nl2_websend_console_output` WHERE `server_id` = ? ORDER BY `id` LIMIT ?";
        $lines = DB::getInstance()->selectQuery($query, [
            (int) $id,
            (int) $max_lines ?? 200
        ])->results();
        return array_map(fn($item) => $item->content, $lines);
    }

    public static function insertConsoleLine($id, $line): void
    {
        DB::getInstance()->insert('websend_console_output', [
            'server_id' => $id,
            'content' => $line
        ]);

        self::deleteOversizedConsoleLines();
    }

    public static function deleteOversizedConsoleLines() : void {

        // Get cached value
        $cache = new Cache(['name' => 'nameless', 'extension' => '.cache', 'path' => ROOT_PATH . '/cache/']);
        $cache->setCache('websend_settings');

        $max_lines = $cache->retrieve('console_max_lines');

        $query = "DELETE FROM nl2_websend_console_output WHERE id < (SELECT max(id) FROM nl2_websend_console_output) - ?";
        DB::getInstance()->createQuery($query, [$max_lines ?? 500]);
    }

    public static function insertPendingCommand($id, $command) : void {
        $query = "INSERT INTO `nl2_websend_pending_commands` (`server_id`, `command`) VALUES (?, ?)";
        DB::getInstance()->createQuery($query, [
            $id,
            $command
        ]);
    }

    public static function getPendingCommands($id) : array {
        $query = "SELECT * FROM `nl2_websend_pending_commands` WHERE `server_id` = ? AND `status` = 0";
        return DB::getInstance()->selectQuery($query, [
            (int) $id
        ])->results();
    }
}