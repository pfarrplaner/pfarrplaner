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
    <div v-if="parent.needs_dimissorial">
        <checked-process-item v-if="parent.dimissorial_requested" :check="parent.dimissorial_received"
                              :positive="'Dimissoriale  erhalten'">
            <template slot="negative">
                Dimissoriale steht noch aus (beantragt am {{ moment(parent.dimissorial_requested).format('DD.MM.YYYY') }})
            </template>
        </checked-process-item>
        <checked-process-item v-else :check="parent.dimissorial_requested"
                              :negative="'Dimissoriale noch nicht beantragt'" positive="" />
    </div>
</template>

<script>
import CheckedProcessItem from "../Ui/elements/CheckedProcessItem";
export default {
    name: "DimissorialCheckItem",
    components: {CheckedProcessItem},
    props: ['parent'],
}
</script>

<style scoped>

</style>
