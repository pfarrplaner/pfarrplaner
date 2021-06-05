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
    <card>
        <card-header>Streaming-Check</card-header>
        <card-body>
            <div class="alert alert-danger" v-if="activeBroadcasts.length == 0">
                <b>Problem:</b> Es ist kein aktiver Stream vorhanden.
            </div>
            <div v-if="(!activator) && (activeBroadcasts.length == 1)">
                <active-broadcast :broadcast="activeBroadcasts[0]" @wrong="showActivator=true"/>
            </div>
            <div class="alert alert-danger" v-if="activeBroadcasts.length > 1">
                <b>Problem:</b> Es sind mehrere ({{ activeBroadcasts.length }}) Streams gleichzeitig auf aktiv gesetzt.
            </div>
            <div v-if="activator">
                <h2>Lösung:</h2>
                <ol>
                    <li>Stelle sicher, dass du keine Daten sendest.</li>
                    <li>Aktiviere den Stream für deinen Gottesdienst (siehe unten).</li>
                    <li>Starte deine Sendung.</li>
                </ol>
                <service-activator :services="nextServicesWithStream"
                                   title="Verfügbare Streams" @close="showActivator = false"/>
                <button class="btn btn-light" @click="showServicesWithoutStream = true">
                    Mein Gottesdienst fehlt in der Liste.
                </button>
                <service-activator v-if="showServicesWithoutStream"
                                   :services="nextServicesWithoutStream"
                                   title="Gottesdienste ohne bereits angelegten Stream"
                                   @close="showActivator = false"/>
                <button class="btn btn-light" @click="showOtherStreams = true">
                    Mein Stream ist kein Gottesdienst.
                </button>
                <stream-activator v-if="showOtherStreams"
                                  :broadcasts="inactiveBroadcasts" :city="city"
                                  title="Livestreams ohne Gottesdienst"
                                  @close="showActivator = false" />
            </div>
        </card-body>
    </card>
</template>

<script>
import CardHeader from "../../components/Ui/cards/cardHeader";
import CardBody from "../../components/Ui/cards/cardBody";
import Card from "../../components/Ui/cards/card";
import ServiceActivator from "../../components/Streaming/Troubleshooter/ServiceActivator";
import StreamActivator from "../../components/Streaming/Troubleshooter/StreamActivator";
import ActiveBroadcast from "../../components/Streaming/Troubleshooter/ActiveBroadcast";

export default {
    name: "troubleshooter",
    components: {ActiveBroadcast, StreamActivator, ServiceActivator, Card, CardBody, CardHeader},
    props: ['activeBroadcasts', 'inactiveBroadcasts', 'nextServicesWithStream', 'nextServicesWithoutStream', 'city'],
    computed: {
        activator() {
            return this.showActivator || (this.activeBroadcasts.length != 1);
        }
    },
    data() {
        return {
            showActivator: false,
            showServicesWithoutStream: false,
            showOtherStreams: false,
        };
    }
}
</script>

<style scoped>
    table.table tr { cursor: pointer }
</style>
