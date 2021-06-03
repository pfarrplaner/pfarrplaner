<?php

namespace App\Console\Commands;

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
