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
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="mdi mdi-menu"></i></a>
                </li>
                <slot name="navbar-left"/>
            </ul>


            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Notifications Dropdown Menu -->
                <li class="nav-item" id="toggleControlSidebar" v-if="enableControlSidebar">
                    <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#"><i
                        class="mdi mdi-cog"></i></a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-navbar btn-light mr-1" :href="route('manual', layout.route)" target="_blank" title="Benutzerhandbuch">
                        <i class="mdi mdi-help-circle"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-navbar" :href="route('logout')">
                        <i class="mdi mdi-logout"></i><span class="d-none d-md-inline"> Abmelden</span>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4" :class="{dev: dev}" style="position: fixed;">
            <!-- Brand Logo -->
            <div class="brand-link" style="margin-left: 5px;">
                <inertia-link class="brand-link-anchor" :href="route('home')"  :title="'Startseite (Pfarrplaner '+package.info.version+'-'+package.env+', '+moment(package.date).locale('de').format('LLLL')+')'">
                    <img src="/img/logo/pfarrplaner.png" width="22" height="22" class="brand-image"
                         style="opacity: .8; margin-top: 7px;"/>
                    <span class="brand-text font-weight-light">Pfarrplaner</span>
                </inertia-link>
                <a class="mobile-menu-handle d-md-none" data-widget="pushmenu" href="#" title="Menüleiste schließen"><i class="mdi mdi-chevron-left-circle"></i></a>
            </div>


            <div class="sidebar" v-if="!noNavBar">
                <!-- Sidebar user panel (optional) -->

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <li v-for="item in layout.menu" :class="{
                            'nav-header': (item.text == undefined) ,
                            'nav-item': (item.text != undefined),
                            'has-treeview': (item.submenu != undefined),
                        }">
                            <span v-if="item.text == undefined">{{ item }}</span>
                            <inertia-link v-if="(item.text != undefined) && (item.inertia == true)" class="nav-link" :class="{ active: item.active }" :href="item.url">
                                <i v-if="item.icon" class="nav-icon" :class="item.icon"  :style="{ color: item.icon_color || 'inherit'}"></i>
                                <p>{{ item.text }}
                                    <i v-if="item.submenu" class="right mdi mdi-chevron-left"></i></p>
                            </inertia-link>
                            <a v-if="(item.text != undefined) && (item.inertia == false)" class="nav-link" :class="{ active: item.active }" @click="clickUrl(item.url)">
                                <i v-if="item.icon" class="nav-icon" :class="item.icon"  :style="{ color: item.icon_color || 'inherit'}"></i>
                                <p>{{ item.text }}
                                    <i v-if="item.submenu" class="right mdi mdi-chevron-left"></i></p></a>
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
            <div class="content-header border-bottom pb-0 mb-0" v-if="(title) && (!noContentHeader)">
                <div class="container-fluid mb-0 pb-0">
                    <h1 class="m-0 mb-4 text-dark">{{ title }}</h1>
                    <!-- flash messages here -->
                    <slot name="before-flash" />
                    <div v-if="(layout.errors.length > 0) || layout.flash.error" class="alert alert-danger">
                        <span v-if="layout.flash.error">{{ layout.flash.error }}</span>
                        <span v-else>Dein Formular enthält {{ layout.errors.length }} Fehler. Bitte überprüfe deine Eingaben.</span>
                    </div>
                    <div v-for="flashType in ['success','info']">
                        <div v-if="layout.flash[flashType]" class="alert" :class="'alert-'+flashType">{{ layout.flash[flashType] }}</div>
                    </div>
                    <slot name="after-flash" />
                    <div class="slot-tab-headers mb-0 pb-0">
                        <slot name="tab-headers" />
                    </div>
                </div><!-- /.container-fluid -->
            </div>

            <!-- Main content -->
            <div class="content" :class="{'p-0': noPadding, 'pt-3': !noPadding}">
                <div class="container-fluid" :class="{'p-0': noPadding}">
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
        noPadding: Boolean,
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
aside.main-sidebar {
    position: fixed !important;
}

.main-sidebar.dev {
    background-color: orangered;
}


.nprogress-busy .admin-layout {
    margin-top: 2px;
}

.nav-item {
    cursor: pointer;
}

.brand-link a {
    text-decoration: none;
    color: rgba(255,255,255,.8);
}

.brand-link a:hover,
a.brand-link-anchor:hover,
a.brand-link-anchor:hover span.brand-text {
    text-decoration: none !important;
    color: white;
}

.mobile-menu-handle {
    float: right;
    margin-right: 5px;
}

.slot-tab-headers /deep/ ul {
    margin-bottom: 0;
    border-bottom: 0;
}

.content-wrapper {
    background-color: #fff;
}

.content-header {
    background-color: #f4f6f9;
}

.content {
    background-color: #fff;
}
</style>
