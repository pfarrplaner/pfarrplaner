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
    'exception_message' => 'Message de l\'exception : :message',
    'exception_trace' => 'Trace de l\'exception : :trace',
    'exception_message_title' => 'Message de l\'exception',
    'exception_trace_title' => 'Trace de l\'exception',

    'backup_failed_subject' => 'Échec de la sauvegarde de :application_name',
    'backup_failed_body' => 'Important : Une erreur est survenue lors de la sauvegarde de :application_name',

    'backup_successful_subject' => 'Succès de la sauvegarde de :application_name',
    'backup_successful_subject_title' => 'Sauvegarde créée avec succès !',
    'backup_successful_body' => 'Bonne nouvelle, une nouvelle sauvegarde de :application_name a été créée avec succès sur le disque nommé :disk_name.',

    'cleanup_failed_subject' => 'Le nettoyage des sauvegardes de :application_name a echoué.',
    'cleanup_failed_body' => 'Une erreur est survenue lors du nettoyage des sauvegardes de :application_name',

    'cleanup_successful_subject' => 'Succès du nettoyage des sauvegardes de :application_name',
    'cleanup_successful_subject_title' => 'Sauvegardes nettoyées avec succès !',
    'cleanup_successful_body' => 'Le nettoyage des sauvegardes de :application_name sur le disque nommé :disk_name a été effectué avec succès.',

    'healthy_backup_found_subject' => 'Les sauvegardes pour :application_name sur le disque :disk_name sont saines',
    'healthy_backup_found_subject_title' => 'Les sauvegardes pour :application_name sont saines',
    'healthy_backup_found_body' => 'Les sauvegardes pour :application_name sont considérées saines. Bon travail !',

    'unhealthy_backup_found_subject' => 'Important : Les sauvegardes pour :application_name sont corrompues',
    'unhealthy_backup_found_subject_title' => 'Important : Les sauvegardes pour :application_name sont corrompues. :problem',
    'unhealthy_backup_found_body' => 'Les sauvegardes pour :application_name sur le disque :disk_name sont corrompues.',
    'unhealthy_backup_found_not_reachable' => 'La destination de la sauvegarde n\'est pas accessible. :error',
    'unhealthy_backup_found_empty' => 'Il n\'y a aucune sauvegarde pour cette application.',
    'unhealthy_backup_found_old' => 'La dernière sauvegarde du :date est considérée trop vieille.',
    'unhealthy_backup_found_unknown' => 'Désolé, une raison exacte ne peut être déterminée.',
    'unhealthy_backup_found_full' => 'Les sauvegardes utilisent trop d\'espace disque. L\'utilisation actuelle est de :disk_usage alors que la limite autorisée est de :disk_limit.',
];
