<?php
/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/pfarrplaner/pfarrplaner
 * @version git: $Id$
 *
 * Sponsored by: Evangelischer Kirchenbezirk Balingen, https://www.kirchenbezirk-balingen.de
 *
 * Pfarrplaner is based on the Laravel framework (https://laravel.com).
 * This file may contain code created by Laravel's scaffolding functions.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace App\Console\Commands\QueryLog;

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
