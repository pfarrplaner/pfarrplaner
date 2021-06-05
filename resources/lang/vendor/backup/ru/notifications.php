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
    'exception_message' => 'Сообщение об ошибке: :message',
    'exception_trace' => 'Сведения об ошибке: :trace',
    'exception_message_title' => 'Сообщение об ошибке',
    'exception_trace_title' => 'Сведения об ошибке',

    'backup_failed_subject' => 'Не удалось сделать резервную копию :application_name',
    'backup_failed_body' => 'Внимание: Произошла ошибка во время резервного копирования :application_name',

    'backup_successful_subject' => 'Успешно создана новая резервная копия :application_name',
    'backup_successful_subject_title' => 'Успешно создана новая резервная копия!',
    'backup_successful_body' => 'Отличная новость, новая резервная копия :application_name успешно создана и сохранена на диск :disk_name.',

    'cleanup_failed_subject' => 'Не удалось очистить резервные копии :application_name',
    'cleanup_failed_body' => 'Произошла ошибка при очистке резервных копий :application_name',

    'cleanup_successful_subject' => 'Очистка от резервных копий :application_name прошла успешно',
    'cleanup_successful_subject_title' => 'Очистка резервных копий прошла удачно!',
    'cleanup_successful_body' => 'Очистка от старых резервных копий :application_name на диске :disk_name прошла удачно.',

    'healthy_backup_found_subject' => 'Резервная копия :application_name с диска :disk_name установлена',
    'healthy_backup_found_subject_title' => 'Резервная копия :application_name установлена',
    'healthy_backup_found_body' => 'Резервная копия :application_name успешно установлена. Хорошая работа!',

    'unhealthy_backup_found_subject' => 'Внимание: резервная копия :application_name не установилась',
    'unhealthy_backup_found_subject_title' => 'Внимание: резервная копия для :application_name не установилась. :problem',
    'unhealthy_backup_found_body' => 'Резервная копия для :application_name на диске :disk_name не установилась.',
    'unhealthy_backup_found_not_reachable' => 'Резервная копия не смогла установиться. :error',
    'unhealthy_backup_found_empty' => 'Резервные копии для этого приложения отсутствуют.',
    'unhealthy_backup_found_old' => 'Последнее резервное копирование создано :date является устаревшим.',
    'unhealthy_backup_found_unknown' => 'Извините, точная причина не может быть определена.',
    'unhealthy_backup_found_full' => 'Резервные копии используют слишком много памяти. Используется :disk_usage что выше допустимого предела: :disk_limit.',
];
