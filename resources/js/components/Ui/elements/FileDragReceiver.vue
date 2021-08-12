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
    <div class="file-drag-receiver" :key="dragging" :class="{dragging: dragging}"
        @drop.prevent.stop="dropped" @dragenter.prevent="dragging = true" @dragleave.prevent="dragging = false"
         @dragover.prevent="dragging = true">
        <slot />
        <div v-if="dragging" style="width: 100%" class="text-center text-small">Datei(en) hier fallen lassen, um sie hochzuladen</div>
    </div>
</template>

<script>
export default {
    name: "FileDragReceiver",
    props: ['list', 'uploadRoute', 'title', 'multi'],
    data() {
        return {
            dragging: false,
            myList: this.list,
        }
    },
    methods: {
        dropped(e) {
            this.dragging = false;
            if (e.dataTransfer.files.length > 0) {
                if (this.multi) {
                    Array.from(e.dataTransfer.files).forEach(file => {
                        this.upload(file);
                    });
                } else {
                    this.upload(e.dataTransfer.files[0]);
                }
            }
        },
        upload(file) {
            let title = this.title;
            let fileName = file.name;
            if (!title) {
                title = file.name;
                title = title.substr(0, title.lastIndexOf('.'));
                title = title.charAt(0).toUpperCase() + title.slice(1);
                title = window.prompt('Bitte gib eine Beschreibung für die Datei "'+fileName+'" an.', title);
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
                this.$emit('input', response.data);
                this.$forceUpdate();
                this.uploading = false;
            });

        }

    }
}
</script>

<style scoped>
    .file-drag-receiver {
        padding: 3px;
        padding-bottom: 1em;
        min-height: 2em;
    }

    .file-drag-receiver.dragging {
        background-color: #fff;
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

</style>
