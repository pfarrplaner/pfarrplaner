<?php

namespace App\Console\Commands;

use App\QueryLog;
use Illuminate\Console\Command;

/**
 * Class TruncateQueryLog
 * @package App\Console\Commands
 */
class TruncateQueryLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'query:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the query log';

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
        QueryLog::clear();
        $this->info('Query log cleared');
    }
}
