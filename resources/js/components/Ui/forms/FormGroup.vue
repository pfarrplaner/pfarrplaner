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
    <div class="form-group" :class="{'form-group-required' : required}">
        <value-check v-if="isCheckedItem" :value="value" />
        <label v-if="label" :for="id+'Input'" class="control-label"><span v-if="preLabel" :class="['fa', 'fa-'+preLabel]"></span> {{ label }}</label>
        <slot error="error"/>
        <small v-if="error" class="invalid-feedback">{{ error }}</small>
        <div v-else><small v-if="help" class="form-text text-muted">{{ help }}</small></div>
    </div>
</template>

<script>
import ValueCheck from "../elements/ValueCheck";
export default {
    name: "FormGroup",
    components: {ValueCheck},
    props: ['id', 'name', 'label', 'help', 'preLabel', 'required', 'value', 'isCheckedItem'],
    data() {
        const errors = this.$page.props.errors;
        return {
            errors: errors,
            error: errors[this.name] || false,
        }
    },
    watch: {
        value: {
            handler: function(newVal) {
                if (this.required) {
                    this.error = this.$page.props.errors[this.name] = newVal ? '' : 'Dieses Feld darf nicht leer bleiben.';
                    this.$forceUpdate();
                }
            }
        }
    }
}
</script>

<style scoped>
.form-group.form-group-required .control-label:after {
    color: #d00;
    content: "*";
    position: absolute;
    margin-left: 3px;
}

</style>
