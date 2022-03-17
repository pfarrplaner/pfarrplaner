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
    <admin-layout title="Gottesdienstliste für den Newsletter">
        <form-textarea label="HTML-Code zum Kopieren" v-model="html" rows="20"/>
        <nav-button type="light" icon="mdi mdi-content-copy" force-icon
                    @click="copyToClipboard"
                    title="In die Zwischenablage kopieren">Kopieren</nav-button>
    </admin-layout>
</template>

<script>
import FormTextarea from "../../../components/Ui/forms/FormTextarea";
import NavButton from "../../../components/Ui/buttons/NavButton";
export default {
    name: "Render",
    components: {NavButton, FormTextarea},
    props: ['html'],
    methods: {
        copyToClipboard() {
            const cb = navigator.clipboard;
            cb.writeText(this.html).then(result => {});
        }
    },
}
</script>

<style scoped>

</style>
