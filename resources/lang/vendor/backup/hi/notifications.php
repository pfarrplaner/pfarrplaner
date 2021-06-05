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
    'exception_message' => 'गलती संदेश: :message',
    'exception_trace' => 'गलती निशान: :trace',
    'exception_message_title' => 'गलती संदेश',
    'exception_trace_title' => 'गलती निशान',

    'backup_failed_subject' => ':application_name का बैकअप असफल रहा',
    'backup_failed_body' => 'जरूरी सुचना: :application_name का बैकअप लेते समय असफल रहे',

    'backup_successful_subject' => ':application_name का बैकअप सफल रहा',
    'backup_successful_subject_title' => 'बैकअप सफल रहा!',
    'backup_successful_body' => 'खुशखबरी, :application_name का बैकअप :disk_name पर संग्रहित करने मे सफल रहे.',

    'cleanup_failed_subject' => ':application_name के बैकअप की सफाई असफल रही.',
    'cleanup_failed_body' => ':application_name के बैकअप की सफाई करते समय कुछ बाधा आयी है.',

    'cleanup_successful_subject' => ':application_name के बैकअप की सफाई सफल रही',
    'cleanup_successful_subject_title' => 'बैकअप की सफाई सफल रही!',
    'cleanup_successful_body' => ':application_name का बैकअप जो :disk_name नाम की डिस्क पर संग्रहित है, उसकी सफाई सफल रही.',

    'healthy_backup_found_subject' => ':disk_name नाम की डिस्क पर संग्रहित :application_name के बैकअप स्वस्थ है',
    'healthy_backup_found_subject_title' => ':application_name के सभी बैकअप स्वस्थ है',
    'healthy_backup_found_body' => 'बहुत बढ़िया! :application_name के सभी बैकअप स्वस्थ है.',

    'unhealthy_backup_found_subject' => 'जरूरी सुचना :  :application_name के बैकअप अस्वस्थ है',
    'unhealthy_backup_found_subject_title' => 'जरूरी सुचना : :application_name के बैकअप :problem के बजेसे अस्वस्थ है',
    'unhealthy_backup_found_body' => ':disk_name नाम की डिस्क पर संग्रहित :application_name के बैकअप अस्वस्थ है',
    'unhealthy_backup_found_not_reachable' => ':error के बजेसे बैकअप की मंजिल तक पोहोच नहीं सकते.',
    'unhealthy_backup_found_empty' => 'इस एप्लीकेशन का कोई भी बैकअप नहीं है.',
    'unhealthy_backup_found_old' => 'हालहीमें :date को लिया हुआ बैकअप बहुत पुराना है.',
    'unhealthy_backup_found_unknown' => 'माफ़ कीजिये, सही कारण निर्धारित नहीं कर सकते.',
    'unhealthy_backup_found_full' => 'सभी बैकअप बहुत ज्यादा जगह का उपयोग कर रहे है. फ़िलहाल सभी बैकअप :disk_usage जगह का उपयोग कर रहे है, जो की :disk_limit अनुमति सीमा से अधिक का है.',
];
