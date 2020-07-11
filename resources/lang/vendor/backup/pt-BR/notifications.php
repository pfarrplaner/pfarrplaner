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
    'exception_message' => 'Exception message: :message',
    'exception_trace' => 'Exception trace: :trace',
    'exception_message_title' => 'Exception message',
    'exception_trace_title' => 'Exception trace',

    'backup_failed_subject' => 'Falha no backup da aplicação :application_name',
    'backup_failed_body' => 'Importante: Ocorreu um erro ao fazer o backup da aplicação :application_name',

    'backup_successful_subject' => 'Backup realizado com sucesso: :application_name',
    'backup_successful_subject_title' => 'Backup Realizado com sucesso!',
    'backup_successful_body' => 'Boas notícias, um novo backup da aplicação :application_name foi criado no disco :disk_name.',

    'cleanup_failed_subject' => 'Falha na limpeza dos backups da aplicação :application_name.',
    'cleanup_failed_body' => 'Um erro ocorreu ao fazer a limpeza dos backups da aplicação :application_name',

    'cleanup_successful_subject' => 'Limpeza dos backups da aplicação :application_name concluída!',
    'cleanup_successful_subject_title' => 'Limpeza dos backups concluída!',
    'cleanup_successful_body' => 'A limpeza dos backups da aplicação :application_name no disco :disk_name foi concluída.',

    'healthy_backup_found_subject' => 'Os backups da aplicação :application_name no disco :disk_name estão em dia',
    'healthy_backup_found_subject_title' => 'Os backups da aplicação :application_name estão em dia',
    'healthy_backup_found_body' => 'Os backups da aplicação :application_name estão em dia. Bom trabalho!',

    'unhealthy_backup_found_subject' => 'Importante: Os backups da aplicação :application_name não estão em dia',
    'unhealthy_backup_found_subject_title' => 'Importante: Os backups da aplicação :application_name não estão em dia. :problem',
    'unhealthy_backup_found_body' => 'Os backups da aplicação :application_name no disco :disk_name não estão em dia.',
    'unhealthy_backup_found_not_reachable' => 'O destino dos backups não pode ser alcançado. :error',
    'unhealthy_backup_found_empty' => 'Não existem backups para essa aplicação.',
    'unhealthy_backup_found_old' => 'O último backup realizado em :date é considerado muito antigo.',
    'unhealthy_backup_found_unknown' => 'Desculpe, a exata razão não pode ser encontrada.',
    'unhealthy_backup_found_full' => 'Os backups estão usando muito espaço de armazenamento. A utilização atual é de :disk_usage, o que é maior que o limite permitido de :disk_limit.',
];
