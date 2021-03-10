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
    <div class="cc-tab">
        <div class="form-group">
            <form-check name="cc" label="Parallel findet ein Kindergottesdienst statt." v-model="service.cc"/>
        </div>
        <div class="row">
            <div class="col-md-6">
                <form-input name="cc_alt_time" label="Vom Gottesdienst abweichende Uhrzeit"
                            :help="'Leer lassen, wenn der Kindergottesdienst zur selben Zeit ('+myService.time+' Uhr) beginnt.'"
                            placeholder="HH:MM"
                            v-model="myService.cc_alt_time"
                            :disabled="disabled"
                />
            </div>
            <div class="col-md-6">
                <form-input name="cc_location" label="Ort der Kinderkirche"
                            :help="service.location.cc_default_location ? 'Leer lassen für: '+ service.location.cc_default_location : ''"
                            v-model="myService.cc_location" :disabled="disabled"/>
            </div>
        </div>
        <hr/>
        <div class="form-group">
            <form-textarea name="cc_lesson" label="Thema des Kindergottesdiensts" v-model="myService.cc_lesson"
                           :disabled="disabled"/>
        </div>
        <hr/>
        <div class="form-group">
            <form-input name="cc_staff" label="Mitarbeitende"
                        v-model="myService.cc_staff" :disabled="disabled"/>
        </div>
    </div>
</template>

<script>
import FormInput from "../../Ui/forms/FormInput";
import FormTextarea from "../../Ui/forms/FormTextarea";
import FormRadioGroup from "../../Ui/forms/FormRadioGroup";
import FormCheck from "../../Ui/forms/FormCheck";

export default {
    name: "CCTab",
    components: {
        FormCheck,
        FormRadioGroup,
        FormTextarea,
        FormInput,
    },
    props: {
        service: Object,
    },
    computed: {
        disabled() {
            return (this.myService.cc == false) || (this.myService.cc == 0);
        }
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
