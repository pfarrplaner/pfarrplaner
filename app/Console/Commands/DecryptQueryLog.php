<?php

namespace App\Console\Commands;

use App\QueryLog;
use Illuminate\Console\Command;

/**
 * Class DecryptQueryLog
 * @package App\Console\Commands
 */
class DecryptQueryLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'query:list {log?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all queries executed against the database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $log = $this->argument('log') ?? '';
        $queries = QueryLog::all();
        foreach ($queries as $query) {
            $this->line($query['query']);
        }
    }
}
