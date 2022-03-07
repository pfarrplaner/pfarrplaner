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
    <div class="form-file-uploader">
        <div v-if="uploading">Datei wird hochgeladen... <span class="mdi mdi-spin mdi-loading"></span></div>
        <form-file-upload @input="upload" @upload-url="uploadUrl" multiple="1" />
        <modal title="Bild zuschneiden" v-if="modalCropperOpen" min-height="50vh"
               @close="cropImage" @cancel="modalCropperOpen = false;"
               close-button-label="Zuschneiden" cancel-button-label="Original verwenden" max-width="800">
            <div style="height: 40vh; !important">
                <cropper class="cropper" ref="cropper" :src="cropableImage" :canvas="canvasSettings"
                         :stencil-props="stencilSettings"/>
            </div>
        </modal>
    </div>
</template>

<script>
import FormFileUpload from "./FormFileUpload";
import Modal from "../modals/Modal";
import { Cropper } from 'vue-advanced-cropper';
import 'vue-advanced-cropper/dist/style.css';


export default {
    name: "FormFileUploader",
    components: {Modal, FormFileUpload, Cropper},
    props: ['parent', 'uploadRoute', 'title', 'cropperCanvas', 'cropperStencil', 'width', 'height'],
    data() {
        let canvasSettings = this.cropperCanvas || {};
        let stencilSettings = this.cropperStencil || {};
        if (this.width) {
            canvasSettings['width'] = this.width;
        }
        if (this.height) {
            canvasSettings['height'] = this.height;
        }
        if (this.width && this.height) {
            stencilSettings['aspectRatio'] = this.width / this.height;
        }
        return {
            uploading: false,
            modalCropperOpen: false,
            cropableImage: '',
            cropableAttachment: null,
            canvasSettings,
            stencilSettings,
        }
    },
    methods: {
        upload(file) {
            let title = this.title;
            let fileName = file.name;
            if (!title) {
                title = file.name;
                title = title.substr(0, title.lastIndexOf('.'));
                title = title.charAt(0).toUpperCase() + title.slice(1);
                title = window.prompt('Bitte gib eine Beschreibung für die Datei "' + fileName + '" an.', title);
            }
            if (null == title) return;


            let fd = new FormData();
            fd.append('attachment_text[0]', title);
            fd.append('attachments[0]', file);

            this.uploading = true;
            axios.post(this.uploadRoute, fd, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(response => {
                let attachments = response.data;
                this.$emit('input', attachments);
                this.uploading = false;
                this.allowCropping(attachments);
            });

        },
        uploadUrl(data) {
            this.uploading = true;
            axios.post(this.uploadRoute, {uploadFromUrl: data.url, attachment_text: data.description})
                .then(response => {
                    let attachments = response.data;
                    this.$emit('input', attachments);
                    this.uploading = false;
                    this.allowCropping(attachments);
                });
        },
        allowCropping(attachments) {
            if ((attachments.length > 0) && (attachments[attachments.length - 1].mimeType.substr(0, 6) == 'image/')) {
                this.modalCropperOpen = true;
                this.cropableImage = this.imageRoute(attachments[attachments.length - 1]);
                this.cropableAttachment = attachments[attachments.length - 1];
            }
        },
        imageRoute(attachment) {
            return route('image', {path: attachment.file.replace('attachments/', '')});
        },
        cropImage() {
            const {coordinates, canvas,} = this.$refs.cropper.getResult();
            if (canvas) {
                this.modalCropperOpen = false;
                const form = new FormData();
                canvas.toBlob(blob => {
                    form.append('attachments[0]', blob);
                    form.append('attachment_text[0]', this.cropableAttachment.title);
                    this.uploading = true;
                    axios.post(route('attachment.update', this.cropableAttachment.id), form)
                        .then(response => {
                            let attachments = response.data;
                            this.$emit('input', attachments);
                            this.uploading = false;
                        });
                }, 'image/jpeg');
            }
        }
    }
}
</script>

<style scoped>

</style>
