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
    <div class="pixabay-picker">
        <form-input name="search" v-model="search" placeholder="Auf Pixabay suchen..." @input="getPage(component, page)"/>
        <div v-if="results.length > 0" :key="search+page">
            <div class="mb-1">
                <button v-if="page > 1" class="btn btn-sm btn-light"  @click.prevent.stop="getPage(component, page-1)">
                    <span class="mdi mdi-chevron-left"></span>
                </button>
                Seite {{ page }} / {{ pages }} ({{ totalHits }} Ergebnisse)
                <button v-if="page < pages" class="btn btn-sm btn-light" @click.prevent.stop="getPage(component, page+1)">
                    <span class="mdi mdi-chevron-right"></span>
                </button>
            </div>
            <div :key="search+page">
                <img class="pixabay-preview img-thumbnail" v-for="(result,resultKey) in results"
                     :src="result.previewURL" title="Klicken, um dieses Bild auszuwählen"
                     @click="$emit('input', result)"
                />
            </div>
        </div>
    </div>
</template>

<script>
import FormGroup from "./FormGroup";
import FormInput from "./FormInput";
import __ from "lodash";
export default {
    name: "FormPixabayPicker",
    components: {FormInput, FormGroup},
    data() {
        return {
            page: 1,
            pages: 0,
            token: this.$page.props.currentUser.data.api_token,
            totalHits: 0,
            results: [],
            search: '',
            component: this,
        }
    },
    methods: {
        getPage: __.debounce((component, page) => {
            if (!component.search) {
                component.result = {};
                component.totalHits = 0;
                component.pages = 0;
                return;
            }
            component.page = page;
            axios.get(route('api.pixabay.query', {query: component.search, page, api_token: component.token}))
                .then(response => {
                    component.totalHits = response.data.totalHits;
                    component.results = response.data.hits;
                    component.pages = Math.ceil(component.totalHits / 20);
                });
        }, 1000),
    }
}
</script>

<style scoped>
.pixabay-preview {
    object-fit: cover;
    overflow: hidden;
    height: 100px;
    width: 100px;
    float: left;
}
</style>
