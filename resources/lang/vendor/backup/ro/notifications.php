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
    'exception_message' => 'Cu excepția mesajului: :message',
    'exception_trace' => 'Urmă excepţie: :trace',
    'exception_message_title' => 'Mesaj de excepție',
    'exception_trace_title' => 'Urmă excepţie',

    'backup_failed_subject' => 'Nu s-a putut face copie de rezervă pentru :application_name',
    'backup_failed_body' => 'Important: A apărut o eroare în timpul generării copiei de rezervă pentru :application_name',

    'backup_successful_subject' => 'Copie de rezervă efectuată cu succes pentru :application_name',
    'backup_successful_subject_title' => 'O nouă copie de rezervă a fost efectuată cu succes!',
    'backup_successful_body' => 'Vești bune, o nouă copie de rezervă pentru :application_name a fost creată cu succes pe discul cu numele :disk_name.',

    'cleanup_failed_subject' => 'Curățarea copiilor de rezervă pentru :application_name nu a reușit.',
    'cleanup_failed_body' => 'A apărut o eroare în timpul curățirii copiilor de rezervă pentru :application_name',

    'cleanup_successful_subject' => 'Curățarea copiilor de rezervă pentru :application_name a fost făcută cu succes',
    'cleanup_successful_subject_title' => 'Curățarea copiilor de rezervă a fost făcută cu succes!',
    'cleanup_successful_body' => 'Curățarea copiilor de rezervă pentru :application_name de pe discul cu numele :disk_name a fost făcută cu succes.',

    'healthy_backup_found_subject' => 'Copiile de rezervă pentru :application_name de pe discul :disk_name sunt în regulă',
    'healthy_backup_found_subject_title' => 'Copiile de rezervă pentru :application_name sunt în regulă',
    'healthy_backup_found_body' => 'Copiile de rezervă pentru :application_name sunt considerate în regulă. Bună treabă!',

    'unhealthy_backup_found_subject' => 'Important: Copiile de rezervă pentru :application_name nu sunt în regulă',
    'unhealthy_backup_found_subject_title' => 'Important: Copiile de rezervă pentru :application_name nu sunt în regulă. :problem',
    'unhealthy_backup_found_body' => 'Copiile de rezervă pentru :application_name de pe discul :disk_name nu sunt în regulă.',
    'unhealthy_backup_found_not_reachable' => 'Nu se poate ajunge la destinația copiilor de rezervă. :error',
    'unhealthy_backup_found_empty' => 'Nu există copii de rezervă ale acestei aplicații.',
    'unhealthy_backup_found_old' => 'Cea mai recentă copie de rezervă făcută la :date este considerată prea veche.',
    'unhealthy_backup_found_unknown' => 'Ne pare rău, un motiv exact nu poate fi determinat.',
    'unhealthy_backup_found_full' => 'Copiile de rezervă folosesc prea mult spațiu de stocare. Utilizarea curentă este de :disk_usage care este mai mare decât limita permisă de :disk_limit.',
];
