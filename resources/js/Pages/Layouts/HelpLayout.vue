<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
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
    <div class="help-layout wrapper">
        <!-- Navbar -->
        <nav class="navbar navbar-expand navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <inertia-link class="nav-link btn btn-light" :href="route('manual', 'index')">
                    <span class="fa fa-home"></span>
                    <span class="d-none d-md-inline">Inhaltsverzeichnis</span>
                </inertia-link>
                <slot name="navbar-left"/>
            </ul>
        </nav>



            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <!-- flash messages here -->
                    <slot/>
                </div>
                <div class="footer">
                    <!-- footer here -->
                </div>
            </div>

        </div>
</template>

<script>

export default {
    props: ['title'],
    mounted() {
        if (this.title != '') document.title = this.title + ' :: Pfarrplaner Handbuch';
    },
    data() {
        return {
            dev: vm.$root.$children[0].$page.props.dev,
            layout: vm.$root.$children[0].$page.props,
        };
    },
    methods: {
    }
}
</script>

<style scoped>

.nprogress-busy .help-layout {
    margin-top: 2px;
}

.nav-item {
    cursor: pointer;
}
</style>
