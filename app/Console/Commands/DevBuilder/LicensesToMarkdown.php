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

use Illuminate\Console\Command;

class LicensesToMarkdown extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'licenses:markdown';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export all license info to markdown';

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
        $output = "Dependency licenses\n===================\n\nPfarrplaner uses the following open-source libraries:\n\n";


        if (file_exists(base_path('.composer-licenses'))) {
            $output .= "## Composer packages\n\n";
            $data = json_decode(file_get_contents(base_path('.composer-licenses')), true);
            foreach ($data['dependencies'] as $packageName => $record) {
                if (is_array($record['license'])) {
                    $licenseInfo = join(' / ', $record['license']).' '.(count($record['license']) > 1 ? 'licenses' : 'license');
                } else {
                    $licenseInfo = $record['license'].' license';
                }
                $output .= ' - ['.$packageName.'](https://packagist.org/packages/'.$packageName.') '.$record['version']."\n"
                          .'    under the '.$licenseInfo."\n\n";
            }
            $output .= "\n\n";
        } else {
            $this->line('Warning: Composer license file not found');
        }
        if (file_exists(base_path('.npm-licenses'))) {
            $output .= "## Node packages\n\n";
            $data = json_decode(file_get_contents(base_path('.npm-licenses')), true);
            foreach ($data as $package => $record) {
                list($packageName, $version) = explode('@', $package);
                if (is_array($record['licenses'])) {
                    $licenseInfo = join(' / ', $record['licenses']).' '.(count($record['licenses']) > 1 ? 'licenses' : 'license');
                } else {
                    $licenseInfo = $record['licenses'].' license';
                }
                if (isset($record['repository'])) {
                    $output .= ' - ['.$packageName.']('.$record['repository'].') '.$version."\n";
                } else {
                    $output .= ' - '.$packageName.' '.$version."\n";
                }
                if (isset($record['publisher'])) {
                    $output .= '    by '.$record['publisher'];
                    if (isset($record['email'])) {
                        $output .= ' ('.$record['email'].')';
                    }
                    $output.= "\n";
                }
                $output .= '    under the '.$licenseInfo."\n\n";
            }
        } else {
            $this->line('Warning: NPM license file not found');
        }
        file_put_contents(base_path('CREDITS.md'), $output);
    }
}
