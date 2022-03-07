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
    <div class="form-image-attacher">
        <form-group :label="label" :help="help" :is-checked-item="isCheckedItem">
            <div v-if="uploading">Datei wird hochgeladen... <span class="mdi mdi-spin mdi-loading"></span></div>
            <div v-else>
                <div v-if="myValue">
                    <img class="img-fluid" :src="route('image', myValue.replace('attachments/', ''))"/>
                    <div>
                        <a class="btn btn-light btn-sm" title="Bild herunterladen"
                           :href="route('image', {path: myValue.replace('attachments/', ''), download: 1})">
                            <span class="mdi mdi-download"></span> Herunterladen
                        </a>
                        <button class="btn btn-danger btn-sm" title="Bild entfernen" @click="detachImage"><span
                            class="mdi mdi-delete"></span> Bild entfernen
                        </button>
                    </div>
                </div>
                <div v-else>
                    <form-file-upload @input="upload" @upload-url="uploadUrl" :no-description="true"
                                      :helpText="handlePaste ? 'Du kannst das Bild auch einfach mit Strg+V aus der Zwischenablage einfügen.' : ''"/>
                </div>
            </div>
        </form-group>
        <modal title="Bild zuschneiden" v-if="modalCropperOpen" min-height="50vh" max-height="80vh"
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
import FormGroup from "./FormGroup";
import Modal from "../modals/Modal";
import {Cropper} from 'vue-advanced-cropper';
import 'vue-advanced-cropper/dist/style.css';

export default {
    name: "FormImageAttacher",
    components: {FormGroup, Modal, FormFileUpload, Cropper},
    props: ['attachRoute', 'detachRoute', 'value', 'label', 'help', 'isCheckedItem', 'handlePaste', 'cropperCanvas', 'cropperStencil', 'width', 'height'],
    created() {
        if (this.handlePaste) window.addEventListener('paste', this.attachFromClipboard);
    },
    destroyed() {
        if (this.handlePaste) window.removeEventListener('paste', this.attachFromClipboard);
    },
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
            myValue: this.value,
            modalCropperOpen: false,
            cropableImage: '',
            cropableAttachment: null,
            canvasSettings,
            stencilSettings,
        }
    },
    methods: {
        upload(file) {
            let fd = new FormData();
            fd.append('attachments[0]', file);

            this.uploading = true;
            axios.post(this.attachRoute, fd, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(response => {
                this.myValue = response.data.image;
                this.$emit('input', response.data.image.toString());
                this.uploading = false;
                this.allowCropping(response.data);
            });

        },
        uploadUrl(data) {
            this.uploading = true;
            axios.post(this.attachRoute, {uploadFromUrl: data.url, attachment_text: data.description})
                .then(response => {
                    this.myValue = response.data.image;
                    this.$emit('input', response.data.image.toString());
                    this.uploading = false;
                    this.allowCropping(response.data);
                });
        },
        imageRoute(image) {
            return route('image', {path: image.replace('attachments/', '')});
        },
        allowCropping(data) {
            this.modalCropperOpen = true;
            this.cropableImage = this.imageRoute(data.image);
            this.cropableAttachment = data;
        },
        detachImage() {
            axios.delete(this.detachRoute).then(response => {
                this.myValue = '';
                this.$emit('input', '');
            })
        },
        attachFromClipboard(event) {
            console.log(event);
            const items = (event.clipboardData || event.originalEvent.clipboardData).items;
            let blob = null;

            for (const item of items) {
                if (item.type.indexOf('image') === 0) {
                    blob = item.getAsFile();
                }
            }
            // upload image if there is a pasted image
            if (blob !== null) {
                this.upload(blob);
                event.preventDefault();
            }
        },
        cropImage() {
            const {coordinates, canvas,} = this.$refs.cropper.getResult();
            if (canvas) {
                this.modalCropperOpen = false;
                this.detachImage();
                const form = new FormData();
                canvas.toBlob(blob => {
                    form.append('attachments[0]', blob);
                    axios.post(this.attachRoute, form)
                        .then(response => {
                            this.myValue = response.data.image;
                            this.$emit('input', response.data.image.toString());
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
