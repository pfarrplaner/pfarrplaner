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
    <div v-if="count == 0" class="alert alert-info">Zur Zeit gibt es keine Gottesdienste mit Anmeldungen.</div>
    <fake-table v-else :columns="[2,4,2,4]" :headers="['Gottesdienst', 'Informationen zum Gottesdienst', 'Plätze', '']"
                collapsed-header="Gottesdienste">
        <div v-for="(service,serviceIndex) in services" :key="serviceIndex" class="row mb-3 p-1"
             :class="{'stripe-odd': (serviceIndex%2==0)}">
            <div class="col-md-2">
                <div>{{ moment(service.day.date).locale('de-DE').format('LL') }}</div>
                <div>{{ service.timeText }}</div>
                <div>{{ service.locationText }}</div>
            </div>
            <div class="col-md-4">
                <details-info :service="service" />
            </div>
            <div class="col-md-2">{{ service.freeSeatsText}}</div>
            <div class="col-md-4 text-right">
                <a :href="route('seatfinder', service.id)" title="Anmeldung hinzufügen"
                   class="btn btn-success"><span class="fa fa-ticket-alt"></span>
                    <span class="d-none d-md-inline">Neue Anmeldung</span>
                </a>
                <a :href="route('service.bookings', service.id)" title="Anmeldungen anzeigen"
                   class="btn btn-light"><span class="fa fa-ticket-alt"></span>
                    <span class="d-none d-md-inline">Anmeldungen</span>
                    <span v-if="service.bookings.length > 0" class="badge badge-info">{{ service.bookings.length }}</span>
                </a>
                <a :href="route('booking.finalize', service.id)" title="Anmeldeliste ausgeben"
                   class="btn btn-light"><span class="fa fa-clipboard-check"></span>
                    <span class="d-none d-md-inline">Anmeldeliste</span>
                </a>
            </div>
        </div>
    </fake-table>
</template>

<script>
import FakeTable from "../Ui/FakeTable";
import DetailsInfo from "../Service/DetailsInfo";
export default {
    name: "RegistrationsTab",
    components: {DetailsInfo, FakeTable},
    props: {
        title: String, description: String, user: Object, settings: Object, services: Array, count: Number,
    },
}
</script>

<style scoped>

</style>
