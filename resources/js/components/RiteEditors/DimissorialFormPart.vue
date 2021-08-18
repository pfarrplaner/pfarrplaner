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
    <fieldset class="dimissorial-form-part">
        <legend>Dimissoriale</legend>
        <div class="row">
            <div class="col-md-3">
                <form-check name="needs_dimissorial" label="Dimissoriale benötigt"
                            v-model="needed"/>
            </div>
            <div class="col-md-3" v-if="needed">
                <form-input name="dimissorial_issuer" label="Zuständiges Pfarramt" v-model="parent.dimissorial_issuer"/>
            </div>
            <div class="col-md-3" v-if="needed">
                <form-date-picker name="dimissorial_requested" label="Beantragt" v-model="parent.dimissorial_requested"
                                  :is-checked-item="true" />
            </div>
            <div class="col-md-3" v-if="needed">
                <form-date-picker name="dimissorial_requested" label="Erhalten" v-model="parent.dimissorial_received"
                                  :is-checked-item="true" />
            </div>
        </div>
        <dimissorial-url v-if="needed" :url="parent.dimissorialUrl" />
    </fieldset>
</template>

<script>
import FormCheck from "../Ui/forms/FormCheck";
import FormInput from "../Ui/forms/FormInput";
import FormDatePicker from "../Ui/forms/FormDatePicker";
import DimissorialUrl from "./DimissorialUrl";

export default {
    name: "DimissorialFormPart",
    components: {DimissorialUrl, FormDatePicker, FormInput, FormCheck},
    props: ['parent'],
    watch: {
        needed: function (newV, oldV) {
            console.log('needed', newV, oldV);
            this.myParent.needs_dimissorial = newV;
        },
    },
    data() {
        var myParent = this.parent;
        if (myParent.dimissorial_requested) myParent.dimissorial_requested = moment(myParent.dimissorial_requested).format('DD.MM.YYYY');
        if (myParent.dimissorial_received) myParent.dimissorial_received = moment(myParent.dimissorial_received).format('DD.MM.YYYY');

        return {
            myParent: myParent,
            needed: this.parent.needs_dimissorial,
        }
    }
}
</script>

<style scoped>

</style>
