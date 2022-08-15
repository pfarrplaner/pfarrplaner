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
    <a class="btn" :class="'btn-'+type" :title="title" @click.prevent.stop="handleClick" :disabled="disabled">
        <span v-if="icon" :class="forceIcon ? icon : 'd-inline d-md-none '+icon"></span>
        <span v-if="!forceNoText" :class="icon ? 'd-none d-md-inline' : ''"><slot /></span>
    </a>
</template>

<script>
export default {
    name: "NavButton",
    props: {
        type: {
            type: String,
            default: 'light',
        },
        icon: String,
        title: String,
        disabled: Boolean,
        forceIcon: Boolean,
        forceNoText: Boolean,
        href: String,
    },
    methods: {
        handleClick() {
            if (this.href) {
                this.$inertia.get(this.href);
            } else {
                this.$emit('click');
            }
        },
    }
}
</script>

<style scoped>

</style>
