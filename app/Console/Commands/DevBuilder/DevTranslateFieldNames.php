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
