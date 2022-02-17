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
    <div class="attachments-tab">
        <h3>Angehängte Dateien</h3>
        <div v-if="hasAutoAttachments || (service.attachments.length > 0)">
            <div v-if="myService.liturgy_blocks.length > 0">
                <div class="liturgy-sheet btn btn-light" v-for="(sheet,key,index) in liturgySheets" :key="key"
                     @click.prevent="(sheet.configurationComponent) ? dialogs[sheet.key] = true : downloadSheet(sheet)"
                     v-if="!sheet.isNotAFile">
                    <b><span :class="sheet.icon"></span> {{ sheet.title }}</b><br/>
                    <small>.{{ sheet.extension }}, Größe unbekannt</small>
                    <span class="float-right mdi mdi-download"></span>
                </div>
            </div>
            <div v-if="myService.konfiapp_event_qr">
                <div class="liturgy-sheet btn btn-light" @click.prevent="downloadQR">
                    <b><span class="mdi mdi-file-pdf-box"></span> QR-Code für Konfis</b><br/>
                    <small>.pdf, ca. 50 kB</small>
                    <span class="float-right mdi mdi-download"></span>
                </div>
            </div>
            <div v-if="!hasAnnouncements" class="liturgy-sheet btn btn-light" @click.prevent="downloadAnnouncements">
                <b><span class="mdi mdi-file-word-box"></span> Bekanntgaben</b><br/>
                <small>.docx, ca. 20 kB</small>
                <span class="float-right mdi mdi-download"></span>
            </div>
            <div v-if="service.attachments.length > 0">
                <attachment v-for="(attachment,key,index) in service.attachments" :key="key" :attachment="attachment"
                            @delete-attachment="deleteAttachment(attachment, key)" allow-delete />
            </div>
        </div>
        <div v-else class="alert alert-info">Zu diesem Gottesdienst gibt es keine Dateianhänge.</div>
        <hr/>
        <h3>Dateien hinzufügen</h3>
        <div v-if="uploading">Datei wird hochgeladen... <span class="mdi mdi-spin mdi-loading"></span></div>
        <form-file-uploader :parent="myService"
                            :upload-route="route('service.attach', this.myService.slug)"
                            v-model="myService.attachments"/>

        <modal v-for="(sheet,sheetKey) in liturgySheets" v-if="dialogs[sheet.key]" :title="sheet.title + ' herunterladen'"
               :key="'dlg'+sheet.key"
               @close="downloadConfiguredSheet(sheet)"
               @cancel="dialogs[sheet.key] = false"
               close-button-label="Herunterladen" cancel-button-label="Abbrechen">
            <component :is="sheet.configurationComponent" :service="service" :sheet="sheet" />
        </modal>
    </div>
</template>

<script>
import Attachment from "../../Ui/elements/Attachment";
import FormInput from "../../Ui/forms/FormInput";
import FormGroup from "../../Ui/forms/FormGroup";
import FormFileUpload from "../../Ui/forms/FormFileUpload";
import Modal from "../../Ui/modals/Modal";
import FormFileUploader from "../../Ui/forms/FormFileUploader";
import FullTextLiturgySheetConfiguration from "../../LiturgyEditor/LiturgySheets/FullTextLiturgySheetConfiguration";
import SongPPTLiturgySheetConfiguration from "../../LiturgyEditor/LiturgySheets/SongPPTLiturgySheetConfiguration";
import SongSheetLiturgySheetConfiguration from "../../LiturgyEditor/LiturgySheets/SongSheetLiturgySheetConfiguration";

export default {
    name: "AttachmentsTab",
    components: {
        FormFileUploader,
        Modal,
        FormFileUpload,
        FormGroup,
        FormInput,
        Attachment,
        FullTextLiturgySheetConfiguration,
        SongPPTLiturgySheetConfiguration,
        SongSheetLiturgySheetConfiguration,
    },
    props: {
        service: Object,
        liturgySheets: Object,
        files: Object,
    },
    computed: {
        hasAutoAttachments() {
            return true;
        },
        hasAnnouncements() {
            let found = false;
            this.service.attachments.forEach(attachment => {
                found = found || (attachment.title == 'Bekanntgaben');
            });
            return found;
        },
    },
    data() {
        var myService = this.service;

        var dialogs = {};
        Object.entries(this.liturgySheets).forEach(sheet => {
            if (sheet[1].configurationComponent) dialogs[sheet[1].key] = false;
        });

        return {
            myService: myService,
            uploading: false,
            dialogs: dialogs,
        }
    },
    methods: {
        downloadSheet(sheet) {
            if (sheet.configurationPage) {
                this.$inertia.visit(route('liturgy.configure', {service: this.service.slug, key: sheet.key}));
            } else {
                window.location.href = route('liturgy.download', {service: this.service.slug, key: sheet.key});
            }
        },
        downloadConfiguredSheet(sheet) {
            document.getElementById('frm'+sheet.key).submit();
            this.dialogs[sheet.key] = false;
        },
        downloadQR() {
            window.location.href = route('report.step', {report: 'KonfiAppQR', step: 'single', service: this.myService.id});
        },
        downloadAnnouncements() {
            window.location.href = route('report.step', {report: 'Announcements', step: 'auto', service: this.myService.id});
        },
        newRow() {
            this.files.attachment_text.push('');
            this.files.attachments.push(null);
            this.$forceUpdate();
        },
        deleteAttachment(attachment, key) {
            axios.delete(route('service.detach', {service: this.myService.slug, attachment: attachment.id}))
            .then(response => {
                this.myService.attachments = response.data;
            });
        },
        upload(file) {
            let title = file.name;
            console.log(title);
            title = title.substr(0, title.lastIndexOf('.'));
            title = title.charAt(0).toUpperCase() + title.slice(1);
            title = window.prompt('Bitte gib eine Beschreibung zu dieser Datei an.', title);
            if (null == title) return;

            let fd = new FormData();
            fd.append('attachment_text[0]', title);
            fd.append('attachments[0]', file);
            console.log(file, fd);

            this.uploading = true;
            axios.post(route('service.attach', this.service.slug), fd, {
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

.mdi-download {
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
