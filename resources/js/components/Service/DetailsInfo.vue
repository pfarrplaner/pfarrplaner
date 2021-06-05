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
    <div class="details-info">
        <div v-if="service.titleText != 'Gottesdienst'" class="service-description">
            <b>{{ service.titleText }}</b>
        </div>
        <div class="service-description" v-if="service.weddings.length > 0">
            <span class="fa fa-ring"></span>
            <calendar-service-wedding v-if="$can('gd-kasualien-lesen') || $can('gd-kasualien-barbeiten')"
                                      v-for="(wedding,index) in service.weddings" :key="wedding.id" :wedding="wedding" trailer=", " :trail="index > 0"/>
        </div>
        <div class="service-description" v-if="service.funerals.length > 0">
            <span class="fa fa-cross"></span>
            <calendar-service-funeral v-if="$can('gd-kasualien-lesen') || $can('gd-kasualien-barbeiten')"
                                      v-for="(wedding,index) in service.weddings" :key="wedding.id" :wedding="wedding" trailer=", " :trail="index > 0"/>
        </div>
        <div class="service-description" v-html="service.descriptionText"></div>
        <div class="service-description" v-if="service.internal_remarks">
            <span class="fa fa-eye-slash" title="Anmerkung nur für den internen Gebrauch"></span> {{ service.internal_remarks }}
        </div>

        <calendar-service-participants :participants="service.pastors" category="P" :predicant="service.need_predicant" />
        <calendar-service-participants :participants="service.organists" category="O" :predicant="0" />
        <calendar-service-participants :participants="service.sacristans" category="M" :predicant="0" />
        <calendar-service-participants v-for="participants,ministry in service.ministriesByCategory" :key="ministry"
                                       :participants="participants" :category="ministry" :predicant="0" />
        <div v-if="$can('gd-kasualien-lesen') || $can('gd-kasualien-nur-statistik')">
            <div class="service-description" v-if="service.baptisms.length > 0">
                <span class="fa fa-water" :title="$can('gd-kasualien-lesen') ? service.baptismsText : ''"></span> {{ service.baptisms.length }}
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "DetailsInfo",
    props: ['service']
}
</script>

<style scoped>

</style>
