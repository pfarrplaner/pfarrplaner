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
        <div v-if="hasAutoAttachments || (service.attachments.length > 0)">
            <div v-if="myService.liturgy_blocks.length > 0">
                <div class="liturgy-sheet btn btn-light" v-for="(sheet,key,index) in liturgySheets" :key="key"
                     @click.prevent="downloadSheet(sheet)"
                     v-if="!sheet.isNotAFile">
                    <b><span :class="sheet.icon"></span> {{ sheet.title }}</b><br/>
                    <small>.{{ sheet.extension }}, Größe unbekannt</small>
                    <span class="float-right fa fa-download"></span>
                </div>
            </div>
            <div v-if="myService.konfiapp_event_qr">
                <div class="liturgy-sheet btn btn-light" @click.prevent="downloadQR">
                    <b><span class="fa fa-file-pdf"></span> QR-Code für Konfis</b><br/>
                    <small>.pdf, ca. 50 kB</small>
                    <span class="float-right fa fa-download"></span>
                </div>
            </div>
            <div v-if="service.attachments.length > 0">
                <attachment v-for="(attachment,key,index) in service.attachments" :key="key" :attachment="attachment"
                            @delete-attachment="deleteAttachment(attachment, key)" allow-delete />
            </div>
        </div>
        <div v-else class="alert alert-info">Zu diesem Gottesdienst gibt es keine Dateianhänge.</div>
        <hr/>
        <h3>Dateien hinzufügen</h3>
        <div v-if="uploading">Datei wird hochgeladen... <span class="fa fa-spinner fa-spin"></span></div>
        <input v-else class="uploader" type="file" @change="upload" />
    </div>
</template>

<script>
import Attachment from "../../Ui/elements/Attachment";
import FormInput from "../../Ui/forms/FormInput";
import FormGroup from "../../Ui/forms/FormGroup";
import FormFileUpload from "../../Ui/forms/FormFileUpload";

export default {
    name: "AttachmentsTab",
    components: {
        FormFileUpload,
        FormGroup,
        FormInput,
        Attachment
    },
    props: {
        service: Object,
        liturgySheets: Object,
        files: Object,
    },
    computed: {
        hasAutoAttachments() {
            return (this.myService.liturgy_blocks.length > 0) || (this.myService.konfiapp_event_qr);
        }
    },
    data() {
        var myService = this.service;

        return {
            myService: myService,
            uploading: false,
        }
    },
    methods: {
        downloadSheet(sheet) {
            window.location.href = route('services.liturgy.download', {service: this.service.id, key: sheet.key});
        },
        downloadQR() {
            window.location.href = route('report.step', {report: 'KonfiAppQR', step: 'single', service: this.myService.id});
        },
        newRow() {
            this.files.attachment_text.push('');
            this.files.attachments.push(null);
            this.$forceUpdate();
        },
        deleteAttachment(attachment, key) {
            axios.delete(route('service.detach', {service: this.myService.id, attachment: attachment.id}))
            .then(response => {
                this.myService.attachments = response.data;
            });
        },
        upload(event) {
            let title = event.target.files[0].name;
            console.log(title);
            title = title.substr(0, title.lastIndexOf('.'));
            title = title.charAt(0).toUpperCase() + title.slice(1);
            title = window.prompt('Bitte gib eine Beschreibung zu dieser Datei an.', title);
            if (null == title) return;

            let fd = new FormData();
            fd.append('attachment_text[0]', title);
            fd.append('attachments[0]', event.target.files[0]);
            console.log(event.target.files[0], fd);

            this.uploading = true;
            axios.post(route('service.attach', this.service.id), fd, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(response => {
                this.myService.attachments = response.data;
                this.uploading = false;
            });

        }
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

.uploader {
    width: 100%;
    min-height: 50px;
}

.alert a.btn {
    text-decoration: none;
}
</style>
