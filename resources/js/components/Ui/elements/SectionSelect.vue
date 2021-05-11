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
    <div class="section-select">
        <form-selectize :value="myValue" :label="label" :help="help" :name="name" @input="handleInput"
                        :options="myItems" :settings="mySelectizeSettings" title-key="title" id-key="title"
                        :multiple="multiple"
        />
    </div>
</template>

<script>
import FormSelectize from "../forms/FormSelectize";
export default {
    name: "SectionSelect",
    props: ['location', 'value', 'label', 'help', 'name', 'multiple'],
    components: {FormSelectize},
    computed: {
        myItems() {
            if (!this.myLocation.seating_sections) this.myLocation.seating_sections = [];
            return this.myLocation.seating_sections;
        }
    },
    data() {
        return {
            myLocation: this.location,
            mySelectizeSettings: {
                labelField: 'title',
                valueField: 'title',
                searchField: ['title'],
            },
            myValue: this.value ? this.value.split(',') : [],
        }
    },
    methods: {
        handleInput(e) {
            this.$emit('input', e.join(','));
        }
    }
}
</script>

<style scoped>

</style>
