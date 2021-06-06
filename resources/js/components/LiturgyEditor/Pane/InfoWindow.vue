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
    <modal title="Liturgische Informationen" :allow-cancel="false" close-button-label="Schließen"
           @close="$emit('close')">
        <img class="img-fluid" :src="'https://www.kirchenjahr-evangelisch.de/'+liturgy['litDetailsImage']" />
        <h3>{{ liturgy['title']}}</h3>
        <h4 v-if="liturgy['litProfileTitle']">{{ liturgy['litProfileTitle']}}</h4>
        <h5>Kernaussage</h5>
        <p v-html="liturgy['litProfileGist']" />
        <h5>{{ liturgy['litDetailsHeadline'] }}</h5>
        <p v-html="liturgy['litDetailsTeaser']" />
        <p v-html="liturgy['litDetailsText']" />
        <h5>{{ liturgy['litRitesTitle']}}</h5>
        <p v-html="liturgy['litRitesTeaser']" />
        <div v-html="liturgy['litRitesText']" />
        <hr />
        <small>Quelle: <a :href="'https://www.kirchenjahr-evangelisch.de/article.php#'+liturgy['dayId']" target="_blank">kirchenjahr-evangelisch.de</a>
            <span v-if="liturgy['litDetailsImageCopyright']">, Bild: {{ liturgy['litDetailsImageCopyright']}}</span>
        </small>
    </modal>
</template>

<script>
import Modal from "../../Ui/modals/Modal";
export default {
    name: "InfoWindow",
    components: {Modal},
    props: ['service'],
    data() {
        return {
            liturgy: this.service.day.liturgy,
        }
    },
}
</script>

<style scoped>

</style>
