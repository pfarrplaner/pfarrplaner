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
    'exception_message' => 'Mensaje de la excepción: :message',
    'exception_trace' => 'Traza de la excepción: :trace',
    'exception_message_title' => 'Mensaje de la excepción',
    'exception_trace_title' => 'Traza de la excepción',

    'backup_failed_subject' => 'Copia de seguridad de :application_name fallida',
    'backup_failed_body' => 'Importante: Ocurrió un error al realizar la copia de seguridad de :application_name',

    'backup_successful_subject' => 'Se completó con éxito la copia de seguridad de :application_name',
    'backup_successful_subject_title' => '¡Nueva copia de seguridad creada con éxito!',
    'backup_successful_body' => 'Buenas noticias, una nueva copia de seguridad de :application_name fue creada con éxito en el disco llamado :disk_name.',

    'cleanup_failed_subject' => 'La limpieza de copias de seguridad de :application_name falló.',
    'cleanup_failed_body' => 'Ocurrió un error mientras se realizaba la limpieza de copias de seguridad de :application_name',

    'cleanup_successful_subject' => 'La limpieza de copias de seguridad de :application_name se completó con éxito',
    'cleanup_successful_subject_title' => '!Limpieza de copias de seguridad completada con éxito!',
    'cleanup_successful_body' => 'La limpieza de copias de seguridad de :application_name en el disco llamado :disk_name se completo con éxito.',

    'healthy_backup_found_subject' => 'Las copias de seguridad de :application_name en el disco :disk_name están en buen estado',
    'healthy_backup_found_subject_title' => 'Las copias de seguridad de :application_name están en buen estado',
    'healthy_backup_found_body' => 'Las copias de seguridad de :application_name se consideran en buen estado. ¡Buen trabajo!',

    'unhealthy_backup_found_subject' => 'Importante: Las copias de seguridad de :application_name están en mal estado',
    'unhealthy_backup_found_subject_title' => 'Importante: Las copias de seguridad de :application_name están en mal estado. :problem',
    'unhealthy_backup_found_body' => 'Las copias de seguridad de :application_name en el disco :disk_name están en mal estado.',
    'unhealthy_backup_found_not_reachable' => 'No se puede acceder al destino de la copia de seguridad. :error',
    'unhealthy_backup_found_empty' => 'No existe ninguna copia de seguridad de esta aplicación.',
    'unhealthy_backup_found_old' => 'La última copia de seguriad hecha en :date es demasiado antigua.',
    'unhealthy_backup_found_unknown' => 'Lo siento, no es posible determinar la razón exacta.',
    'unhealthy_backup_found_full' => 'Las copias de seguridad  están ocupando demasiado espacio. El espacio utilizado actualmente es :disk_usage el cual es mayor que el límite permitido de :disk_limit.',
];
