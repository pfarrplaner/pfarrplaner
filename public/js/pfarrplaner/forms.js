/*
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

$(document).ready(function () {
    $('.fancy-selectize').selectize();

    $('.location-select').selectize({
        create: true,
        render: {
            option_create: function (data, escape) {
                return '<div class="create">Freie Ortsangabe: <strong>' + escape(data.input) + '</strong>&hellip;</div>';
            }
        },
    });

    $('.form-check-input').change(function(){
        var name = '"'+$(this).attr('name').replace('_check', '')+'"';
        var checked = this.checked;
        var value = checked ? 1 : 0;
        $('input[name='+name+']').val(value);
    });


    $('.datepicker').datepicker({
        format: 'dd.mm.yyyy',
        language: 'de',
    });

    $('.datetimepicker').each(function () {
        var value = $(this).val();
        $(this).datetimepicker({
            locale: 'de',
            format: 'DD.MM.Y HH:mm',
            placeholder: 'TT.MM.JJJJ HH:MM',
            icons: {
                time: 'fa fa-clock',
                date: 'fa fa-calendar',
                up: 'fa fa-arrow-up',
                down: 'fa fa-arrow-down',
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right',
                today: 'fa fa-calendar-check-o',
                clear: 'fa fa-trash',
                close: 'fa fa-times'
            },
            date: value,
        });
        $(this).val(value);

    });
});
