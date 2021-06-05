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
    <div class="home-tab">
        <div class="row">
            <div class="col-md-6">
                <form-check name="baptism" label="Dies ist ein Taufgottesdienst." v-model="service.baptism"/>
            </div>
            <div class="col-md-6">
                <form-check name="eucharist" label="Dies ist ein Abendmahlsgottesdienst." v-model="service.eucharist"/>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-6">
                <form-input name="title" label="Abweichender Titel" v-model="service.title"
                            help="z.B. für öffentliche Listen"/>
            </div>
            <div class="col-md-6">
                <form-input name="description" label="Anmerkungen" v-model="service.description"
                            help="Wird als Beschreibung in öffentlichen Listen angezeigt"/>
            </div>
        </div>
        <form-textarea name="announcements" label="Zusätzliche Bekanntgaben" v-model="service.announcements"
                       help="Bekanntgaben, die über die automatisch erstellte Terminliste hinausgehen."/>
        <hr/>
        <form-textarea name="internal_remarks" label="Interne Anmerkungen" v-model="service.internal_remarks"
                       help="Diese Anmerkungen werden nirgends veröffentlicht, sondern sind nur hier zu sehen."
                       pre-label="eye-slash"/>
        <div class="row">
            <div class="col-md-6">
                <form-selectize name="tags" label="Kennzeichnungen" v-model="service.tags"
                                help="Kennzeichnungen z.B. für den Gemeindebrief" :items="tags"/>
            </div>
            <div class="col-md-6">
                <form-selectize name="service_groups" label="Dieser Gottesdienst gehört zu folgenden Gruppen"
                                v-model="service.service_groups" help="Gruppen z.B. für den Gemeindebrief"
                                :items="serviceGroups"/>
            </div>
        </div>
    </div>
</template>

<script>
import FormCheck from "../../Ui/forms/FormCheck";
import FormInput from "../../Ui/forms/FormInput";
import FormTextarea from "../../Ui/forms/FormTextarea";
import FormSelectize from "../../Ui/forms/FormSelectize";

export default {
    name: "SpecialTab",
    components: {
        FormSelectize,
        FormTextarea,
        FormInput,
        FormCheck,
    },
    props: {
        service: Object,
        tags: Array,
        serviceGroups: Array,
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
