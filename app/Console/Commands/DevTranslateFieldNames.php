<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DevTranslateFieldNames extends Command
{
    public const TAG = '//===AUTO_INSERTED_ATTRIBUTES===//';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:translate-field-names';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add missing field names to translation file';

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
        $schema = DB::connection()->getDoctrineSchemaManager();
        $missingFields = [];
        foreach ($schema->listTableNames() as $tableName) {
            foreach ($schema->listTableColumns($tableName) as $attribute => $data) {
                $attribute = str_replace('`', '', $attribute);
                $key = 'validation.attributes.'.$attribute;
                if (!in_array($attribute, ['id', 'created_at', 'updated_at'])) {
                    if (__($key) == $key) $missingFields[$attribute] = $attribute;
                }
            }
        }

        $code = '';
        foreach ($missingFields as $attribute) {
            $code .= "        '{$attribute}' => '{$attribute}',\n";
            $this->line('Attribute "'.$attribute.'" is missing from validation language file.');
        }

        $langFile = resource_path('lang/de/validation.php');
        file_put_contents($langFile, str_replace(self::TAG, $code.self::TAG, file_get_contents($langFile)));

        $this->line('Added '.count($missingFields).' attributes to '.$langFile);
    }
}
