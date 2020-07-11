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

return [
    'exception_message' => 'Fejlbesked: :message',
    'exception_trace' => 'Fejl trace: :trace',
    'exception_message_title' => 'Fejlbesked',
    'exception_trace_title' => 'Fejl trace',

    'backup_failed_subject' => 'Backup af :application_name fejlede',
    'backup_failed_body' => 'Vigtigt: Der skete en fejl under backup af :application_name',

    'backup_successful_subject' => 'Ny backup af :application_name oprettet',
    'backup_successful_subject_title' => 'Ny backup!',
    'backup_successful_body' => 'Gode nyheder - der blev oprettet en ny backup af :application_name på disken :disk_name.',

    'cleanup_failed_subject' => 'Oprydning af backups for :application_name fejlede.',
    'cleanup_failed_body' => 'Der skete en fejl under oprydning af backups for :application_name',

    'cleanup_successful_subject' => 'Oprydning af backups for :application_name gennemført',
    'cleanup_successful_subject_title' => 'Backup oprydning gennemført!',
    'cleanup_successful_body' => 'Oprydningen af backups for :application_name på disken :disk_name er gennemført.',

    'healthy_backup_found_subject' => 'Alle backups for :application_name på disken :disk_name er OK',
    'healthy_backup_found_subject_title' => 'Alle backups for :application_name er OK',
    'healthy_backup_found_body' => 'Alle backups for :application_name er ok. Godt gået!',

    'unhealthy_backup_found_subject' => 'Vigtigt: Backups for :application_name fejlbehæftede',
    'unhealthy_backup_found_subject_title' => 'Vigtigt: Backups for :application_name er fejlbehæftede. :problem',
    'unhealthy_backup_found_body' => 'Backups for :application_name på disken :disk_name er fejlbehæftede.',
    'unhealthy_backup_found_not_reachable' => 'Backup destinationen kunne ikke findes. :error',
    'unhealthy_backup_found_empty' => 'Denne applikation har ingen backups overhovedet.',
    'unhealthy_backup_found_old' => 'Den seneste backup fra :date er for gammel.',
    'unhealthy_backup_found_unknown' => 'Beklager, en præcis årsag kunne ikke findes.',
    'unhealthy_backup_found_full' => 'Backups bruger for meget plads. Nuværende disk forbrug er :disk_usage, hvilket er mere end den tilladte grænse på :disk_limit.',
];
