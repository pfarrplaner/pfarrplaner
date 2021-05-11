<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
  - @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
  - @link https://github.com/potofcoffee/pfarrplaner
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
    <div class="seat" :style="{
        backgroundColor: seat.color,
        padding: (icon == 'fa-couch') ? '1px 30px 1px 30px' : '1px 3px',
        fontWeight: booking.fixed_seat ? 'bold' : 'normal',
        fontStyle: booking.fixed_seat ? 'normal' : 'italic',
    }">
        <span class="fa" :class="[icon]"></span> {{ seat.title }}
    </div>
</template>

<script>
export default {
    name: "Seat",
    props: ['seat', 'booking'],
    computed: {
        icon() {
            return (!isNaN(this.seat.title)) && (this.seat.seats > 1) ? 'fa-couch' : 'fa-chair';
        }
    }
}
</script>

<style scoped>
    div.seat {
        display: inline-block;
        border: solid 1px darkgray; border-radius: 3px;
        box-shadow: darkgray 0px 0px 1px 1px;
        margin-right: 3px;
        margin-bottom: 3px;
    }
</style>
