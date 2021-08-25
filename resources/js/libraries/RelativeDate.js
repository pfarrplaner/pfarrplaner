/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
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

import moment from "moment/moment";

function relative(date1, date2) {
    if (!date1) return '';
    if (!date2) return '';

    let dayDiff = date2.diff(date1, 'days');
    if (isNaN(dayDiff)) return '';

    if (dayDiff == 0) return 'heute';
    if (dayDiff == 1) return 'gestern';
    if (dayDiff == 2) return 'vorgestern';
    if (dayDiff < 6) return 'am '+date1.format('dddd');
    if (dayDiff < 13) return 'letzten '+date1.format('dddd');
    return 'am '+date1.format('dddd')+' vor '+Math.floor(dayDiff / 7)+' Wochen';
}


export default function (date1, date2) {
    return relative(moment(date1, 'DD.MM.YYYY').locale('de'), moment(date2, 'DD.MM.YYYY').locale('de'));
}

