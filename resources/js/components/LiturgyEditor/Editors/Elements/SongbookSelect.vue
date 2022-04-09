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
    <div class="songbook-select">
        <div :key="songbooks.length">
            <form-selectize :options="songbooks" v-model="myValue" :settings="settings" @input="handleSelect"/>
        </div>
        <modal title="Neues Liederbuch anlegen" v-if="showModal" @close="closeModal" @cancel="cancelModal"
               @shown="modalShown" close-button-label="Speichern">
            <form-input label="Titel" v-model="modalSongbook.name"/>
            <form-input label="Kürzel" v-model="modalSongbook.code"/>
            <form-input label="ISBN" v-model="modalSongbook.isbn"/>
            <form-textarea label="Beschreibung" v-model="modalSongbook.description"/>
        </modal>

    </div>
</template>

<script>
import FormSelectize from "../../../Ui/forms/FormSelectize";
import Modal from "../../../Ui/modals/Modal";
import FormInput from "../../../Ui/forms/FormInput";
import FormTextarea from "../../../Ui/forms/FormTextarea";

export default {
    name: "SongbookSelect",
    components: {FormTextarea, FormInput, Modal, FormSelectize},
    props: ['value', 'songbooks', 'pivot'],
    data() {
        return {
            myValue: this.value.id || this.value,
            settings: {
                searchField: ['name', 'code'],
                create: this.addSongbook,
                render: {
                    option_create: function (data, escape) {
                        return '<div class="create">Neues Liederbuch anlegen: <strong>' + escape(data.input) + '</strong>&hellip;</div>';
                    },
                    item: function (item, escape) {
                        return '<div>' + escape(item.name) + ' (' + escape(item.code) + ')</div>';
                    },
                    option: function (item, escape) {
                        return '<div>' + escape(item.name) + ' (' + escape(item.code) + ')</div>';
                    },
                }
            },
            createCallback: null,
            modalSongbook: {},
            showModal: false,
        }
    },
    methods: {
        addSongbook(item, callback = null) {
            this.createCallback = callback;
            this.modalSongbook = {
                name: item,
                code: '',
                isbn: '',
                description: '',
                image: '',
            };
            this.showModal = true;
        },
        closeModal() {
            this.createCallback({});
            axios.post(route('api.songbook.store', {api_token: this.$page.props.currentUser.data.api_token}), this.modalSongbook)
                .then(response => {
                    this.modalSongbook = response.data;
                    this.songbooks.push(this.modalSongbook);
                    this.myValue = this.modalSongbook.id;
                    this.$forceUpdate();
                    this.handleSelect();
                });
            this.showModal = false;
        },
        cancelModal() {
            this.showModal = false;
        },
        handleSelect() {
            let filtered = this.songbooks.filter(item => {
                return item.id == this.myValue
            });
            if (filtered.length) {
                filtered = filtered[0];
                filtered.pivot = this.pivot;
                filtered.pivot.songbook_id = this.myValue;
                this.$emit('input', filtered);
            }
        }
    }
}
</script>

<style scoped>

</style>
