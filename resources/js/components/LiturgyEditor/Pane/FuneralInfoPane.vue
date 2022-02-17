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
    <div class="card">
        <div class="card-header">
            {{ funeral.type }} von {{ funeral.buried_name }}
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-2">Geboren: {{ moment(funeral.dob).locale('de').format('LL') }}</div>
                <div class="col-md-5">Verstorben: {{ dodString }}
                    <span class="mdi mdi-content-copy" @click.prevent.stop="copyToClipboard"
                          title="Klicken, um den Text in die Zwischenablage zu kopieren"></span>
                </div>
                <div class="col-md-2">
                    <bible-reference v-if="funeral.text" :liturgy="liturgy" liturgy-key="funeral" title="Text:"/>
                </div>
                <div class="col-md-3 text-right">
                    <inertia-link class="btn btn-light btn-sm"
                                  :href="route('funerals.edit', funeral.id)"
                                  title="Beerdigung bearbeiten"><span class="mdi mdi-pencil"></span></inertia-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import RelativeDate from "../../../libraries/RelativeDate";
import BibleReference from "../Elements/BibleReference";

export default {
    name: "FuneralInfoPane",
    components: {BibleReference},
    props: ['service', 'funeral'],
    computed: {
        age() {
            if ((!this.funeral.dod) || (!this.funeral.dob)) return null;
            return moment(this.funeral.dod).diff(moment(this.funeral.dob), 'years');
        },
        dodString() {
            if (!this.funeral.dod) return '';
            let dod = moment(this.funeral.dod).locale('de');
            return dod.format('LL') + ' (' + RelativeDate(dod.format('DD.MM.YYYY'), moment().format('DD.MM.YYYY')) + ', ' + this.age+')';
        },
    },
    data() {
        return {
            liturgy: {funeral: this.funeral.text},
        };
    },
    methods: {
        copyToClipboard() {
            if (!this.funeral.dod) return;
            let dod = moment(this.funeral.dod).locale('de');
            const cb = navigator.clipboard;
            cb.writeText(dod.format('LL') + ' = ' + RelativeDate(dod.format('DD.MM.YYYY'), moment(this.service.day.date).format('DD.MM.YYYY')) + ', im Alter von ' + this.age + ' Jahren ');
        }
    }
}
</script>

<style scoped>
.mdi-content-copy {
    color: lightgray;
}
.mdi-content-copy:hover {
    color: gray;
}


</style>
