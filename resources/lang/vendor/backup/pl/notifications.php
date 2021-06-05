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
    'exception_message' => 'Błąd: :message',
    'exception_trace' => 'Zrzut błędu: :trace',
    'exception_message_title' => 'Błąd',
    'exception_trace_title' => 'Zrzut błędu',

    'backup_failed_subject' => 'Tworzenie kopii zapasowej aplikacji :application_name nie powiodło się',
    'backup_failed_body' => 'Ważne: Wystąpił błąd podczas tworzenia kopii zapasowej aplikacji :application_name',

    'backup_successful_subject' => 'Pomyślnie utworzono kopię zapasową aplikacji :application_name',
    'backup_successful_subject_title' => 'Nowa kopia zapasowa!',
    'backup_successful_body' => 'Wspaniała wiadomość, nowa kopia zapasowa aplikacji :application_name została pomyślnie utworzona na dysku o nazwie :disk_name.',

    'cleanup_failed_subject' => 'Czyszczenie kopii zapasowych aplikacji :application_name nie powiodło się.',
    'cleanup_failed_body' => 'Wystąpił błąd podczas czyszczenia kopii zapasowej aplikacji :application_name',

    'cleanup_successful_subject' => 'Kopie zapasowe aplikacji :application_name zostały pomyślnie wyczyszczone',
    'cleanup_successful_subject_title' => 'Kopie zapasowe zostały pomyślnie wyczyszczone!',
    'cleanup_successful_body' => 'Czyszczenie kopii zapasowych aplikacji :application_name na dysku :disk_name zakończone sukcecem.',

    'healthy_backup_found_subject' => 'Kopie zapasowe aplikacji :application_name na dysku :disk_name są poprawne',
    'healthy_backup_found_subject_title' => 'Kopie zapasowe aplikacji :application_name są poprawne',
    'healthy_backup_found_body' => 'Kopie zapasowe aplikacji :application_name są poprawne. Dobra robota!',

    'unhealthy_backup_found_subject' => 'Ważne: Kopie zapasowe aplikacji :application_name są niepoprawne',
    'unhealthy_backup_found_subject_title' => 'Ważne: Kopie zapasowe aplikacji :application_name są niepoprawne. :problem',
    'unhealthy_backup_found_body' => 'Kopie zapasowe aplikacji :application_name na dysku :disk_name są niepoprawne.',
    'unhealthy_backup_found_not_reachable' => 'Miejsce docelowe kopii zapasowej nie jest osiągalne. :error',
    'unhealthy_backup_found_empty' => 'W aplikacji nie ma żadnej kopii zapasowych tej aplikacji.',
    'unhealthy_backup_found_old' => 'Ostatnia kopia zapasowa wykonania dnia :date jest zbyt stara.',
    'unhealthy_backup_found_unknown' => 'Niestety, nie można ustalić dokładnego błędu.',
    'unhealthy_backup_found_full' => 'Kopie zapasowe zajmują zbyt dużo miejsca. Obecne użycie dysku :disk_usage jest większe od ustalonego limitu :disk_limit.',
];
