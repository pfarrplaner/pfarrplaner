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
    'exception_message' => 'Hata mesajı: :message',
    'exception_trace' => 'Hata izleri: :trace',
    'exception_message_title' => 'Hata mesajı',
    'exception_trace_title' => 'Hata izleri',

    'backup_failed_subject' => 'Yedeklenemedi :application_name',
    'backup_failed_body' => 'Önemli: Yedeklenirken bir hata oluştu :application_name',

    'backup_successful_subject' => 'Başarılı :application_name yeni yedeklemesi',
    'backup_successful_subject_title' => 'Başarılı bir yeni yedekleme!',
    'backup_successful_body' => 'Harika bir haber, :application_name âit yeni bir yedekleme :disk_name adlı diskte başarıyla oluşturuldu.',

    'cleanup_failed_subject' => ':application_name yedeklemeleri temizlenmesi başarısız.',
    'cleanup_failed_body' => ':application_name yedeklerini temizlerken bir hata oluştu ',

    'cleanup_successful_subject' => ':application_name yedeklemeleri temizlenmesi başarılı.',
    'cleanup_successful_subject_title' => 'Yedeklerin temizlenmesi başarılı!',
    'cleanup_successful_body' => ':application_name yedeklemeleri temizlenmesi ,:disk_name diskinden silindi',

    'healthy_backup_found_subject' => ':application_name yedeklenmesi ,:disk_name adlı diskte sağlıklı',
    'healthy_backup_found_subject_title' => ':application_name yedeklenmesi sağlıklı',
    'healthy_backup_found_body' => ':application_name için yapılan yedeklemeler sağlıklı sayılır. Aferin!',

    'unhealthy_backup_found_subject' => 'Önemli: :application_name için yedeklemeler sağlıksız',
    'unhealthy_backup_found_subject_title' => 'Önemli: :application_name için yedeklemeler sağlıksız. :problem',
    'unhealthy_backup_found_body' => 'Yedeklemeler: :application_name disk: :disk_name sağlıksız.',
    'unhealthy_backup_found_not_reachable' => 'Yedekleme hedefine ulaşılamıyor. :error',
    'unhealthy_backup_found_empty' => 'Bu uygulamanın yedekleri yok.',
    'unhealthy_backup_found_old' => ':date tarihinde yapılan en son yedekleme çok eski kabul ediliyor.',
    'unhealthy_backup_found_unknown' => 'Üzgünüm, kesin bir sebep belirlenemiyor.',
    'unhealthy_backup_found_full' => 'Yedeklemeler çok fazla depolama alanı kullanıyor. Şu anki kullanım: :disk_usage, izin verilen sınırdan yüksek: :disk_limit.',
];
