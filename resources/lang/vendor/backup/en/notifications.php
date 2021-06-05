<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
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

return [
    'exception_message' => 'Exception message: :message',
    'exception_trace' => 'Exception trace: :trace',
    'exception_message_title' => 'Exception message',
    'exception_trace_title' => 'Exception trace',

    'backup_failed_subject' => 'Failed back up of :application_name',
    'backup_failed_body' => 'Important: An error occurred while backing up :application_name',

    'backup_successful_subject' => 'Successful new backup of :application_name',
    'backup_successful_subject_title' => 'Successful new backup!',
    'backup_successful_body' => 'Great news, a new backup of :application_name was successfully created on the disk named :disk_name.',

    'cleanup_failed_subject' => 'Cleaning up the backups of :application_name failed.',
    'cleanup_failed_body' => 'An error occurred while cleaning up the backups of :application_name',

    'cleanup_successful_subject' => 'Clean up of :application_name backups successful',
    'cleanup_successful_subject_title' => 'Clean up of backups successful!',
    'cleanup_successful_body' => 'The clean up of the :application_name backups on the disk named :disk_name was successful.',

    'healthy_backup_found_subject' => 'The backups for :application_name on disk :disk_name are healthy',
    'healthy_backup_found_subject_title' => 'The backups for :application_name are healthy',
    'healthy_backup_found_body' => 'The backups for :application_name are considered healthy. Good job!',

    'unhealthy_backup_found_subject' => 'Important: The backups for :application_name are unhealthy',
    'unhealthy_backup_found_subject_title' => 'Important: The backups for :application_name are unhealthy. :problem',
    'unhealthy_backup_found_body' => 'The backups for :application_name on disk :disk_name are unhealthy.',
    'unhealthy_backup_found_not_reachable' => 'The backup destination cannot be reached. :error',
    'unhealthy_backup_found_empty' => 'There are no backups of this application at all.',
    'unhealthy_backup_found_old' => 'The latest backup made on :date is considered too old.',
    'unhealthy_backup_found_unknown' => 'Sorry, an exact reason cannot be determined.',
    'unhealthy_backup_found_full' => 'The backups are using too much storage. Current usage is :disk_usage which is higher than the allowed limit of :disk_limit.',
];
