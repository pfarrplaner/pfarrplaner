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
    <admin-layout :title="title">
        <div class="row">
            <div class="col-md-6">
                <p>So bindest du die gewünschte Tabelle in eine TYPO3-Website (Gemeindebaukasten) ein:</p>
                <ol>
                    <li>Lege ein neues Inhaltselement auf der gewünschten Seite an.
                        <br/>
                        <img src="/img/typo3/new-element.png"/>
                        <br/>
                    </li>

                    <li>Wähle den Typ "HTML"
                        <br/>
                        <img src="/img/typo3/new-html-element.png"/>
                        <br/>
                    </li>
                    <li>Kopiere den Code auf der rechten Seite komplett in das Feld "HTML-Code":</li>
                </ol>
            </div>
            <div class="col-md-6">
                <form-textarea label="HTML-Code zum Kopieren" v-model="html" rows="20"/>
                <nav-button type="light" icon="mdi mdi-content-copy" force-icon
                            @click="copyToClipboard"
                            title="In die Zwischenablage kopieren">Kopieren
                </nav-button>
            </div>
        </div>
    </admin-layout>
</template>

<script>

import FormTextarea from "../Ui/forms/FormTextarea";
import NavButton from "../Ui/buttons/NavButton";
export default {
    name: "EmbedCode",
    components: {NavButton, FormTextarea},
    props: ['html', 'title'],
    methods: {
        copyToClipboard() {
            const cb = navigator.clipboard;
            cb.writeText(this.html).then(result => {
            });
        }
    },
}
</script>

<style scoped>

</style>
