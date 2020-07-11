<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/potofcoffee/pfarrplaner
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

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class NullableServiceFields
 */
class NullableServiceFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->integer('location_id')->nullable()->change();
            $table->time('time')->nullable()->change();
            $table->mediumText('description')->nullable()->default('')->change();
            $table->integer('need_predicant')->nullable()->default(0)->change();
            $table->string('offerings_counter1')->nullable()->default('')->change();
            $table->string('offerings_counter2')->nullable()->default('')->change();
            $table->string('offering_goal')->nullable()->default('')->change();
            $table->string('offering_description')->nullable()->default('')->change();
            $table->string('offering_type')->nullable()->default('')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->integer('location_id')->change();
            $table->time('time')->change();
            $table->mediumText('description')->change();
            $table->integer('need_predicant')->change();
            $table->string('offerings_counter1')->change();
            $table->string('offerings_counter2')->change();
            $table->string('offering_goal')->change();
            $table->string('offering_description')->change();
            $table->string('offering_type')->change();
        });
    }
}
