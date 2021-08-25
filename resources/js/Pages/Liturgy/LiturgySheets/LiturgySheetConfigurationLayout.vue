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
    <admin-layout :title="title + ' einrichten'">
        <template slot="navbar-left">
            <button class="btn btn-primary" @click="submit">
                <span class="d-inline d-md-none fa fa-download"></span>
                <span class="d-none d-md-inline">Herunterladen</span>
            </button>
            <button class="btn btn-light ml-1" @click="back">
                <span class="d-inline d-md-none fa fa-arrow-left"></span>
                <span class="d-none d-md-inline">Zurück</span>
            </button>
        </template>
        <div class="alert alert-info">
            Zu diesem Ausgabeformat gibt es Einstellungen, die du verändern kannst:
        </div>
        <card>
            <card-header>Einstellungen für {{ title }}</card-header>
            <card-body>
                <form id="configForm" method="post" :action="route('liturgy.download', {
                    service: this.service.slug,
                    key: this.sheetConfig.key,
                })">
                    <input type="hidden" name="_token" :value="token"/>
                    <slot/>
                </form>
            </card-body>
        </card>
    </admin-layout>
</template>

<script>
import Card from "../../../components/Ui/cards/card";
import CardHeader from "../../../components/Ui/cards/cardHeader";
import CardBody from "../../../components/Ui/cards/cardBody";

export default {
    name: "LiturgySheetConfigurationLayout",
    components: {CardBody, CardHeader, Card},
    props: ['title', 'service', 'sheetConfig'],
    data() {
        return {
            token: window.Laravel.csrfToken,
        }
    },
    methods: {
        submit() {
            document.getElementById('configForm').submit();
        },
        back() {
            history.back();
        }
    }
}
</script>

<style scoped>

</style>
