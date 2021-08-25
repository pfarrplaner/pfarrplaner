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
use Illuminate\Support\Facades\Route;

class BuildManualPages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'build:manual';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build the online manual';

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
        $toc = $this->getTOC();

        $this->line('Scanning for missing manual pages...');
        // create empty pages for every get route not yet covered
        $empty = base_path('manual/notfound.md');
        $routes = Route::getRoutes()->getRoutesByMethod();
        $routeKeys = [];
        ksort($routes);
        /** @var \Illuminate\Routing\Route $route */
        foreach ($routes['GET'] as $route) {
            $routeKey = $route->getAction('as');
            if ($routeKey) {
                $targetFile = base_path('manual/'.$routeKey.'.md');
                if (!file_exists(base_path('manual/'.$routeKey.'.md'))) {
                    $this->line('Creating new manual page '.$targetFile);
                    copy($empty, $targetFile);
                }
            }
        }

        $this->line('Moving image files...');
        foreach (glob(base_path('manual/img*.png')) as $file) {
            copy($file, base_path('manual/media/images/'.basename($file)));
            unlink($file);
        }
        $this->line('Rewriting image references...');
        foreach ($this->getAllPages() as $file) {
            file_put_contents($file, str_replace('(img', '(media/images/img', file_get_contents($file)));
        }

        $this->line('Building table of contents...');
        file_put_contents(base_path('manual/index.md'), 'Inhaltsverzeichnis'.PHP_EOL.'=================='.PHP_EOL.PHP_EOL.$this->outputTOCLevel($toc));
        $this->line('Writing table of contents to file '.base_path('manual/index.md'));

        $this->line('Done');
    }

    protected function getTOC() {
        $toc = [];
        foreach ($this->getAllPages() as $file) {
            $page = file_get_contents($file);
            preg_match_all('/\[\/\/\]: \# \(TOC: (.*?)\)/', $page, $matches);
            if (count($matches[1])) {
                foreach ($matches[1] as $match) {
                    preg_match('/((?:\d+.)+)(.*)/', $match, $matches2);
                    if (count($matches2)) {
                        $levels = explode('.', trim($matches2[1]));
                        $jsonObject = '{ ## }';
                        foreach ($levels as $level) {
                            $jsonObject = str_replace('##', '"_'.$level.'": { "items": { ## }}', $jsonObject);
                        }
                        $jsonObject = str_replace('{ ## }', '{}, "title": "'.$matches2[2].'", "file": "'.basename($file).'"', $jsonObject);
                        $toc = array_merge_recursive(json_decode($jsonObject, true), $toc);
                    }
                }
            }
        }
        return $toc;
    }

    protected function outputTOCLevel($data, $level = 0, $prefix='')
    {
        $o = '';
        $data2 = [];
        foreach ($data as $key => $item) {
            $data2[substr($key, 1)] = $item;
        }
        ksort($data2);
        foreach ($data2 as $key => $item) {
            $o .= str_pad('', $level*4, ' ', STR_PAD_LEFT)
                .'* ['.$prefix.$key.'. '.$item['title'].']('.$item['file'].')'.PHP_EOL;
            if (isset($item['items'])) $o .= $this->outputTOCLevel($item['items'], ($level+1), $prefix.$key.'.');
        }
        return $o;
    }

    protected function getAllPages()
    {
        return glob(base_path('manual/*.md'));
    }

}
