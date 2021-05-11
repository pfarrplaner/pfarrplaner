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
    <div class="tag-select">
        <form-selectize :name="name" :label="label" :help="help"
                        :options="tags" :value="myValue"
                        :item-renderer="renderOption" :option-renderer="renderOption"
                        @input="handleInput"
                        multiple />
    </div>
</template>

<script>
import FormSelectize from "../forms/FormSelectize";
export default {
    name: "TagSelect",
    components: {FormSelectize},
    props: ['tags', 'name', 'label', 'help', 'value'],
    data() {
        var myValue = [];
        if (this.value) {
            this.value.forEach(item => { myValue.push(item.id)});
        }

        return {
            myValue: myValue,
        }
    },
    methods: {
        renderOption(item, escape) {
            return '<div class="item" style="padding-left: 3px;"><span class="fa fa-tag"></span> '+escape(item.name)+'</div>';
        },
        handleInput(e) {
            var items = [];
            this.tags.forEach(tag => { if (e.includes(tag.id.toString())) items.push(tag); });
            this.$emit('input', items);
        },
    }
}
</script>

<style scoped>

</style>
