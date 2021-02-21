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
            <div class="col-md-6">
                <form-input name="offerings_counter1" label="Opferzähler*in 1"
                            v-model="service.offerings_counter1"/>
            </div>
            <div class="col-md-6">
                <form-input name="offerings_counter2" label="Opferzähler*in 2"
                            v-model="service.offerings_counter2"/>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-6">
                <form-input name="offering_goal" label="Opferzweck" v-model="service.offering_goal"/>
                <form-input name="offering_description" label="Anmerkungen zum Opfer"
                            v-model="service.offering_description" help="Anmerkungen für den Opferplan"/>
                <form-radio-group name="offering_type" label="Opfertyp" v-model="service.offering_type" :items="{
                            '': 'eigener Beschluss',
                            'eO': 'empfohlenes Opfer',
                            'PO': 'Pflichtopfer',
                        }"/>
            </div>
            <div class="col-md-6">
                <form-textarea name="offering_text" label="Text zum Opfer"
                               placeholder="z.B. Brief des Landesbischofs" v-model="offering_text"/>
            </div>
        </div>
        <hr/>
        <form-input name="offering_amount" label="Eingegangene Summe" v-model="service.offering_amount"/>
    </div>
</template>

<script>
import FormInput from "../../Ui/forms/FormInput";
import FormTextarea from "../../Ui/forms/FormTextarea";
import FormRadioGroup from "../../Ui/forms/FormRadioGroup";

export default {
    name: "OfferingsTab",
    components: {
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
    methods: {}
}
</script>

<style scoped>

</style>
