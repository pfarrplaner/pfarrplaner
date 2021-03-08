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
    <div class="attachments-tab">
        <h3>Angehängte Dateien</h3>
        <div v-if="service.attachments.length > 0">
            <attachment v-for="(attachment,key,index) in service.attachments" :key="key" :attachment="attachment"
            @delete-attachment="deleteAttachment(attachment, key)" allow-delete />
        </div>
        <div v-else>Zu diesem Gottesdienst gehören keine Dateianhänge.</div>
        <hr/>
        <h3>Automatisch bereitgestellte Dateien</h3>
        <div v-if="myService.liturgy_blocks.length > 0">
            <div class="liturgy-sheet btn btn-light" v-for="(sheet,key,index) in liturgySheets" :key="key"
                 @click.prevent="downloadSheet(sheet)"
                 v-if="!sheet.isNotAFile">
                <b><span :class="sheet.icon"></span> {{ sheet.title }}</b><br/>
                <small>.{{ sheet.extension }}, Größe unbekannt</small>
                <span class="float-right fa fa-download"></span>
            </div>
        </div>
        <div v-else>
            Zu diesem Gottesdienst gibt es noch keine automatisch bereitgestellten Dateien, weil noch keine
            <inertia-link :href="route('services.liturgy.editor', {service: service.id})">Liturgie</inertia-link>
            angelegt wurde.
        </div>
        <hr/>
        <h3>Dateien hinzufügen</h3>
        <div :key="myService.newAttachments.length">
            <div class="row" v-for="(attachment,key,index) in myService.newAttachments" :key="key">
                <div class="col-md-6">
                    <form-input :name="'attachment_text['+key+']'" label="" placeholder="Beschreibung der Datei"
                                v-model="attachment.text"/>
                </div>
                <div class="col-md-6">
                    <form-input :name="'attachment_text['+key+']'" label="" placeholder="Beschreibung der Datei"
                                v-model="attachment.file" type="file"/>
                </div>
            </div>
        </div>
        <form-group>
            <button class="btn btn-sm btn-light" @click.prevent="newRow">Reihe hinzufügen</button>
        </form-group>
    </div>
</template>

<script>
import Attachment from "../../Ui/elements/Attachment";
import FormInput from "../../Ui/forms/FormInput";
import FormGroup from "../../Ui/forms/FormGroup";

export default {
    name: "AttachmentsTab",
    components: {
        FormGroup,
        FormInput,
        Attachment
    },
    props: {
        service: Object,
        liturgySheets: Object,
    },
    data() {
        var myService = this.service;
        if (undefined == myService.newAttachments) myService.newAttachments = [{
            text: '',
            file: '',
        }];
        if (undefined == myService.removeAttachments) myService.removeAttachments = [];

        return {
            myService: myService,
        }
    },
    methods: {
        downloadSheet(sheet) {
            window.location.href = route('services.liturgy.download', {service: this.service.id, key: sheet.key});
        },
        newRow() {
            this.myService.newAttachments.push({text: '', file: ''});
            this.$forceUpdate();
        },
        deleteAttachment(attachment, key) {
            this.myService.removeAttachments.push(attachment.id);
            this.myService.attachments.splice(key, 1);
        },
    }
}
</script>

<style scoped>
.liturgy-sheet {
    width: 100%;
    text-align: left;
    margin-bottom: .25rem;
}

.liturgy-sheet small {
    padding-left: 15px;
}

.fa-download {
    color: gray;
}
</style>
