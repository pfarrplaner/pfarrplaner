<?php

namespace App\Console\Commands\Liturgy;

use Illuminate\Console\Command;
use Storage;

class GetLiturgyInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'liturgy:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get liturgical calendar from kirchenjahr-evangelisch.de';

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
        Storage::put(
            'liturgy.json',
            file_get_contents(
                'https://www.kirchenjahr-evangelisch.de/service.php?o=lcf&f=gaa&r=json&dl=user'
            )
        );
        $this->line('Renewed the liturgical calendar');
    }
}
