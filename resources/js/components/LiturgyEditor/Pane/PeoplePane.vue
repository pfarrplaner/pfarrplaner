<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
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
    <div class="liturgy-editor-people-pane">
        <form @submit.prevent="save">
            <div class="form-group">
                <label>Verantwortliche*r</label>
                <selectize name="responsible[]" class="form-control" v-model="selected" multiple ref="peopleSelect">
                    <optgroup
                        v-for="ministry,ministryIndex in { pastors: 'Pfarrer*in', organists: 'Organist*in', sacristans: 'Mesner*in'}"
                        :label="ministry">
                        <option :value="'ministry:'+ministryIndex">{{ ministry }} (Alle Eingeteilten)</option>
                        <option v-for="person,personIndex in service[ministryIndex]"
                                :value="'user:'+person.id">{{ person.name }}
                        </option>
                    </optgroup>
                    <optgroup v-for="ministry,ministryIndex in service.ministriesByCategory" :label="ministryIndex">
                        <option :value="'ministry:'+ministryIndex">{{ ministryIndex }} (Alle Eingeteilten)
                        </option>
                        <option v-for="person,personIndex in service.ministriesByCategory[ministryIndex]"
                                :value="'user:'+person.id">{{ person.name }}
                        </option>
                    </optgroup>
                    <optgroup v-if="Object.keys(ministries).length > 0" v-for="ministry,ministryIndex in ministries" label="Alle bekannten Dienste">
                        <option :value="'ministry:'+ministryIndex">{{ ministryIndex }} (Alle Eingeteilten)
                        </option>
                        <option v-for="person,personIndex in service.ministriesByCategory[ministryIndex]"
                                :value="'user:'+person.id">{{ person.name }}
                        </option>
                    </optgroup>
                </selectize>
            </div>
            <hr/>
            <div class="form-group">
                <button class="btn btn-primary">Speichern</button>
                <a class="btn btn-secondary" :href="route('services.liturgy.editor', this.service.id)">Abbrechen</a>
            </div>
        </form>
    </div>
</template>

<script>

import Selectize from 'vue2-selectize';

export default {
    name: "PeoplePane",
    props: {
        element: Object,
        service: Object,
        ministries: {
            type: Object,
            default: [],
        }
    },
    components: {
        Selectize,
    },
    data() {
        var e = this.element;
        var emptyOption = {
            name: '',
            type: '',
        };
        if (undefined == e.data.responsible) e.data.responsible = [emptyOption];
        if (e.data.responsible.length == 0) e.data.responsible = [emptyOption];

        return {
            emptyOption: emptyOption,
            editedElement: e,
            selected: e.data.responsible,
        }
    },
    mounted() {
        this.$refs.peopleSelect.$el.nextSibling.firstChild.click();
    },
    methods: {
        save() {
            this.$inertia.post(route('liturgy.item.roster', {
                service: this.service.id,
                block: this.element.liturgy_block_id,
                item: this.element.id,
            }), this.selected, {preserveState: false});
        },
    }
}
</script>

<style scoped>
.liturgy-editor-people-pane {
    padding: 5px;
}
</style>
