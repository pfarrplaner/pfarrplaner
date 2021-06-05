<?php

namespace App\Console\Commands;

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
