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
    <div class="absences-tab">
        <h2>Mein Urlaub</h2>
        <div>
            <a class="btn btn-primary" :href="route('absences.index')" title="Urlaubskalender öffnen">Urlaubskalender öffnen</a>
        </div>
        <hr />
        <div v-if="absences.length == 0" class="alert alert-info">Zur Zeit hast du keinen Urlaub geplant.</div>
        <div v-else>
            <h3>Urlaub / Abwesenheit</h3>
            <fake-table :columns="[3,3,4,2]" :headers="['Zeitraum', 'Beschreibung', 'Vertretung', '']"
                        collapsed-header="Abwesenheiten">
                <div v-for="(absence,absenceIndex) in absences" :key="'absence_'+absenceIndex"
                     class="row mb-3 p-1" :class="{'stripe-odd': (absenceIndex % 2 == 0)}">
                    <div class="col-md-3">{{ absence.durationText }}</div>
                    <div class="col-md-3">{{ absence.reason }}</div>
                    <div class="col-md-4">{{ absence.replacementText }}
                        <div v-if="absence.replacement_notes" style="font-size: 0.8em;">
                            <div style="font-weight: bold">Hinweise:</div>
                            {{ absence.replacement_notes }}
                        </div>
                    </div>
                    <div class="col-md-2 text-right">
                        <a class="btn btn-primary" :href="route('absences.edit', absence.id)" title="Eintrag bearbeiten">
                            <span class="fa fa-edit"></span>
                        </a>
                    </div>
                </div>
            </fake-table>
        </div>
        <hr />
        <div v-if="absences.length == 0" class="alert alert-info">Zur Zeit bist du für keine Vertretungen eingeplant.</div>
        <div v-else>
            <h3>Vertretungen</h3>
            <fake-table :columns="[2,2,2,6]" :headers="['Zeitraum', 'Vertretung für', 'Beschreibung', 'Vertretungsregelung', '']"
                        collapsed-header="Vertretungen">
                <div v-for="(replacement,replacementIndex) in replacements" :key="'replacement_'+replacementIndex"
                     class="row mb-3 p-1" :class="{'stripe-odd': (replacementIndex % 2 == 0)}">
                    <div class="col-md-2">{{ replacement.absence.durationText }}</div>
                    <div class="col-md-2">{{ replacement.absence.user.name }}</div>
                    <div class="col-md-2">{{ replacement.absence.reason }}</div>
                    <div class="col-md-6">{{ replacement.absence.replacementText }}
                        <div v-if="replacement.absence.replacement_notes" style="font-size: 0.8em;">
                            <div style="font-weight: bold">Hinweise:</div>
                            {{ replacement.absence.replacement_notes }}
                        </div>
                    </div>
                </div>
                <div class="col-md-2 text-right">
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
