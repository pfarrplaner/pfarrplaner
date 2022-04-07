<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2022 Christoph Fischer, https://christoph-fischer.org
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
    <div class="container pt-3">
        <div class="alert alert-info" v-if="services.length == 0">
        Heute gibt es keine Gottesdienste mit QR-Codes in {{ city.name }}.
        </div>
        <accordion v-else id="servicesAccordion">
            <accordion-element v-for="(service,serviceIndex,serviceKey) in services"
                               v-if="service.konfiapp_event_qr"
                               :title="service.timeText+': '+service.titleText+' ('+service.locationText+')'"
                               :key="serviceKey">
                <img :src="route('qrcode', service.konfiapp_event_qr)" class="img-fluid qr" />
                <div class="text-small">{{getType(service)}}</div>
            </accordion-element>
        </accordion>
    </div>
</template>

<script>
import Accordion from "../../../components/Ui/accordion/Accordion";
import AccordionElement from "../../../components/Ui/accordion/AccordionElement";

export default {
    name: "QR",
    components: {AccordionElement, Accordion},
    props: ['services', 'city', 'types'],
    methods: {
        getType(service) {
            let filtered = this.types.filter(item => {
                return item.id == service.konfiapp_event_type;
            });
            if (filtered.length == 0) return '';
            return filtered[0]['punktzahl']+(filtered[0]['punktzahl'] == 1 ? ' Punkt' : ' Punkte')+' in der Kategorie '+filtered[0]['name'];
        }
    }
}
</script>

<style scoped>
    .qr {
        width: 100%;
    }
</style>
