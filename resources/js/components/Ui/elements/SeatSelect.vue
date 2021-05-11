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
    <div class="seat-select">
        <form-selectize :label="label" :help="help" :multiple="multiple" :name="name"
                        title-key="title"
                        :options="myItems"  :settings="mySelectizeSettings" :item-renderer="renderItem"
                        :option-renderer="renderItem"/>
    </div>
</template>

<script>
import FormSelectize from "../forms/FormSelectize";
export default {
    name: "SeatSelect",
    components: {FormSelectize},
    props: ['location', 'label', 'help', 'multiple', 'name', 'excludeSections'],
    computed: {
        myItems() {
            var items = [];
            if (!this.excludedSections) this.excludedSections = [];
            this.location.seating_sections.forEach(section => {
                if (!this.excludedSections.includes(section)) {
                    var sectionColor = (section.color ? section.color : '#efefef');
                    section.seating_rows.forEach(row => {
                        var icon = row.seats > 1 ? 'couch' : 'chair';
                        items.push({id: row.title, title: row.title, section: section.title, seats: row.seats,
                            icon: icon, color: row.color ? row.color : sectionColor});
                        if (row.split) {
                            var splitFactor = row.split;
                            var chrVal = 64;
                            splitFactor.split(',').forEach(factor => {
                                var title = row.title+String.fromCharCode(++chrVal);
                                items.push({id: title, title: title, section: section.title,
                                    seats: factor, icon: 'chair', color: row.color ? row.color : sectionColor});
                            });
                        }
                    });
                }
            });
            return items;
        }
    },
    data() {
        return {
            myLocation: this.location,
            mySelectizeSettings: {
                labelField: 'title',
                valueField: 'title',
                searchField: ['title', 'section'],
                excludedSections: this.excludeSections,
            }
        }
    },
    methods: {
        renderItem(item, escape) {
            var icon = '';
            if (item.icon == 'couch') {
                icon = '<span class="fa fa-couch"></span>';
            } else {
                for (var i=0; i<item.seats; i++) {
                    icon += '<span class="fa fa-chair"></span>';
                }
            }
            return '<div class="item" style="padding-left: 3px; background-color: '+item.color+'"><div>'
                +icon+' '+escape(item.title)+'</div><span style="font-size: .7em;">'
                +escape(item.section)+' &middot; '+escape(item.seats.toString())
                +(item.seats >1 ? ' Plätze' : ' Platz')+'</span></div>';
        }
    }
}
</script>

<style scoped>

</style>
