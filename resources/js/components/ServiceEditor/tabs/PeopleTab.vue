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
    <div class="people-tab">
        <div class="row">
            <div class="col-md-4">
                <people-select name="participants[P][]" label="Pfarrer*in" :people="people" :teams="teams"
                               v-model="myService.pastors" :include-teams-from-city="myService.city"
                                @count="updatePeopleCounter" />
                <form-check name="need_predicant"
                            label="Für diesen Gottesdienst wird ein*e Prädikant*in benötigt."
                            v-model="myService.need_predicant" />
            </div>
            <div class="col-md-4">
                <people-select name="participants[O][]" label="Organist*in" :people="people" :teams="teams"
                               v-model="myService.organists"  :include-teams-from-city="myService.city"
                               @count="updatePeopleCounter"  />
            </div>
            <div class="col-md-4">
                <people-select name="participants[M][]" label="Mesner*in" :people="people" :teams="teams"
                               v-model="myService.sacristans" :include-teams-from-city="myService.city"
                               @count="updatePeopleCounter"  />
            </div>
        </div>
        <hr/>
        <div><label><span class="fa fa-users"></span> Weitere Dienste</label></div>
        <div class="row">
            <div class="col-md-6"><label>Dienstbeschreibung</label></div>
            <div class="col-md-6"><label>Eingeteilte Personen</label></div>
        </div>
        <ministry-row v-for="(members,title,index) in myService.ministriesByCategory"
                      :title="title" :members="members" :index="index" :people="people" :teams="teams"
                      :key="'ministry_rows'+Object.entries(myService.ministriesByCategory).length+'_'+index"
                      :ministries="ministries" v-model="myService.ministriesByCategory" @delete="deleteRow"
                      :include-teams-from-city="myService.city"
                      @count="updatePeopleCounter" />
        <button class="btn btn-light btn-sm" @click.prevent.stop="addRow">Reihe hinzufügen</button>
        <hr>
        <div>
            <button class="btn btn-light" title="Liste der Beteiligten ('Credits') kopieren" @click.prevent="copyCredits">
                <span class="fa fa-copy"></span> <span class="d-none d-md-inline">Liste der Beteiligten ("Credits") kopieren</span></button>
            <div v-if="creditsCopied" class="alert alert-info alert-dismissible fade show mt-2">Die Liste der Beteiligten wurde in die Zwischenablage kopiert.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import FormInput from "../../Ui/forms/FormInput";
import LocationSelect from "../../Ui/elements/LocationSelect";
import DaySelect from "../../Ui/elements/DaySelect";
import PeopleSelect from "../../Ui/elements/PeopleSelect";
import FormCheck from "../../Ui/forms/FormCheck";
import MinistryRow from "../../Ui/elements/MinistryRow";

export default {
    name: "PeopleTab",
    components: {
        MinistryRow,
        PeopleSelect,
        DaySelect,
        LocationSelect,
        FormInput,
        FormCheck,
    },
    props: {
        service: Object,
        locations: Array,
        days: Array,
        people: Array,
        ministries: Array,
        teams: Array,
    },
    data() {
        if (!this.service.ministriesByCategory) this.service.ministriesByCategory = {};
        if (this.service.ministriesByCategory.length == 0) this.service.ministriesByCategory = {};

        return {
            myService: this.service,
            myLocation: this.service.location || this.service.special_location,
            locationUpdating: false,
            creditsCopied: false,
        }
    },
    methods: {
        addRow() {
            this.myService.ministriesByCategory['Neuer Dienst'] = [];
            this.$forceUpdate();
        },
        deleteRow(store) {
            this.myService.ministriesByCategory = store;
            this.$forceUpdate();
        },
        copyCredits() {
            var credits = { Liturgie: [], Orgel: [], Mesnerdienst: []};
            this.myService.pastors.forEach(person => {
                credits.Liturgie.push(person.name);
            });
            this.myService.organists.forEach(person => {
                credits.Orgel.push(person.name);
            });
            Object.entries(this.myService.ministriesByCategory).forEach(item => {
                item[1].forEach(person => {
                    if (!credits[item[0]]) credits[item[0]] = [];
                    credits[item[0]].push(person.name);
                });
            })
            this.myService.sacristans.forEach(person => {
                credits.Mesnerdienst.push(person.name);
            });

            var creditsText = [];
            Object.entries(credits).forEach(item => {
                creditsText.push(item[0]+': '+item[1].join(', '));
            });
            const cb = navigator.clipboard;
            cb.writeText(creditsText.join(' · ')).then(result => {
                this.creditsCopied = true;
            });
        },
        updatePeopleCounter() {
            this.$emit('count');
        },
    }
}
</script>

<style scoped>

</style>
