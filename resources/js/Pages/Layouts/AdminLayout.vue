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
    <div class="admin-layout wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item" v-if="!noNavBar">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                </li>
                <slot name="navbar-left"/>
            </ul>


            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Notifications Dropdown Menu -->
                <li class="nav-item" id="toggleControlSidebar" v-if="enableControlSidebar">
                    <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#"><i
                        class="fas fa-cogs"></i></a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-navbar btn-light mr-1" :href="route('manual', layout.route)" target="_blank" title="Benutzerhandbuch">
                        <i class="fa fa-question"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-navbar" :href="route('logout')">
                        <i class="fa fa-power-off"></i><span class="d-none d-md-inline"> Abmelden</span>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4" :class="{dev: dev}">
            <!-- Brand Logo -->
            <inertia-link :href="route('home')" class="brand-link" style="margin-left: 5px;" :title="'Startseite (Pfarrplaner '+package.info.version+'-'+package.env+', '+moment(package.date).locale('de').format('LLLL')+')'">
                <img src="/img/logo/pfarrplaner.png" width="22" height="22" class="brand-image"
                     style="opacity: .8; margin-top: 7px;"/>
                <span class="brand-text font-weight-light">Pfarrplaner</span>
            </inertia-link>

            <div class="sidebar" v-if="!noNavBar">
                <!-- Sidebar user panel (optional) -->

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <li class="nav-item">
                            <inertia-link :href="route('user.profile')"
                               class="nav-link"
                               :class="{active: layout.currentRoute == 'user.profile'}">
                                <i class="nav-icon fa fa-user"></i>
                                <p>{{ layout.currentUser.data.name }}</p>
                            </inertia-link>
                        </li>
                        <li v-for="item in layout.menu" :class="{
                            'nav-header': (item.text == undefined) ,
                            'nav-item': (item.text != undefined),
                            'has-treeview': (item.submenu != undefined),
                        }">
                            <span v-if="item.text == undefined">{{ item }}</span>
                            <inertia-link v-if="(item.text != undefined) && (item.inertia == true)" class="nav-link" :class="{ active: item.active }" :href="item.url">
                                <i v-if="item.icon" class="nav-icon" :class="item.icon"  :style="{ color: item.icon_color || 'inherit'}"></i>
                                <p>{{ item.text }}
                                    <i v-if="item.submenu" class="right fas fa-angle-left"></i></p>
                            </inertia-link>
                            <a v-if="(item.text != undefined) && (item.inertia == false)" class="nav-link" :class="{ active: item.active }" @click="clickUrl(item.url)">
                                <i v-if="item.icon" class="nav-icon" :class="item.icon"  :style="{ color: item.icon_color || 'inherit'}"></i>
                                <p>{{ item.text }}
                                    <i v-if="item.submenu" class="right fas fa-angle-left"></i></p></a>
                            <ul class="nav nav-treeview" style="display: none;" v-if="item.submenu != undefined">
                                <li class="nav-item" v-for="subitem in item.submenu">
                                    <inertia-link v-if="subitem.inertia" class="nav-link" :class="{active: subitem.active}"
                                       :href="subitem.url">
                                        <i v-if="subitem.icon" class="nav-icon" :class="subitem.icon"
                                           :style="subitem.icon_color ? 'color: '+subitem.icon_color+';' : ''"></i>
                                        <p>{{ subitem.text }}
                                            <span v-if="subitem.counter != undefined" class="badge right"
                                                  :class="subitem.counter_class ? 'badge-'+subitem.counter_class : 'badge-info'">{{ subitem.counter }}</span>
                                        </p>
                                    </inertia-link>
                                    <a v-else class="nav-link" :class="{active: subitem.active}"
                                       :href="subitem.url">
                                        <i v-if="subitem.icon" class="nav-icon" :class="subitem.icon"
                                           :style="subitem.icon_color ? 'color: '+subitem.icon_color+';' : ''"></i>
                                        <p>{{ subitem.text }}
                                            <span v-if="subitem.counter != undefined" class="badge right"
                                                  :class="subitem.counter_class ? 'badge-'+subitem.counter_class : 'badge-info'">{{ subitem.counter }}</span>
                                        </p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <div class="user-panel d-flex"></div>
                        <!-- Add icons to the links using the .nav-icon class
                             with font-awesome or any other icon font library -->
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header" v-if="(title) && (!noContentHeader)">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">{{ title }}</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <!-- breadcrumbs here -->
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>

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

        <aside class="control-sidebar control-sidebar-dark">
            <div class="p-3 control-sidebar-content">
                <slot name="control-sidebar"/>
            </div>
            <!-- Control sidebar content goes here -->
        </aside>
    </div>
</template>

<script>

export default {
    props: {
        'enableControlSidebar': {
            default: false,
        },
        'noNavBar': {
            default: false,
        },
        'title': {
            default: '',
        },
        noContentHeader: Boolean,
    },
    mounted() {
        if (this.title != '') document.title = this.title + ' :: ' + this.layout.appName;
    },
    data() {
        return {
            dev: vm.$root.$children[0].$page.props.dev,
            layout: vm.$root.$children[0].$page.props,
            package: vm.$root.$children[0].$page.props.package,
        };
    },
    methods: {
        clickUrl(url) {
            if (url=='#') return;
            window.location.href = url;
        }
    }
}
</script>

<style scoped>
.main-sidebar.dev {
    background-color: orangered;
}


.nprogress-busy .admin-layout {
    margin-top: 2px;
}

.nav-item {
    cursor: pointer;
}
</style>
