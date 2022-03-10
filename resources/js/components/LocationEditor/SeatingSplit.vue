<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2022 Christoph Fischer, https://christoph-fischer.org
  - @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
  - @link https://github.com/pfarrplaner/pfarrplaner
  - @version git: $Id$
  -
  - Sponsored by: Evangelischer Kirchenbezirk Balingen, https://www.kirchenbezirk-balingen.de
  -
  - Pfarrplaner is based on the Laravel framework (https://laravel.com).
  - This file may contain code created by Laravel's scaffolding functions.
  -
  - This program is free software: you can redistribute it and/or modify
  - it under the terms of the GNU General Public License as published by
  - the Free Software Foundation, either version 3 of the License, or
  - (at your option) any later version.
  -
  - This program is distributed in the hope that it will be useful,
  - but WITHOUT ANY WARRANTY; without even the implied warranty of
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  - GNU General Public License for more details.
  -
  - You should have received a copy of the GNU General Public License
  - along with this program.  If not, see <http://www.gnu.org/licenses/>.
  -->

<template>
    <div class="seating-split">
        <table v-if="value" class="table table-bordered">
            <tr>
                <td :colspan="divisions.length">
                    <span v-for="index in parseInt(value.seats)" class="mdi mdi-sofa-single"/> ({{ value.seats }})
                </td>
            </tr>
            <tr class="split">
                <td v-for="division in divisions">
                    <span v-for="index in parseInt(division)" class="mdi mdi-sofa-single"/> ({{ division }})
                </td>
            </tr>
        </table>
    </div>
</template>

<script>
export default {
    name: "SeatingSplit",
    props: ['value'],
    computed: {
        divisions() {
            return String(this.value.split).split(',');
        }
    }
}
</script>

<style scoped>
    .table-bordered tr td {
        text-align: center;
    }
    .table-bordered tr.split td:last-child {
        text-align: right;
    }
    .table-bordered tr.split td:first-child {
        text-align: left;
    }
</style>
