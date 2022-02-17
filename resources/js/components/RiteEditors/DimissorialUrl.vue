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
    <div class="dimissorial-url">
        <b>Link zum automatischen Erteilen des Dimissoriale:</b> <a :href="url" target="_blank">{{ url }}</a>
        <span class="mdi mdi-content-copy" @click.prevent.stop="copyToClipboard"
              title="Klicken, um den Text in die Zwischenablage zu kopieren"></span>
    </div>
</template>

<script>
export default {
    name: "DimissorialUrl",
    props: ['url'],
    methods: {
        copyToClipboard() {
            const cb = navigator.clipboard;
            cb.writeText(this.url).then(result => {});
        }
    },
}
</script>

<style scoped>
.mdi-content-copy {
    color: lightgray;
    display: none;
}
.mdi-content-copy:hover {
    color: gray;
}

.dimissorial-url:hover .mdi-content-copy {
    display: inline;
}

</style>
