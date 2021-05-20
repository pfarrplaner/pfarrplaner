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
    <div>
        <form-selectize :name="name" :items="items" v-model="myValue" @input="handleInput"
                        :label="label" :help="help" id-key="id" title-key="name"/>
    </div>
</template>

<script>
import FormSelectize from "../forms/FormSelectize";

export default {
    name: "KonfiAppEventTypeSelect",
    props: {
        city: Object,
        name: String,
        value: Number,
        label: String,
        help: String,
    },
    components: {FormSelectize},
    created() {
        axios.get('/api/city/'+this.city.id+'/konfiapp-types')
        .then(response => {
            return response.data
        }).then(data => {
            this.items = data;
        })
    },
    data() {
        return {
            items: [],
            myValue: this.value,
        };
    },
    methods: {
        handleInput(e) {
            this.$emit('input', e.id);
        },
    }
}
</script>

<style scoped>

</style>
