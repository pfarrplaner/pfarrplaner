<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MigrateHomescreentabsSetting extends Migration
{

    protected function migrateHomeScreenTabConfig($config) {
        if (isset($config['migrated'])) return $config;
        $newConfig = [];
        foreach ($config as $tabKey => $tab) {
            $newConfig['tabs'][] = ['type' => $tabKey, 'config' => $tab ];
        }
        $newConfig['migrated'] = true;
        return $newConfig;
    }


    protected function migrateBack($config) {
        if (!isset($config['migrated'])) return $config;
        $newConfig = [];
        foreach ($config as $tabKey => $tab) {
            $newConfig[$tab['type']] = $tab['config'];
        }
        return $newConfig;
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (\App\User::all() as $user) {
            $setting = $user->getSetting('homeScreenTabsConfig', null);
            if ($setting) {
                if (!isset($setting['migrated'])) {
                    echo 'Migrating User #'.$user->id.'... ';
                    $user->setSetting('homeScreenTabsConfig', $this->migrateHomeScreenTabConfig($setting));
                    echo 'Done.'.PHP_EOL;
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach (\App\User::all() as $user) {
            $setting = $user->getSetting('homeScreenTabsConfig', null);
            if ($setting) {
                if (isset($setting['migrated'])) {
                    echo 'Migrating User #'.$user->id.' back... ';
                    $user->setSetting('homeScreenTabsConfig', $this->migrateBack($setting));
                    echo 'Done.'.PHP_EOL;
                }
            }
        }
    }
}
