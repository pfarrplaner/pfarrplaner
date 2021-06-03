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
    <div class="baptism row">
        <div class="col-md-2" v-if="showService">
            {{ moment(baptism.service.day.date).format('DD.MM.YYYY') }}<br />
            {{ baptism.service.timeText }}<br />
            {{ baptism.service.locationText }}
        </div>
        <div class="col-md-2">
            {{ baptism.candidate_name}}<br />
            <small>Fon {{ baptism.candidate_phone }}<br />E-Mail {{ baptism.candidate_email}}</small>
        </div>
        <div class="col-md-4">
            <checked-process-item :check="(baptism.first_contact_on) && (baptism.first_contact_with)"
                                  negative="Erstkontakt nicht dokumentiert">
                <template slot="positive">
                    Erstkontakt dokumentiert:
                    <span v-if="baptism.first_contact_on">{{ moment(baptism.first_contact_on).format('DD.MM.YYYY') }}, </span>
                    {{ baptism.first_contact_with }}
                </template>
            </checked-process-item>
            <div>
                <checked-process-item :check="(baptism.appointment)" negative="Taufgespräch noch nicht vereinbart">
                    <template slot="positive">
                        Taufgespräch am {{ moment(baptism.appointment).locale('de-DE').format('LLLL') }} Uhr
                    </template>
                </checked-process-item>
            </div>
            <div>
                <checked-process-item :check="baptism.hasRegistrationForm" positive="Anmeldung aufgenommen und Anmeldeformular erstellt" negative="Anmeldeformular noch nicht erstellt" />
                <checked-process-item :check="baptism.signed" positive="Anmeldung unterschrieben" negative="Anmeldung noch nicht unterschrieben" />
            </div>
            <div>
                <checked-process-item :check="baptism.docs_ready" positive="Urkunden erstellt" negative="Urkunden noch nicht erstellt">
                    <template slot="positive">
                        Urkunden erstellt<small v-if="baptism.docs_where"><br />Hinterlegt: {{ baptism.docs_where }}</small>
                    </template>
                </checked-process-item>
            </div>
        </div>
        <div class="col-md-3">
            <div v-if="!showPastor">
            <attachment  v-for="(attachment,key,index) in baptism.attachments" :key="'attachment'+key" :attachment="attachment" />
            </div>
            <div v-else>
                <calendar-service-participants :participants="baptism.service.pastors" category="P" :predicant="baptism.service.need_predicant" />
            </div>
        </div>
        <div class="col-md-1 text-right">
            <inertia-link class="btn btn-sm btn-light" title="Taufe bearbeiten"
               :href="route('baptisms.edit', {baptism: baptism.id})"><span class="fa fa-edit"></span></inertia-link>
            <button class="btn btn-sm btn-danger" title="Taufe löschen"
                    @click.prevent="deleteBaptism"><span class="fa fa-trash"></span></button>
        </div>
    </div>

</template>

<script>
import CheckedProcessItem from "../../Ui/elements/CheckedProcessItem";
import Attachment from "../../Ui/elements/Attachment";
import DetailsInfo from "../../Service/DetailsInfo";

export default {
    name: "Baptism",
    components: {
        DetailsInfo,
        Attachment,
        CheckedProcessItem,
    },
    props: {
        baptism: Object,
        showService: {
            type: Boolean,
            default: false,
        },
        showPastor: {
            type: Boolean,
            default: false,
        },
    },
    methods: {
        deleteBaptism() {
            this.$inertia.delete('baptisms.destroy', {baptism: this.baptism.id});
        }
    }

}
</script>

<style scoped>
</style>
