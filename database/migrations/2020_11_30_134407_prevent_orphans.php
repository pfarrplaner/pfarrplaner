<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PreventOrphans extends Migration
{

    protected function getColumnType($table, $column) {
        $dColumn = DB::connection()->getDoctrineColumn($table, $column);
        return $dColumn->getType()->getName().($dColumn->getUnsigned() ? 'Unsigned' : 'Signed');
    }

    public function createCascadingDelete($tableName, $foreignKey, $foreignTableName) {
        $existingConstraints = $this->listTableForeignKeys($tableName);
        $constraint = $tableName.'_'.$foreignKey.'_foreign';
        if (in_array($constraint, $existingConstraints)) {
            // drop existing foreign key constraint
            Schema::table($tableName, function(Blueprint $table) use ($constraint) {
                $table->dropForeign($constraint);
            });
        }
        $keyType = $this->getColumnType($foreignTableName, 'id');
        $localKeyType = $this->getColumnType($tableName, $foreignKey);
        if ($keyType != $localKeyType) {
            Schema::table($tableName, function (Blueprint $table) use ($keyType, $foreignKey) {
                if ($keyType == 'bigint') {
                    echo 'Changing my key to unsignedBigInteger'.PHP_EOL;
                    $table->unsignedBigInteger($foreignKey)->change();
                } elseif (substr($keyType,0,7) == 'integer') {
                    echo 'Changing my key to unsignedInteger'.PHP_EOL;
                    $table->unsignedInteger($foreignKey)->change();
                }
            });
        }
        Schema::table($tableName, function(Blueprint $table) use ($foreignKey, $foreignTableName, $keyType) {
            $table->foreign($foreignKey)->references('id')->on($foreignTableName)->onDelete('cascade');
        });
    }


    public function listTableForeignKeys($table)
    {
        $conn = Schema::getConnection()->getDoctrineSchemaManager();

        return array_map(function($key) {
            return $key->getName();
        }, $conn->listTableForeignKeys($table));
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function(Blueprint $table){
            $table->unsignedInteger('location_id')->change();
        });
        Schema::table('locations', function(Blueprint $table){
            $table->increments('id')->change();
        });
        $this->createCascadingDelete('services', 'day_id', 'days');
        $this->createCascadingDelete('absences', 'user_id', 'users');
        $this->createCascadingDelete('baptisms', 'service_id', 'services');
        $this->createCascadingDelete('funerals', 'service_id', 'services');
        $this->createCascadingDelete('weddings', 'service_id', 'services');
        $this->createCascadingDelete('locations', 'city_id', 'cities');
        $this->createCascadingDelete('parishes', 'city_id', 'cities');
        $this->createCascadingDelete('replacements', 'absence_id', 'absences');
        $this->createCascadingDelete('seating_rows', 'seating_section_id', 'seating_sections');
        $this->createCascadingDelete('seating_sections', 'location_id', 'locations');
        $this->createCascadingDelete('street_ranges', 'parish_id', 'parishes');
        $this->createCascadingDelete('subscriptions', 'city_id', 'cities');
        $this->createCascadingDelete('subscriptions', 'user_id', 'users');
        $this->createCascadingDelete('user_settings', 'user_id', 'users');
        $this->createCascadingDelete('user_approver', 'user_id', 'users');
        $this->createCascadingDelete('user_approver', 'approver_id', 'users');
        $this->createCascadingDelete('service_service_group', 'service_group_id', 'service_groups');
        $this->createCascadingDelete('service_tag', 'service_id', 'services');
        $this->createCascadingDelete('service_tag', 'tag_id', 'tags');
        $this->createCascadingDelete('parish_user', 'user_id', 'users');
        $this->createCascadingDelete('parish_user', 'parish_id', 'parishes');
        $this->createCascadingDelete('city_day', 'city_id', 'cities');
        $this->createCascadingDelete('city_day', 'day_id', 'days');

        $this->createCascadingDelete('service_user', 'service_id', 'services');
        $this->createCascadingDelete('service_user', 'user_id', 'users');
        $this->createCascadingDelete('city_user', 'city_id', 'cities');
        $this->createCascadingDelete('city_user', 'user_id', 'users');
        $this->createCascadingDelete('user_home', 'city_id', 'cities');
        $this->createCascadingDelete('user_home', 'user_id', 'users');
        $this->createCascadingDelete('replacement_user', 'replacement_id', 'replacements');
        $this->createCascadingDelete('replacement_user', 'user_id', 'users');
        $this->createCascadingDelete('service_service_group', 'service_id', 'services');


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
