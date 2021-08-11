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
    <div class="subscriptions-tab">
        <fake-table :columns="[4,8]" collapsed-header="Kirchengemeinde"
                    :headers="['Kirchengemeinde', 'Benachrichtigungseinstellungen']">
            <div class="row p-2" v-for="(city,cityKey) in cities" :key="cityKey">
                <div class="col-md-4 text-bold">{{ city.name }}</div>
                <div class="col-md-8">
                    <form-selectize :options="mySubScriptionOptions" v-model="subscriptions[city.id]"
                        :settings="{searchField: ['name'], valueField: 'id'}"/>
                </div>
            </div>
        </fake-table>
    </div>
</template>

<script>
import FakeTable from "../../Ui/FakeTable";
import FormSelectize from "../../Ui/forms/FormSelectize";
export default {
    name: "SubscriptionsTab",
    components: {FormSelectize, FakeTable},
    props: ['subscriptions', 'cities'],
    data() {
        return {
            mySubScriptionOptions: [
                { id: 0, name: 'keine Benachrichtigungen'},
                { id: 1, name: 'nur eigene Gottesdienste'},
                { id: 2, name: 'alle Gottesdienste'},
                { id: 4, name: 'nur bei Zeit-/Datumsänderungen'},
            ],
        }
    }
}
</script>

<style scoped>

</style>
