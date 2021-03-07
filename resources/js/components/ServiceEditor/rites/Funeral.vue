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
    <div class="funeral row">
        <div class="col-md-2">
            <b>{{ funeral.buried_name }}</b><br />
            <small>{{ moment(funeral.dob).format('DD.MM.YYYY') }} - {{ moment(funeral.dod).format('DD.MM.YYYY') }}
                ({{ funeral.age }})
                <br />
                {{ funeral.type }}
            </small>
        </div>
        <div class="col-md-6">
            <div>
                <checked-process-item :check="(funeral.appointment)" negative="Trauergespräch noch nicht vereinbart">
                    <template slot="positive">
                        Trauergespräch am {{ moment(funeral.appointment).locale('de-DE').format('LLLL') }} Uhr
                    </template>
                </checked-process-item>
                <checked-process-item :check="(funeral.text)" negative="Predigttext noch nicht eingetragen">
                    <template slot="positive">
                        Predigttext: {{ funeral.text }}
                    </template>
                </checked-process-item>
                <checked-process-item :check="(funeral.announcement)" negative="Abkündigungstermin noch nicht festgelegt">
                    <template slot="positive">
                        Abkündigung im GD am {{ moment(funeral.announcement).locale('de-DE').format('LL') }}
                    </template>
                </checked-process-item>
            </div>
        </div>
        <div class="col-md-3">
            <attachment v-for="(attachment,key,index) in funeral.attachments" :key="key" :attachment="attachment" />
        </div>
        <div class="col-md-1 text-right">
            <a class="btn btn-sm btn-light" title="Trauung bearbeiten"
               :href="route('funerals.edit', {funeral: funeral.id})"><span class="fa fa-edit"></span></a>
            <button class="btn btn-sm btn-danger" title="Trauung löschen"
                    @click.prevent="deleteFuneral"><span class="fa fa-trash"></span></button>
        </div>
    </div>

</template>

<script>
import CheckedProcessItem from "../../Ui/elements/CheckedProcessItem";
import Attachment from "../../Ui/elements/Attachment";

export default {
    name: "Funeral",
    components: {
        Attachment,
        CheckedProcessItem,
    },
    props: ['funeral'],
    methods: {
        deleteFuneral() {
            this.$inertia.delete('funerals.destroy', {funeral: this.funeral.id});
        }
    }

}
</script>

<style scoped>
</style>
