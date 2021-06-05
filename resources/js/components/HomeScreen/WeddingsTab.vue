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
    <div class="weddings-tab">
        <h2>{{ title }}</h2>
        <div v-if="!count" class="alert alert-info">
            <span v-if="(settings.homeScreenTabsConfig.funerals.mine || false)">Zur Zeit hast du keine Trauungen geplant.</span>
            <span v-else>Zur Zeit sind keine Trauungen geplant.</span>
        </div>
        <div v-else>
            <fake-table :columns="[2,2,4,3,1]" collapsed-header="Trauungen"
                        :headers="['Gottesdienst','Hochzeitspaar', 'Informationen zur Trauung', 'Dokumente', '']">
                <wedding v-for="(wedding, weddingIndex) in weddings"  :wedding="wedding" :key="wedding.id"
                         :show-service="true"
                         class="mb-3 p-1" :class="{'stripe-odd': (weddingIndex % 2 == 0)}"/>
            </fake-table>
        </div>
    </div>

</template>

<script>
import Wedding from "../ServiceEditor/rites/Wedding";
import FakeTable from "../Ui/FakeTable";
export default {
    name: "WeddingsTab",
    components: {FakeTable, Wedding},
    props: {
        title: String, description: String, user: Object, settings: Object, weddings: Array, count: null,
    },

}
</script>

<style scoped>

</style>
