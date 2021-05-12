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
    <div class="next-services-tab">
        <h2>{{ title }}</h2>
        <p>Angezeigt werden die Gottesdienste der nächsten 2 Monate.</p>
        <div class="table-responsive">
            <table class="table table-striped" width="100%">
                <thead>
                <tr>
                    <th>Datum</th>
                    <th>Zeit</th>
                    <th>Ort</th>
                    <th>Liturgische Infos</th>
                    <th>Details</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(service,serviceIndex) in services" :key="serviceIndex">
                    <td>{{ moment(service.day.date).locale('de-DE').format('LL') }}</td>
                    <td>{{ service.timeText }}</td>
                    <td>{{ service.locationText }}</td>
                    <td>
                        <liturgical-info :service="service"/>
                    </td>
                    <td>
                        <details-info :service="service" />
                    </td>
                    <td>
                        <inertia-link class="btn btn-light" title="Im Kalender ansehen"
                                      :href="route('calendar', moment(service.day.date).format('YYYY-MM'))">
                            <span class="fa fa-calendar"></span>
                        </inertia-link>
                        <inertia-link class="btn btn-primary" title="Gottesdienst bearbeiten"
                                      :href="route('services.edit', service.id)">
                            <span class="fa fa-edit"></span>
                        </inertia-link>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

    </div>
</template>

<script>
import LiturgicalInfo from "../Service/LiturgicalInfo";
import DetailsInfo from "../Service/DetailsInfo";
export default {
    name: "NextServicesTab",
    components: {DetailsInfo, LiturgicalInfo},
    props: ['title', 'description', 'user', 'settings', 'services']
}
</script>

<style scoped>

</style>
