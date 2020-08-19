<?php

namespace App\Console\Commands;

use App\QueryLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class RestoreFromQueryLog
 * @package App\Console\Commands
 */
class RestoreFromQueryLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'query:restore';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore from query log';

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
        $queries = QueryLog::all();

        if (count($queries) == 0) {
            $this->error('There are no queries to restore.');
            return;
        }

        $start = $queries[0]['time'];

        $this->warn('CAUTION! This will re-execute '.count($queries).' queries from '.$start->format('Y-m-d H:i:s').' to '.QueryLog::date()->format('Y-m-d H:i:s').'.');
        $this->warn('Using this function could seriously mess up your database!');
        $this->warn('This command is only meant to be used after restoring from a nightly backup.');
        if ($this->confirm('Do you really want to do this?')) {
            $ctr = 0;
            foreach ($queries as $query) {
                $ctr++;
                $this->line('Restoring archived query #'.$ctr.' from '.$query['time']->format('Y-m-d H:i:s'));
                DB::query($query['query']);
            }
            $this->info('Restored '.count($queries).' queries. DB is now up-to-date as of '.QueryLog::date()->format('Y-m-d H:i:s').'.');
            QueryLog::clear();
            $this->info('Query log cleared');
        }
    }
}
