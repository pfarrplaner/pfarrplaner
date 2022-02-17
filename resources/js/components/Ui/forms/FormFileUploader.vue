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
        <form-file-upload @input="upload" multiple="1" />
    </div>
</template>

<script>
import FormFileUpload from "./FormFileUpload";
export default {
    name: "FormFileUploader",
    components: {FormFileUpload},
    props: ['parent', 'uploadRoute', 'title'],
    data() {
        return {
            uploading: false,
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
                this.uploading = false;
            });

        }

    }

}
</script>

<style scoped>

</style>
