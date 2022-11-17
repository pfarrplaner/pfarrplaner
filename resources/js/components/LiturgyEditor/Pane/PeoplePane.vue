<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
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
    <div class="liturgy-editor-people-pane">
        <form @submit.prevent="save">
            <div class="row">
                <div class="col-sm-9 form-group">
                    <label>Verantwortliche*r</label>
                    <selectize name="responsible[]" class="form-control" v-model="selected" multiple ref="peopleSelect"
                               :options="options"
                               :settings="selectizeSettings" />

                </div>
                <div class="col-sm-3 text-right" style="padding-top: 2rem;">
                    <button class="btn btn-primary" title="Eintrag speichern" @click.prevent.stop="save"><span class="mdi mdi-content-save"></span></button>
                </div>
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

        var options = [];
        var optGroups = [];

        const basicMinistries = { pastors: 'Pfarrer*in', organists: 'Organist*in', sacristans: 'Mesner*in'};
        for (var ministryIndex in basicMinistries) {
            optGroups.push({ groupName: basicMinistries[ministryIndex] });
            options.push({id:'ministry:'+ministryIndex, name: basicMinistries[ministryIndex], category: basicMinistries[ministryIndex], type: 'users'});
            this.service[ministryIndex].forEach(person => {
                options.push({id: 'user:'+person.id, name: person.name, category: basicMinistries[ministryIndex], type: 'user-check'});
            });
        }
        var knownMinistries = this.service.ministriesByCategory;
        Object.keys(this.ministries).forEach(ministry => {
            if (knownMinistries[ministry]) {
                optGroups.push({ groupName: ministry });
                options.push({id:'ministry:'+ministry, name: ministry, category: ministry, type: 'users'});
                knownMinistries[ministry].forEach(person => {
                    options.push({id: 'user:'+person.id, name: person.name, category: ministry, type: 'user-check'});
                });
            }
        }, this);
        optGroups.push({ groupName: 'Eigene Eingaben'});
        e.data.responsible.forEach(item => {
            if ((item) && (typeof item == String)) {
                if (item.substr(0,5) == 'free:') {
                    options.push({id: item, name: item.substr(5), category: 'Eigene Eingaben', type: 'user-times'});
                }
            }
        });


        return {
            apiToken: this.$page.props.currentUser.data.api_token,
            emptyOption: emptyOption,
            editedElement: e,
            selected: e.data.responsible,
            options: options,
            selectizeSettings: {
                options: options,
                searchField: ['name', 'category'],
                valueField: 'id',
                labelField: 'name',
                optgroupField: 'category',
                optgroupLabelField: 'groupName',
                optgroupValueField: 'groupName',
                optgroups: optGroups,
                create: function (input) {
                    return {id: 'free:'+input, name: input, category: 'Eigene Eingaben', type: 'user-times'}
                },
                render: {
                    option_create: function (data, escape) {
                        return '<div class="create">Freie Texteingabe: <strong>' + escape(data.input) + '</strong>&hellip;</div>';
                    },
                    item: function (item, escape) {
                        return '<div><span class="'+item.type+'"></span> '+item.name+'</div>';
                    }
                }
            },
        }
    },
    mounted() {
        this.$refs.peopleSelect.$el.nextSibling.firstChild.click();
    },
    methods: {
        save() {
            axios.post(route('api.liturgy.item.assign', {
                api_token: this.apiToken,
                item: this.element.id,
            }), this.selected).then(response => {
                this.element.data = response.data;
                this.$emit('close');
            });
        },
    }
}
</script>

<style scoped>
.liturgy-editor-people-pane {
    padding: 5px;
}
</style>
