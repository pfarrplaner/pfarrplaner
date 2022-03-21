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
    <admin-layout title="Mit Outlook verbinden">
        <form-selectize label="Welche Art von Kalender möchtest du mit Outlook verbinden?"
                        :options="calendarLinks" v-model="myType"/>
        <div v-if="link.needs.includes('cities')" :key="link.id">
            <form-selectize label="Daten für folgende Kirchengemeinden mit einschließen"
                            :options="cities" v-model="myForm.cities" multiple/>
        </div>
        <div v-if="link.needs.includes('includeHidden')" :key="link.id">
            <form-check label="Versteckte Gottesdienste mit einbeziehen"
                        v-model="myForm.includeHidden" />
        </div>
        <hr />
        <ol class="steps">
            <li>Öffne Microsoft Outlook und wechsle zur Kalenderansicht.</li>
            <li>Wähle "Start &gt; Kalender öffnen &gt; Aus dem Internet..."</li>
            <li>Gib als Speicherort folgenden Link ein:<br>
                <div class="input-group mb-3">
                    <input id="link" type="text" class="form-control" placeholder="Link zum Kalenderexport"
                           aria-label="Link zum Kalenderexport" aria-describedby="basic-addon2"
                           :value="url">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button"
                                @click="copyToClipboard"
                                title="In die Zwischenablage kopieren">
                            <span class="fa fa-clipboard"></span></button>
                    </div>
                </div>
            </li>
            <li>Klicke auf "Ok".</li>
        </ol>
        <p>Der Kalender erscheint nun wie gewohnt in der Kalenderansicht.</p>
    </admin-layout>
</template>

<script>
import FormSelectize from "../../components/Ui/forms/FormSelectize";
import FormCheck from "../../components/Ui/forms/FormCheck";

export default {
    name: "Connect",
    props: ['calendarLinks', 'cities'],
    components: {FormCheck, FormSelectize},
    computed: {
        link() {
            return this.calendarLinks.filter(item => {
                return item.id === this.myType
            })[0];
        },
        url() {
            let data = {
                user: this.$page.props.currentUser.data.id,
                token: this.$page.props.currentUser.data.api_token,
                key: this.link.id,
            };
            if (this.link.needs.includes('cities')) {
                data['cities'] = this.myForm.cities.join(',');
            }
            if (this.link.needs.includes('includeHidden')) {
                data['includeHidden'] = this.myForm.includeHidden ? 1: 0;
            }
            return route('ical.export', data);
        }
    },
    data() {
        return {
            myType: this.calendarLinks.length ? this.calendarLinks[0].id : null,
            myForm: {
                cities: this.cities.length ? [this.cities[0].id] : null,
                includeHidden: false,
            }
        }
    },
    methods: {
        copyToClipboard() {
            const cb = navigator.clipboard;
            cb.writeText(this.url).then(result => {
            });
        }
    },
}
</script>

<style scoped>

</style>
