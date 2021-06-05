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
    <form-group :label="label" :help="help" :is-checked-item="isCheckedItem" :pre-label="preLabel">
        <div class="row">
            <div class="col-md-6">
                <date-picker v-model="myFrom" :config="myDatePickerConfig1" @input="setFrom"/>
            </div>
            <div class="col-md-6">
                <date-picker v-model="myTo" :config="myDatePickerConfig2" @input="setTo"/>
            </div>
        </div>
    </form-group>
</template>

<script>
import FormGroup from "../forms/FormGroup";
export default {
    name: "DateRangeInput",
    components: {FormGroup},
    props: ['from', 'to', 'label', 'preLabel', 'help', 'isCheckedItem'],
    data() {
        return {
            myDatePickerConfig1: {
                locale: 'de',
                format: 'DD.MM.YYYY',
                maxDate: this.to,
            },
            myDatePickerConfig2: {
                locale: 'de',
                format: 'DD.MM.YYYY',
                useCurrent: false,
                minDate: this.from,
            },
            myFrom: this.from.length > 10 ? moment(this.from).format('DD.MM.YYYY') : this.from,
            myTo: this.to.length > 10 ? moment(this.to).format('DD.MM.YYYY') : this.to,
        }
    },
    methods: {
        setFrom(e) {
            this.myFrom = e;
            this.myDatePickerConfig2.minDate = moment(e, 'DD.MM.YYYY');
            this.$emit('input', [moment(this.myFrom, 'DD.MM.YYYY'), moment(this.myTo, 'DD.MM.YYYY')]);
        },
        setTo(e) {
            this.myTo = e;
            this.myDatePickerConfig1.maxDate = moment(e, 'DD.MM.YYYY');
            this.$emit('input', [moment(this.myFrom, 'DD.MM.YYYY'), moment(this.myTo, 'DD.MM.YYYY')]);
        }
    }
}
</script>

<style scoped>

</style>
