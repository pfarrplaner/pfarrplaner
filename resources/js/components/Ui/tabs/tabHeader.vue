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
    <li :id="id+'Tab'" class="nav-item">
        <a class="nav-link" :class="{active: active || (activeTab == id)}" :href="'#'+id" role="tab" data-toggle="tab">{{ title }} <span v-if="count && (count > 0)"
            class="badge" :class="badgeClass()">{{ $count }}</span></a>
    </li>

</template>

<script>
export default {
    name: "tabHeader",
    props: ['id', 'active', 'activeTab', 'title', 'count', 'badge_type'],
    methods: {
        badgeClass() {
            return this.badge_type ? 'badge-'+this.badge_type : 'badge-primary';
        }
    }
}
</script>

<style scoped>

</style>
