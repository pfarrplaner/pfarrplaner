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
        <div v-if="uploading">Datei wird hochgeladen... <span class="fa fa-spinner fa-spin"></span></div>
        <div v-if="myValue">
            <img class="img-fluid" :src="route('image', myValue.replace('attachments/', ''))" />
            <div>
                <a class="btn btn-light btn-sm" title="Bild herunterladen" :href="route('image', {path: myValue.replace('attachments/', ''), download: 1})">
                    <span class="fa fa-download"></span> Herunterladen
                </a>
                <button class="btn btn-danger btn-sm" title="Bild entfernen" @click="detachImage"><span class="fa fa-trash"></span> Bild entfernen</button>
            </div>
        </div>
        <div v-else>
            <form-file-upload @input="upload" :helpText="handlePaste ? 'Du kannst das Bild auch einfach mit Strg+V aus der Zwischenablage einfügen.' : ''" />
        </div>
        </form-group>
    </div>
</template>

<script>
import FormFileUpload from "./FormFileUpload";
import FormGroup from "./FormGroup";
export default {
    name: "FormImageAttacher",
    components: {FormGroup, FormFileUpload},
    props: ['attachRoute', 'detachRoute', 'value', 'label', 'help', 'isCheckedItem', 'handlePaste'],
    created() {
        if (this.handlePaste) window.addEventListener('paste', this.attachFromClipboard);
    },
    destroyed() {
        if (this.handlePaste) window.removeEventListener('paste', this.attachFromClipboard);
    },
    data() {
        return {
            uploading: false,
            myValue: this.value,
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
            });

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
    }

}
</script>

<style scoped>

</style>
