<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
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
    <li :id="id+'Tab'" class="nav-item" :title="disabled ? disabledTitle : ''">
        <span v-if="(!title) && (!icon)" class="nav-link fa fa-spin fa-spinner"></span>
        <a v-else class="nav-link" :class="{active: (active || (activeTab == id)) && !disabled, disabled: disabled}" :href="href || ('#'+id)" role="tab" data-toggle="tab">
            <span v-if="icon" :class="icon"></span>
            {{ title }}
            <span v-if="count && (count > 0) && (!disabled)" class="badge" :class="badgeClass()">{{ count }}</span>
            <value-check v-if="isCheckedItem" :value="checkValue" />
        </a>
    </li>

</template>

<script>
import ValueCheck from "../elements/ValueCheck";
export default {
    name: "tabHeader",
    components: {ValueCheck},
    props: ['id', 'active', 'activeTab', 'title', 'count', 'badgeType', 'icon', 'href', 'isCheckedItem', 'checkValue', 'disabled', 'disabledTitle'],
    methods: {
        badgeClass() {
            return this.badgeType ? 'badge-'+this.badgeType : 'badge-primary';
        }
    }
}
</script>

<style scoped>

</style>
