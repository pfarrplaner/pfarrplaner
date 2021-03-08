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
    <div class="attachment btn btn-light" @click.prevent="download" :title="attachment.title + ' herunterladen'">
        <b><span class="fa" :class="attachment.icon"></span> {{ attachment.title }}</b><br/>
        <small>.{{ attachment.extension }}, {{ fileSize(attachment.size) }}</small>
        <button v-if="allowDelete" class="float-right btn btn-xs btn-danger" title="Anhang löschen" @click.prevent.stop="deleteAttachment($event)">
            <span class="fa fa-trash"></span>
        </button>
        <span class="float-right fa fa-download"></span>
    </div>
</template>

<script>
export default {
    name: "Attachment",
    props: {
        attachment: Object,
        allowDelete: Boolean,
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
        margin-right: 20px;
        color: gray;
    }
</style>
