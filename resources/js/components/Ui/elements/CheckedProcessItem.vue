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
    <div>
        <span :class="checkClass(check)" :style="{color: check ? colorPositive : colorNegative}"></span>
        <span v-if="check"><slot name="positive">{{ positive }}</slot></span>
        <span v-else><slot name="negative">{{ negative }}</slot></span>
    </div>
</template>

<script>
export default {
    name: "CheckedProcessItem",
    props: {
        check: {
            type: null,
        },
        positive: String,
        negative: String,
        iconPositive: {
            type: String,
            default: 'mdi mdi-check-circle',
        },
        iconNegative: {
            type: String,
            default: 'mdi mdi-close-circle',
        },
        colorPositive: {
            type: String,
            default: 'green',
        },
        colorNegative: {
            type: String,
            default: 'red',
        }
    },
    methods: {
        checkClass(criterion) {
            return criterion ? this.iconPositive : this.iconNegative;
        },
    }
}
</script>

<style scoped>
</style>
