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
    <div class="baptisms-tab">
        <h2>{{ title }}</h2>
        <div v-if="baptisms.length == 0" class="alert alert-info">
            <span
                v-if="(settings.homeScreenTabsConfig.baptisms.mine || false)">Zur Zeit hast du keine Taufen geplant.</span>
            <span v-else>Zur Zeit sind keine Taufen geplant.</span>
        </div>
        <div v-else>
            <fake-table :columns="[2,2,4,3,1]" collapsed-header="Taufen"
                        :headers="['Gottesdienst', 'Täufling', 'Informationen zur Taufe', 'Dokumente', '']">
                <baptism v-for="(baptism, baptismIndex) in baptisms" :baptism="baptism" :key="baptism.id" :show-service="true"
                         class="mb-3 p-1" :class="{'stripe-odd': (baptismIndex % 2 == 0)}"/>

            </fake-table>
        </div>

        <hr/>

        <h2>Taufanfragen</h2>
        <div v-if="baptismRequests.length == 0" class="alert alert-info">Zur Zeit gibt es keine offenen Taufanfragen.</div>
        <div v-else>
            <fake-table :columns="[2,4,3,1]" collapsed-header="Taufanfragen"
                        :headers="['Täufling', 'Informationen zur Taufe', 'Dokumente', '']">
                <baptism  v-for="(baptism, baptismIndex) in baptismRequests" :baptism="baptism" :key="baptism.id"
                          class="mb-3 p-1" :class="{'stripe-odd': (baptismIndex % 2 == 0)}"/>
            </fake-table>
            <div class="d-none d-md-block fluid-blocks-header py-2">
                <div class="row">
                    <div class="col-md-2">Täufling</div>
                    <div class="col-md-4">Informationen zur Taufe</div>
                    <div class="col-md-3">Dokumente</div>
                    <div class="col-md-1"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Baptism from "../ServiceEditor/rites/Baptism";
import FakeTable from "../Ui/FakeTable";

export default {
    name: "BaptismsTab",
    components: {FakeTable, Baptism},
    props: {
        title: String, description: String, user: Object, settings: Object, baptisms: Array, baptismRequests: Array,
    },
}
</script>

<style scoped>
</style>
