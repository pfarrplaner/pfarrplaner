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
    <div class="absences-tab">
        <h2>Mein Urlaub</h2>
        <div>
            <a class="btn btn-primary" :href="route('absences.index')" title="Urlaubskalender öffnen">Urlaubskalender öffnen</a>
        </div>
        <hr />
        <div v-if="absences.length == 0" class="alert alert-info">Zur Zeit hast du keinen Urlaub geplant.</div>
        <div v-else>
            <h3>Urlaub / Abwesenheit</h3>
            <fake-table :columns="[2,3,4,3]" :headers="['Zeitraum', 'Beschreibung', 'Vertretung', '']"
                        collapsed-header="Abwesenheiten">
                <div v-for="(absence,absenceIndex) in absences" :key="'absence_'+absenceIndex"
                     class="row mb-3 p-1" :class="{'stripe-odd': (absenceIndex % 2 == 0)}">
                    <div class="col-md-2">{{ absence.durationText }}</div>
                    <div class="col-md-3">{{ absence.reason }}</div>
                    <div class="col-md-4">{{ absence.replacementText }}
                        <div v-if="absence.replacement_notes" style="font-size: 0.8em;">
                            <div style="font-weight: bold">Hinweise:</div>
                            {{ absence.replacement_notes }}
                        </div>
                    </div>
                    <div class="col-md-3 text-right">
                        <a class="btn btn-primary" :href="route('absence.edit', absence.id)" title="Eintrag bearbeiten">
                            <span class="mdi mdi-pencil"></span>
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle m-1" type="button" id="dropdownMenuButton"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                    title="Dokumente herunterladen">
                                <span class="mdi mdi-download"></span>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" title="Urlaubsantrag erstellen"
                                   :href="route('reports.render', {report: 'leaveRequestForm', absence: absence.id})">
                                    Urlaubsantrag
                                </a>
                                <a class="dropdown-item" title="Dienstreiseantrag erstellen"
                                   :href="route('reports.render', {report: 'travelRequestForm', absence: absence.id})">
                                    Dienstreiseantrag
                                </a>
                            </div>
                        </div>


                    </div>
                </div>
            </fake-table>
        </div>
        <hr />
        <div v-if="absences.length == 0" class="alert alert-info">Zur Zeit bist du für keine Vertretungen eingeplant.</div>
        <div v-else>
            <h3>Vertretungen</h3>
            <fake-table :columns="[3,3,6]" :headers="['Zeitraum', 'Vertretung für', 'Vertretungsregelung']"
                        collapsed-header="Vertretungen">
                <div v-for="(replacement,replacementIndex) in replacements" :key="'replacement_'+replacementIndex"
                     class="row mb-3 p-1" :class="{'stripe-odd': (replacementIndex % 2 == 0)}">
                    <div class="col-md-3">{{ replacement.absence.durationText }}</div>
                    <div class="col-md-3">{{ replacement.absence.user.name }}</div>
                    <div class="col-md-6">{{ replacement.absence.replacementText }}
                        <div v-if="replacement.absence.replacement_notes" style="font-size: 0.8em;">
                            <div style="font-weight: bold">Hinweise:</div>
                            {{ replacement.absence.replacement_notes }}
                        </div>
                    </div>
                </div>
            </fake-table>
        </div>
    </div>
</template>

<script>
import FakeTable from "../Ui/FakeTable";
export default {
    name: "AbsencesTab",
    components: {FakeTable},
    props: ['title', 'description', 'user', 'settings', 'absences', 'replacements', 'count', 'config']

}
</script>

<style scoped>

</style>
