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
    <div class="attachment btn btn-light" @click.prevent="download" :title="attachment.title + ' herunterladen'">
        <b><span class="fa" :class="attachment.icon"></span> {{ attachment.title }}</b><br/>
        <small>.{{ attachment.extension }}, {{ fileSize(attachment.size) }}</small>
        <button v-if="allowDelete" class="float-right btn btn-xs btn-danger" title="Anhang löschen"
                @click.prevent.stop="deleteAttachment($event)">
            <span class="fa fa-trash"></span>
        </button>
        <span class="float-right fa fa-download" :class="allowDelete ? 'mr-3 mt-1' : ''"></span>
        <img v-if="isImage" class="float-right preview mr-4" :src="imageRoute()" @click.stop="showLightBox = true"/>
        <div v-if="isImage && showLightBox" class="lightbox-backdrop"
             @click.stop="showLightBox = false"
             @keydown.esc="showLightBox = false">
            <div class="btn btn-dark lightbox-close" title="Vorschau schließen"><span class="fa fa-times"></span></div>
            <div class="lightbox" @keydown.esc="showLightBox = false">
                <img class="img-fluid" :src="imageRoute()" @keydown.esc="showLightBox = false"/>
                <div class="mt-2" @keydown.esc="showLightBox = false">
                    <button class="btn btn-primary" @click.stop="download" @keydown.esc="showLightBox = false">Download</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Attachment",
    props: {
        attachment: Object,
        allowDelete: Boolean,
    },
    data() {
        return {
            isImage: this.attachment.mimeType.substr(0, 5) == 'image',
            showLightBox: false,
        }
    },
    methods: {
        download() {
            window.location.href = route('attachment', {attachment: this.attachment.id});
        },
        /**
         * Get human-readable file size
         * @param size
         * @returns {string}
         * @source https://programanddesign.com/js/human-readable-file-size-in-javascript/
         */
        fileSize(size) {
            var i = Math.floor(Math.log(size) / Math.log(1024));
            return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
        },
        deleteAttachment(event) {
            this.$emit('delete-attachment');
        },
        imageRoute() {
            return route('image', {path: this.attachment.file.replace('attachments/', '')});
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

small {
    padding-left: 15px;
}

.btn-danger {
    margin-top: -.3rem;
}

.fa-download {
    color: gray !important;
}

.preview {
    max-height: 2em;
    display: block;
}

.lightbox {
    margin: 0;
    position: absolute;
    width: 90%;
    top: 50%;
    left: 50%;
    -ms-transform: translate(-50%, -50%);
    transform: translate(-50%, -50%);
}

.lightbox img {
    max-height: 80vh;
}

.lightbox-close {
    position: absolute;
    top: 1em;
    right: 1em;
}

.lightbox-backdrop {
    position: fixed;
    padding: 0;
    margin: 0;

    top: 0;
    left: 0;

    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    z-index: 20000;

    vertical-align: center;
    text-align: center;
}
</style>
