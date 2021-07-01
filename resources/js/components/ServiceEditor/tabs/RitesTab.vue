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
    <div class="rites-tab">
        <div v-if="myService.weddings.length > 0">
            <h3>Trauungen</h3>
            <wedding class="rite-wrapper" :class="{first: index != 0}"
                     v-for="(wedding,key,index) in myService.weddings" :key="key" :wedding="wedding" />
        </div>
        <div v-if="myService.baptisms.length > 0">
            <h3>Taufen</h3>
            <baptism class="rite-wrapper" :class="{first: index != 0}"
                     v-for="(baptism,key,index) in myService.baptisms" :key="key" :baptism="baptism"/>
        </div>
        <div v-if="myService.funerals.length > 0">
            <h3>Bestattungen</h3>
            <funeral class="rite-wrapper" :class="{first: index != 0}"
                     v-for="(funeral,key,index) in myService.funerals" :key="key" :funeral="funeral"/>
        </div>
        <div v-if="myService.weddings.length == myService.baptisms.length == myService.funerals.length == 0">
            Für diesen Gottesdienst sind noch keine Kasualien eingetragen.
        </div>
        <hr />
        <div id="rites-buttons">
            <a class="btn btn-default btn-light btn-rite" :href="route('wedding.add', service.id)">Trauung hinzufügen</a>
            <inertia-link class="btn btn-default btn-light btn-rite" :href="route('baptism.add', service.id)">Taufe hinzufügen</inertia-link>
            <a class="btn btn-default btn-light btn-rite" :href="route('funeral.add', service.id)">Bestattung hinzufügen</a>
        </div>
    </div>
</template>

<script>
import FormInput from "../../Ui/forms/FormInput";
import FormTextarea from "../../Ui/forms/FormTextarea";
import FormRadioGroup from "../../Ui/forms/FormRadioGroup";
import FormCheck from "../../Ui/forms/FormCheck";
import FormGroup from "../../Ui/forms/FormGroup";
import CheckedProcessItem from "../../Ui/elements/CheckedProcessItem";
import Baptism from "../rites/Baptism";
import Wedding from "../rites/Wedding";
import Funeral from "../rites/Funeral";

export default {
    name: "RitesTab",
    components: {
        Wedding,
        Baptism,
        Funeral,
        CheckedProcessItem,
        FormGroup,
        FormCheck,
        FormRadioGroup,
        FormTextarea,
        FormInput,
    },
    props: {
        service: Object,
    },
    data() {
        return {
            myService: this.service,
        }
    },
    methods: {
        editBaptism() {},
    }
}
</script>

<style scoped>
.rite-wrapper {
    border-top: solid 1px #c7c7c7;
    padding-top: 0.25rem;
}

.rite-wrapper.first {
    border-top: 0;
    padding-top: 0;
}

</style>
