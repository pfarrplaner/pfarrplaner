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
    <div class="funeral row">
        <div class="col-md-2" v-if="showService">
            {{ moment(funeral.service.day.date).format('DD.MM.YYYY') }}<br/>
            {{ funeral.service.timeText }}<br/>
            {{ funeral.service.locationText }}
            <div v-if="showPastor">
                <participants :participants="funeral.service.pastors"/>
            </div>
        </div>
        <div class="col-md-2">
            <b>{{ funeral.buried_name }}</b><br/>
            <div v-if="funeral.dob && funeral.dod" style="font-size: .8em;">
                {{ moment(funeral.dob).format('DD.MM.YYYY') }} - {{ moment(funeral.dod).format('DD.MM.YYYY') }}
                ({{ funeral.age }})
            </div>
            <div style="font-size: .8em;">
                {{ funeral.type }}
            </div>
        </div>
        <div class="col-md-4">
            <div>
                <checked-process-item :check="(funeral.appointment)" negative="Trauergespräch noch nicht vereinbart">
                    <template slot="positive">
                        <a :href="route('funeral.appointment.ical', funeral)" title="In den Kalender übernehmen">
                            <span class="fa fa-calendar"></span> Trauergespräch am
                            {{ moment(funeral.appointment).locale('de-DE').format('LLLL') }} Uhr
                        </a>
                    </template>
                </checked-process-item>
                <checked-process-item :check="(funeral.text)" negative="Predigttext noch nicht eingetragen">
                    <template slot="positive">
                        Predigttext: {{ funeral.text }}
                    </template>
                </checked-process-item>
                <checked-process-item :check="(funeral.announcement)"
                                      negative="Abkündigungstermin noch nicht festgelegt">
                    <template slot="positive">
                        Abkündigung im GD am {{ moment(funeral.announcement).locale('de-DE').format('LL') }}
                    </template>
                </checked-process-item>
            </div>
        </div>
        <div class="col-md-3">
            <file-drag-receiver multi
                                v-model="myFuneral.attachments"
                                :upload-route="route('funeral.attach', funeral.id)" :key="Object.keys(myFuneral.attachments).length">
                    <attachment v-for="(attachment,key,index) in funeral.attachments" :key="'attachment_'+key"
                                :attachment="attachment"/>
                    <fake-attachment :href="route('funeral.form', {funeral: funeral.id})"
                                     title="Formular für Kirchenregisteramt" extension="pdf"
                                     icon="fa-file-pdf" size="ca. 135 kB"/>
            </file-drag-receiver>
        </div>
        <div class="col-md-1 text-right">
            <a class="btn btn-sm btn-light" title="Bestattung bearbeiten"
               :href="route('funerals.edit', {funeral: funeral.id})"><span class="fa fa-edit"></span></a>
            <button class="btn btn-sm btn-danger" title="Bestattung löschen"
                    @click.prevent="deleteFuneral"><span class="fa fa-trash"></span></button>
        </div>
    </div>

</template>

<script>
import CheckedProcessItem from "../../Ui/elements/CheckedProcessItem";
import Attachment from "../../Ui/elements/Attachment";
import DetailsInfo from "../../Service/DetailsInfo";
import Participants from "../../Calendar/Service/Participants";
import FakeAttachment from "../../Ui/elements/FakeAttachment";
import FileDragReceiver from "../../Ui/elements/FileDragReceiver";
import AttachmentList from "../../Ui/elements/AttachmentList";

export default {
    name: "Funeral",
    components: {
        AttachmentList,
        FileDragReceiver,
        FakeAttachment,
        Participants,
        DetailsInfo,
        Attachment,
        CheckedProcessItem,
    },
    props: ['funeral', 'showService', 'showPastor'],
    data() {
        return {
            myFuneral : this.funeral,
        }
    },
    methods: {
        deleteFuneral() {
            this.$inertia.delete(route('funerals.destroy', {funeral: this.funeral.id}), {preserveState: false});
        },
        downloadForm() {
            window.location.href = route('funeral.form', {funeral: this.funeral.id});
        }
    }

}
</script>

<style scoped>
.attachment {
    width: 100%;
    text-align: left;
    margin-bottom: .25rem;
    vertical-align: middle;
}

.fa-download {
    margin-right: 20px;
    color: gray;
}
</style>
