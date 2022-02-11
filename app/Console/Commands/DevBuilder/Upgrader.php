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

namespace App\Console\Commands\DevBuilder;

use App\Absence;
use App\Attachment;
use App\Baptism;
use App\City;
use App\Comment;
use App\Funeral;
use App\Location;
use App\Parish;
use App\Service;
use App\Services\PackageService;
use App\StreetRange;
use App\User;
use App\Wedding;
use Carbon\Carbon;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Console\Command;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Output\Output;

/**
 * Class DemoBuilder
 * @package App\Console\Commands
 */
class Upgrader extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform all necessary steps for an upgrade';


    /**
     * The migrator instance.
     *
     * @var Migrator
     */
    protected $migrator;

    public function __construct()
    {
        parent::__construct();
        $this->migrator = app('migrator');
    }


    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $this->output->title('Pfarrplaner upgrade');
        $this->line(str_pad('New Pfarrplaner app version: ', 30) . PackageService::info()['buildVersion']);
        $this->line(str_pad('Source build date: ', 30) . PackageService::info()['date']->format('Y-m-d H:i'));
        $this->line(str_pad('Upgrade date: ', 30) . Carbon::now()->format('Y-m-d H:i'));

        $this->output->section('Pre-flight checks');
        if (!$this->checkRequirements()) {
            return;
        }

        if ($this->hasMigrations()) {
            $this->output->section('Database upgrades');
            $result = shell_exec('php artisan migrate');
            $this->writeResult('Migrated database', $result);
        } else {
            $this->writeResult('No new migrations to be run', true);
        }

        $this->output->section('Composer');
        $result = shell_exec('composer update');
        $this->writeResult('Composer depencency build', $result);

        $this->output->section('NPM dependencies');
        $result = shell_exec('npm install');
        $this->writeResult('NPM depencency build', $result);

        $environment = app()->environment() == 'production' ? 'prod' : 'dev';
        $this->output->section('Webpack ('.$environment.')');
        $result = shell_exec('npm run '.$environment);
        $this->writeResult('Webpack build', $result);

        $this->output->section('Cache & cleanup');
        $result = shell_exec('php artisan optimize');
        $this->writeResult('App optimized', $result);

    }

    protected function writeDelayedResult($title, $resultCallBack)
    {
        $this->output->write(str_pad($title, 60), false);
        $result = $resultCallBack();
        $this->output->writeln($result ? '      [<info>OK</info>]' : '  [<error>FAILED</error>]');
        return $result;
    }

    protected function writeResult($title, $result)
    {
        $this->output->write(str_pad($title, 60), false);
        $this->output->writeln($result ? '      [<info>OK</info>]' : '  [<error>FAILED</error>]');
        return $result;
    }

    protected function checkRequirement($title, $examinedValue, $compareTo = true, $individualMethod = false)
    {
        return $this->writeResult(
            'Check: ' . $title . ' => ' . (string)$examinedValue,
            ($individualMethod || ($examinedValue == $compareTo))
        );
    }

    protected function checkRequirements()
    {
        return $this->checkRequirement(
            'Migration table is accessible',
            $this->migrator->repositoryExists(),
            true
        );
    }

    protected function hasMigrations() {
        $ran = collect($this->migrator->getRepository()->getRan());
        $migrations = collect(
            array_keys(
                $this->migrator->getMigrationFiles(
                    array_merge(
                        $this->migrator->paths(),
                        [$this->laravel->databasePath() . DIRECTORY_SEPARATOR . 'migrations']
                    )
                )
            )
        )->diff($ran);
        return $migrations->count();
    }

}
