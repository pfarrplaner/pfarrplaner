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
    <div class="baptisms-tab">
        <h2>{{ title }}</h2>
        <div v-if="baptisms.length == 0" class="alert alert-info">
            <span v-if="(settings.homeScreenTabsConfig.baptisms.mine || false)">Zur Zeit hast du keine Taufen geplant.</span>
            <span v-else>Zur Zeit sind keine Taufen geplant.</span>
        </div>
        <div v-else>
            <baptism v-for="baptism in baptisms" class="mb-3" :baptism="baptism" :key="baptism.id" :show-service="true"/>
        </div>

        <hr />

        <h2>Taufanfragen</h2>
        <div v-if="baptismRequests.length == 0" class="alert alert-info">Zur Zeit gibt es keine offenen Taufanfragen.</div>
        <baptism v-else v-for="baptism in baptismRequests" :baptism="baptism" :key="baptism.id" />
    </div>
</template>

<script>
import Baptism from "../ServiceEditor/rites/Baptism";
import DetailsInfo from "../Service/DetailsInfo";
import CheckedProcessItem from "../Ui/elements/CheckedProcessItem";

export default {
    name: "BaptismsTab",
    components: {CheckedProcessItem, DetailsInfo, Baptism},
    props: {
        title: String, description: String, user: Object, settings: Object, baptisms: Array, baptismRequests: Array,
    },
}
</script>

<style scoped>
</style>
