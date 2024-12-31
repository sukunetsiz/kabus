<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DropMessagingTables extends Command
{
    protected $signature = 'drop:messaging-tables';
    protected $description = 'Drop conversations and messages tables';

    public function handle()
    {
        $tables = ['conversations', 'messages'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::dropIfExists($table);
                $this->info("Table '{$table}' dropped successfully.");
            } else {
                $this->info("Table '{$table}' does not exist.");
            }
        }

        $this->info('Operation completed.');
    }
}
