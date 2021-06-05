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
    <admin-layout title="Über Pfarrplaner">
        <card>
            <card-header>Informationen zur App</card-header>
            <card-body>
                <div>
                <h3>Pfarrplaner</h3><br />
                v.{{ version }}-{{ env }} vom {{ moment(date).locale('DE').format('LLLL')}}
                </div>
                <p>Gehostet auf einem Server des <a href="https://wwww.kirchenbezirk-balingen.de/" target="_blank">Evangelischen Kirchenbezirks Balingen</a>.</p>
                <p>Der Quellcode von Pfarrplaner ist als Open Source auf <a href="https://github.com/pfarrplaner/pfarrplaner" target="_blank">GitHub</a> verfügbar und steht unter
                    der <a href="https://github.com/pfarrplaner/pfarrplaner/blob/master/LICENSE" target="_blank">GNU General Public License (GPL) 3.0</a> oder höher.
                </p>
            </card-body>
        </card>
        <card>
            <card-header>Changelog</card-header>
            <card-body>
                <vue-markdown :source="changelog"/>
            </card-body>
        </card>
    </admin-layout>
</template>

<script>
import CardHeader from "../components/Ui/cards/cardHeader";
import CardBody from "../components/Ui/cards/cardBody";
import Card from "../components/Ui/cards/card";
import VueMarkdown from 'vue-markdown';

export default {
    name: "About",
    components: {Card, CardBody, CardHeader, VueMarkdown},

    props: ['version', 'date', 'changelog', 'env']
}
</script>

<style scoped>
    /deep/ h1, /deep/ h2, /deep/ h3 {
        font-size: 1em;
        font-weight: bold;
    }
</style>
