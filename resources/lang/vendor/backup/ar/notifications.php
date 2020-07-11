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
    'exception_message' => 'رسالة استثناء: :message',
    'exception_trace' => 'تتبع الإستثناء: :trace',
    'exception_message_title' => 'رسالة استثناء',
    'exception_trace_title' => 'تتبع الإستثناء',

    'backup_failed_subject' => 'أخفق النسخ الاحتياطي لل :application_name',
    'backup_failed_body' => 'مهم: حدث خطأ أثناء النسخ الاحتياطي :application_name',

    'backup_successful_subject' => 'نسخ احتياطي جديد ناجح ل :application_name',
    'backup_successful_subject_title' => 'نجاح النسخ الاحتياطي الجديد!',
    'backup_successful_body' => 'أخبار عظيمة، نسخة احتياطية جديدة ل :application_name تم إنشاؤها بنجاح على القرص المسمى :disk_name.',

    'cleanup_failed_subject' => 'فشل تنظيف النسخ الاحتياطي للتطبيق :application_name .',
    'cleanup_failed_body' => 'حدث خطأ أثناء تنظيف النسخ الاحتياطية ل :application_name',

    'cleanup_successful_subject' => 'تنظيف النسخ الاحتياطية ل :application_name تمت بنجاح',
    'cleanup_successful_subject_title' => 'تنظيف النسخ الاحتياطية تم بنجاح!',
    'cleanup_successful_body' => 'تنظيف النسخ الاحتياطية ل :application_name على القرص المسمى :disk_name تم بنجاح.',

    'healthy_backup_found_subject' => 'النسخ الاحتياطية ل :application_name على القرص :disk_name صحية',
    'healthy_backup_found_subject_title' => 'النسخ الاحتياطية ل :application_name صحية',
    'healthy_backup_found_body' => 'تعتبر النسخ الاحتياطية ل :application_name صحية. عمل جيد!',

    'unhealthy_backup_found_subject' => 'مهم: النسخ الاحتياطية ل :application_name غير صحية',
    'unhealthy_backup_found_subject_title' => 'مهم: النسخ الاحتياطية ل :application_name غير صحية. :problem',
    'unhealthy_backup_found_body' => 'النسخ الاحتياطية ل :application_name على القرص :disk_name غير صحية.',
    'unhealthy_backup_found_not_reachable' => 'لا يمكن الوصول إلى وجهة النسخ الاحتياطي. :error',
    'unhealthy_backup_found_empty' => 'لا توجد نسخ احتياطية لهذا التطبيق على الإطلاق.',
    'unhealthy_backup_found_old' => 'تم إنشاء أحدث النسخ الاحتياطية في :date وتعتبر قديمة جدا.',
    'unhealthy_backup_found_unknown' => 'عذرا، لا يمكن تحديد سبب دقيق.',
    'unhealthy_backup_found_full' => 'النسخ الاحتياطية تستخدم الكثير من التخزين. الاستخدام الحالي هو :disk_usage وهو أعلى من الحد المسموح به من :disk_limit.',
];
