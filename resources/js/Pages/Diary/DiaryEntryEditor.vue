<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2022 Christoph Fischer, https://christoph-fischer.org
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
    <modal v-if="showModal" title="Eintrag bearbeiten" @close="submit" @cancel="deleteEntry"
           style="min-height: 50vh;" :cancel-button-type="this.myDiaryEntry.id == -1 ? 'secondary' : 'danger'"
           close-button-label="Speichern" :cancel-button-label="this.myDiaryEntry.id == -1 ? 'Abbrechen' : 'Löschen'">
        <form-date-picker label="Datum und Uhrzeit" v-model="myDiaryEntry.date" :config="myDatePickerConfig"
        iso-date/>
        <form-input ref="title" label="Bezeichnung" v-model="myDiaryEntry.title" autofocus />
    </modal>
</template>

<script>
import Modal from "../../components/Ui/modals/Modal";
import FormInput from "../../components/Ui/forms/FormInput";
import FormDatePicker from "../../components/Ui/forms/FormDatePicker";
export default {
    name: "DiaryEntryEditor",
    components: {FormDatePicker, FormInput, Modal},
    props: ['diaryEntry'],
    mounted() {
        this.$refs.title.$el.focus();
    },
    data() {
        return {
            showModal: true,
            apiToken: this.$page.props.currentUser.data.api_token,
            myDiaryEntry: this.diaryEntry,
            myDatePickerConfig: {
                locale: 'de',
                format: 'DD.MM.YYYY HH:mm',
                inline: true,
                sideBySide: true,
            }
        }
    },
    methods: {
        submit() {
            if (this.myDiaryEntry.id != -1) {
                axios.patch(route('api.diary.entry.update', {
                    diaryEntry: this.myDiaryEntry.id,
                    api_token: this.apiToken,
                }), this.myDiaryEntry)
                    .then(response => {
                        this.myDiaryEntry = response.data;
                        this.$emit('input', this.myDiaryEntry);
                    })
            } else {
                axios.post(route('api.diary.entry.store', {
                    api_token: this.apiToken,
                }), {
                    ...this.myDiaryEntry,
                    id: null,
                })
                    .then(response => {
                        this.myDiaryEntry = response.data;
                        this.$emit('input', {...this.myDiaryEntry, created: true});
                    })
            }
        },
        deleteEntry(source) {
            this.showModal = false;
            if (this.myDiaryEntry.id != -1) {
                if (source != 'window') this.$inertia.delete(route('diary.entry.destroy', this.myDiaryEntry.id), {preserveState: false});
                this.$emit('cancel');
            } else {
                this.$emit('cancel');
            }
        }
    }
}
</script>

<style scoped>

</style>
