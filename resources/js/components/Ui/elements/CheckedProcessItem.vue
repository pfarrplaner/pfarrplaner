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
    <div>
        <span :class="checkClass(check)"></span>
        <span v-if="check"><slot name="positive">{{ positive }}</slot></span>
        <span v-else><slot name="negative">{{ negative }}</slot></span>
    </div>
</template>

<script>
export default {
    name: "CheckedProcessItem",
    props: ['check', 'positive', 'negative'],
    methods: {
        checkClass(criterion) {
            return criterion ? 'fa fa-check-circle' : 'fa fa-times-circle';
        },
    }
}
</script>

<style scoped>
.fa.fa-check-circle {
    color: green;
}
.fa.fa-times-circle {
    color: red;
}
</style>
