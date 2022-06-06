<?php
/*
 *	Originally made by Samerton (https://github.com/samerton)
 *  Fork by Supercrafter100 (https://github.com/supercrafter100)
 *
 *  NamelessMC version 2.0.0-pr13
 *
 *  License: MIT
 *
 *  Websend class which has some database utilities
 */

class WSDBInteractions {

    public static function getConsoleOutput($id, $startIndex): array
    {
        // Get cached value
        $cache = new Cache(['name' => 'nameless', 'extension' => '.cache', 'path' => ROOT_PATH . '/cache/']);
        $cache->setCache('websend_settings');

        $max_lines = $cache->isCached('max_displayed_records') ? $cache->retrieve('max_displayed_records') : DB::getInstance()->get('websend_settings', ['name', 'max_displayed_records'])->first();
        $cache->store('max_displayed_records', $max_lines);

        $lines = DB::getInstance()->query('SELECT content, id FROM `nl2_websend_console_output` WHERE `server_id` = ? AND id >= ? ORDER BY `id` LIMIT ?', [
            $id,
            $startIndex,
            $max_lines ?? 200
        ])->results();

        $last_index = $lines[count($lines)-1]->id;
        return [array_map(fn($item) => $item->content, $lines), $last_index];
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

        $max_lines = $cache->isCached('console_max_lines') ? $cache->retrieve('console_max_lines') : DB::getInstance()->get('websend_settings', ['name', 'console_max_lines'])->first();
        $cache->store('console_max_lines', $max_lines);

        // TODO: Make it so this is server id specific. Maybe ask Derkades for help on the query with this one?
        DB::getInstance()->query('DELETE FROM nl2_websend_console_output WHERE id < (SELECT MAX(id) FROM nl2_websend_console_output) - ?', [$max_lines ?? 500]);
    }

    public static function insertPendingCommand($id, $command) : void {
        DB::getInstance()->query('INSERT INTO `nl2_websend_pending_commands` (`server_id`, `command`) VALUES (?, ?)', [
            $id,
            $command
        ]);
    }

    public static function getPendingCommands($id) : array {
        $query = "SELECT * FROM `nl2_websend_pending_commands` WHERE `server_id` = ? AND `status` = 0";
        return DB::getInstance()->query($query, [
            (int) $id
        ])->results();
    }
}