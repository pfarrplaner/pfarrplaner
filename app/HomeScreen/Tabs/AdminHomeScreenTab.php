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

namespace App\HomeScreen\Tabs;

use App\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Backup\BackupDestination\Backup;
use Spatie\Backup\Commands\ListCommand;
use Spatie\Backup\Helpers\Format;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatus;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatusFactory;

class AdminHomeScreenTab extends AbstractHomeScreenTab
{
    protected $title = 'Administration';
    protected $description = 'Werkzeuge für Administrator:innen';
    protected $config = [];

    public function isAvailable(): bool
    {
        return Auth::user()->isAdmin;
    }


    public function toArray($data = [])
    {
        $data['people'] = User::all();
        $data['backups'] = $this->getBackups();

        return parent::toArray($data);
    }

    protected function getBackups() {
        $statuses = BackupDestinationStatusFactory::createForMonitorConfig(config('backup.monitor_backups'));

        $cmd = new ListCommand();
        $result = [];

        /** @var BackupDestinationStatus $status */
        foreach ($statuses as $status) {
            $destination = $status->backupDestination();

            $row = [
                'name' => $destination->backupName(),
                'disk' => $destination->diskName(),
                'diskCheck' => ($status->getHealthCheckFailure() === null),
                'reachable' => $destination->isReachable(),
                'healthy' => $status->isHealthy(),
                'amount' => $destination->backups()->count(),
                'newest' => $this->getFormattedBackupDate($destination->newestBackup()),
                'usedStorage' => Format::humanReadableSize($destination->usedStorage()),
            ];

            if (! $destination->isReachable()) {
                foreach (['amount', 'newest', 'usedStorage'] as $propertyName) {
                    $row[$propertyName] = '/';
                }
            }

            $result[] = $row;
        }

        return $result;
    }

    protected function getFormattedBackupDate(Backup $backup = null)
    {
        return is_null($backup)
            ? 'Keine Backups vorhanden'
            : Format::ageInDays($backup->date());
    }


}
