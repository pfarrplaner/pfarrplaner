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
    <div v-if="attachments.length > 0">
        <attachment v-for="(attachment,attachmentKey,index) in attachments" :key="attachmentKey" :attachment="attachment"
                    @delete-attachment="deleteAttachment(attachment, attachmentKey)" allow-delete />
    </div>
    <div v-else class="alert alert-info">{{ emptyMessage || 'Es sind noch keine Dateianhänge vorhanden.'}}</div>
</template>

<script>
import Attachment from "./Attachment";
export default {
    name: "AttachmentList",
    components: {Attachment},
    props: ['value', 'deleteRouteName', 'emptyMessage', 'parentType', 'parentObject'],
    data() {
        return {
            attachments: this.value || [],
        }
    },
    methods: {
        deleteAttachment(attachment) {
            let config = { attachment: attachment.id };
            config[this.parentType] = this.parentObject.id;
            console.log(config);
            axios.delete(route(this.deleteRouteName, config))
                .then(response => {
                    this.$emit('input', response.data);
                });
        }
    }
}
</script>

<style scoped>

</style>
