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
    <div class="wedding row">
        <div class="col-md-2" v-if="showService">
            {{ moment(wedding.service.day.date).format('DD.MM.YYYY') }}<br />
            {{ wedding.service.timeText }}<br />
            {{ wedding.service.locationText }}
            <div v-if="showPastor">
                <participants :participants="wedding.service.pastors"/>
            </div>
        </div>
        <div class="col-md-2">
            <b>{{ wedding.spouse1_name }}</b><br />
            <small>{{ wedding.spouse1_phone }}<br />{{ wedding.spouse1_email}}</small>
            <br />
            <b>{{ wedding.spouse2_name }}</b><br />
            <small>{{ wedding.spouse2_phone }}<br />{{ wedding.spouse2_email}}</small>
        </div>
        <div class="col-md-4">
            <div>
                <checked-process-item :check="(wedding.appointment)" negative="Traugespräch noch nicht vereinbart">
                    <template slot="positive">
                        Traugespräch am {{ moment(wedding.appointment).locale('de-DE').format('LLLL') }} Uhr
                    </template>
                </checked-process-item>
            </div>
            <div>
                <checked-process-item :check="wedding.registered" positive="Anmeldeformular erstellt" negative="Anmeldeformular noch nicht erstellt" />
                <checked-process-item :check="wedding.signed" positive="Anmeldung unterschrieben" negative="Anmeldung noch nicht unterschrieben" />
            </div>
            <div>
                <checked-process-item :check="wedding.docs_ready" positive="Urkunden erstellt" negative="Urkunden noch nicht erstellt">
                    <template slot="positive">
                        Urkunden erstellt<small v-if="wedding.docs_where"><br />Hinterlegt: {{ wedding.docs_where }}</small>
                    </template>
                </checked-process-item>
            </div>
        </div>
        <div class="col-md-3">
                <attachment  v-for="(attachment,key,index) in wedding.attachments" :key="'attachment'+key" :attachment="attachment" />
        </div>
        <div class="col-md-1 text-right">
            <a class="btn btn-sm btn-light" title="Trauung bearbeiten"
               :href="route('weddings.edit', {wedding: wedding.id})"><span class="fa fa-edit"></span></a>
            <button class="btn btn-sm btn-danger" title="Trauung löschen"
                    @click.prevent="deleteWedding"><span class="fa fa-trash"></span></button>
        </div>
    </div>

</template>

<script>
import CheckedProcessItem from "../../Ui/elements/CheckedProcessItem";
import Attachment from "../../Ui/elements/Attachment";
import DetailsInfo from "../../Service/DetailsInfo";
import Participants from "../../Calendar/Service/Participants";

export default {
    name: "Wedding",
    components: {
        Participants,
        DetailsInfo,
        Attachment,
        CheckedProcessItem,
    },
    props: ['wedding', 'showService', 'showPastor'],
    methods: {
        deleteWedding() {
            this.$inertia.delete(route('weddings.destroy', {wedding: this.wedding.id}), {preserveState: false});
        }
    }

}
</script>

<style scoped>
</style>
