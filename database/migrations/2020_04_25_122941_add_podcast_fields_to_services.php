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
 * Class AddPodcastFieldsToServices
 */
class AddPodcastFieldsToServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('external_url')->default('')->nullable();
            $table->string('sermon_title')->default('')->nullable();
            $table->string('sermon_reference')->default('')->nullable();
            $table->string('sermon_image')->default('')->nullable();
            $table->text('sermon_description')->nullable();
        });
        Schema::table('cities', function (Blueprint $table) {
            $table->string('podcast_title')->default('')->nullable();
            $table->string('podcast_logo')->default('')->nullable();
            $table->string('sermon_default_image')->default('')->nullable();
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
            $table->dropColumn('external_url');
            $table->dropColumn('sermon_title');
            $table->dropColumn('sermon_reference');
            $table->string('sermon_image')->default('')->nullable();
            $table->dropColumn('sermon_description');
        });
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn('podcast_title');
            $table->dropColumn('podcast_logo');
            $table->dropColumn('sermon_default_image');
        });
    }
}
