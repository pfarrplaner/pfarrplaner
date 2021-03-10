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

export function BlockType() {
    this.title = 'Abschnitt';
    this.typeDescription = 'Abschnitt';
    this.type = 'block';
    this.items = [];
}

export function FreetextType() {
    this.title = 'Freier Text';
    this.typeDescription = 'Freitext';
    this.type= 'freetext';
    this.description = '';
}

export function LiturgicType() {
    this.title = 'Lit. Element';
    this.typeDescription = 'Lit. Element';
    this.type= 'liturgic';
}

export function PsalmType() {
    this.title = 'Psalmgebet';
    this.typeDescription = 'Psalmgebet';
    this.type= 'psalm';
}

export function ReadingType() {
    this.title = 'Schriftlesung';
    this.typeDescription = 'Schriftlesung';
    this.type= 'reading';
}

export function SongType() {
    this.title = 'Lied';
    this.typeDescription = 'Lied';
    this.type= 'song';
}

export function SermonType() {
    this.title = 'Predigt';
    this.typeDescription = 'Predigt';
    this.type= 'sermon';
}

