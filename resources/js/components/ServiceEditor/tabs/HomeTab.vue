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
    <div class="home-tab">
        <div class="row">
            <div class="col-md-4">
                <day-select name="day.id" label="Tag" v-model="myService.day" :city="myService.city"
                            @input="myService.day_id = myService.day.id" :days="days"/>
            </div>
            <div class="col-md-4">
                <location-select name="location_id" label="Ort" v-model="myService.location"
                                 :locations="locations" @input="myService.location_id = myService.location.id"/>
            </div>
            <div class="col-md-4">
                <form-input name="time" label="Uhrzeit" v-model="myService.time" placeholder="HH:MM"/>
            </div>
        </div>
        <hr/>
        <h3>Beteiligte Personen</h3>
        <div class="row">
            <div class="col-md-4">
                <people-select name="participants[P][]" label="Pfarrer*in" :people="people"
                               v-model="myService.pastors"/>
                <form-check name="need_predicant"
                            label="Für diesen Gottesdienst wird ein*e Prädikant*in benötigt."
                            v-model="myService.need_predicant"/>
            </div>
            <div class="col-md-4">
                <people-select name="participants[O][]" label="Organist*in" :people="people"
                               v-model="myService.organists"/>
            </div>
            <div class="col-md-4">
                <people-select name="participants[M][]" label="Mesner*in" :people="people"
                               v-model="myService.sacristans"/>
            </div>
        </div>
        <hr/>
        <div><label><span class="fa fa-users"></span> Weitere Dienste</label></div>
        <div class="row">
            <div class="col-md-6"><label>Dienstbeschreibung</label></div>
            <div class="col-md-6"><label>Eingeteilte Personen</label></div>
        </div>
        <ministry-row v-for="(members,title,index) in myService.ministriesByCategory"
                      :title="title" :members="members" :index="index" :people="people" :key="index"
                      :ministries="ministries" v-model="myService.ministriesByCategory" @delete="deleteRow"
        />
        <button class="btn btn-light btn-sm" @click.prevent.stop="addRow">Reihe hinzufügen</button>
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
    name: "HomeTab",
    components: {
        MinistryRow,
        FormCheck,
        PeopleSelect,
        DaySelect,
        LocationSelect,
        FormInput,
    },
    props: {
        service: Object,
        locations: Array,
        days: Array,
        people: Array,
        ministries: Array,
    },
    data() {
        return {
            myService: this.service,
        }
    },
    methods: {
        addRow() {
            this.myService.ministriesByCategory[''] = [];
            this.$forceUpdate();
        },
        deleteRow(store) {
            this.myService.ministriesByCategory = store;
            this.$forceUpdate();
        }
    }
}
</script>

<style scoped>

</style>
