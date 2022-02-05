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
    <div>
        <span v-for="city in allCities" class="badge mr-1" :class="city.badge" >{{ city.name }}</span>
    </div>
</template>

<script>
export default {
    name: "CitiesWithAccess",
    props: ['user'],
    data() {
        return {
            visibleCities: this.$page.props.currentUser.data.cities,
        }
    },
    computed: {
        allCities() {
            let cities = {};
            this.user.admin_cities.forEach(city => {
                if (undefined == cities[city.name]) cities[city.name] = {name: city.name, badge: 'badge-admin', id: city.id};
            });
            this.user.writable_cities.forEach(city => {
                if (undefined == cities[city.name]) cities[city.name] = {name: city.name, badge: 'badge-success', id: city.id};
            });
            this.user.cities.forEach(city => {
                if (undefined == cities[city.name]) cities[city.name] = {name: city.name, badge: 'badge-warning', id: city.id};
            });
            if (this.$page.props.currentUser.data.isAdmin) return cities;

            let filteredCities = [];
            for (const cityIndex in cities) {
                if (this.visibleCities.includes(cities[cityIndex].id)) filteredCities.push(cities[cityIndex]);
            }
            return filteredCities;
        }
    }
}
</script>

<style scoped>
    .badge-admin {
        background-color: purple;
        color: white;
    }
</style>
