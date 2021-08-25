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
    <div class="cases-tab">
        <h2>Beerdigungen</h2>
        <div v-if="!funeralsCount" class="alert alert-info">
            <span>Es sind keine Beerdigungen eingetragen.</span>
        </div>
        <div v-else>
            <fake-table :columns="[2,2,4,3,1]" collapsed-header="Beerdigungen"
                        :headers="['Gottesdienst', 'Verstorbene:r', 'Informationen zur Bestattung', 'Details', '']">
                <funeral v-for="(funeral,funeralIndex) in funerals" :funeral="funeral" :key="'case_funeral_'+funeral.id"
                         :show-service="true" :show-pastor="true"
                         class="row mb-3 p-1" :class="{'stripe-odd': (funeralIndex % 2 == 0)}"/>
            </fake-table>
        </div>
        <hr />
        <h2>Trauungen</h2>
        <div v-if="!weddingsCount" class="alert alert-info">
            <span>Es sind keine Trauungen eingetragen.</span>
        </div>
        <div v-else>
            <fake-table :columns="[2,2,4,3,1]" collapsed-header="Trauungen"
                        :headers="['Gottesdienst','Hochzeitspaar', 'Informationen zur Trauung', 'Details', '']">
                <wedding v-for="(wedding, weddingIndex) in weddings"  :wedding="wedding" :key="'case_wedding_'+wedding.id"
                         :show-service="true" :show-pastor="true"
                         class="mb-3 p-1" :class="{'stripe-odd': (weddingIndex % 2 == 0)}"/>
            </fake-table>
        </div>
        <hr />
        <h2>Taufen</h2>
        <div v-if="!baptismsCount" class="alert alert-info">
            <span>Es sind keine Taufen eingetragen.</span>
        </div>
        <div v-else>
            <fake-table :columns="[2,2,4,3,1]" collapsed-header="Taufen"
                        :headers="['Gottesdienst', 'Täufling', 'Informationen zur Taufe', 'Details', '']">
                <baptism v-for="(baptism, baptismIndex) in baptisms" :baptism="baptism" :key="'case_baptism_'+baptism.id"
                         :show-service="true" :show-pastor="true"
                         class="mb-3 p-1" :class="{'stripe-odd': (baptismIndex % 2 == 0)}"/>

            </fake-table>
        </div>
    </div>

</template>

<script>
import FakeTable from "../Ui/FakeTable";
import Baptism from "../ServiceEditor/rites/Baptism";
import Funeral from "../ServiceEditor/rites/Funeral";
import Wedding from "../ServiceEditor/rites/Wedding";
export default {
    name: "CasesTab",
    components: {Baptism, Funeral, Wedding, FakeTable},
    props: ['title', 'description', 'user', 'settings', 'funerals', 'funeralsCount', 'weddings', 'weddingsCount', 'baptisms', 'baptismsCount']

}
</script>

<style scoped>

</style>
