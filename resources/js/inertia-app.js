/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/potofcoffee/pfarrplaner
 * @version git: $Id$
 *
 * Sponsored by: Evangelischer Kirchenbezirk Balingen, https://www.kirchenbezirk-balingen.de
 *
 * Pfarrplaner is based on the Laravel framework (https://laravel.com).
 * This file may contain code created by Laravel's scaffolding functions.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

import { InertiaApp } from '@inertiajs/inertia-vue'
import EventBus from './plugins/EventBus.js';
import Vue from 'vue'
import { InertiaProgress } from '@inertiajs/progress'

import AdminLayout from "./Pages/Layouts/AdminLayout";

import LaravelPermission from "./plugins/LaravelPermission";

import CalendarPaneHorizontal from './components/Calendar/Pane/Horizontal';
import CalendarPaneVertical from './components/Calendar/Pane/Vertical';
import CalendarNavTop from './components/Calendar/Nav/Top.vue';
import CalendarNavControlSidebar from './components/Calendar/Nav/ControlSidebar';
import CalendarDayHeader from './components/Calendar/Day/Header';
import CalendarCell from './components/Calendar/Cell';
import CalendarService from './components/Calendar/Service.vue';
import CalendarServiceParticipants from './components/Calendar/Service/Participants.vue';
import CalendarServiceWedding from './components/Calendar/Service/Wedding.vue';
import CalendarServiceFuneral from './components/Calendar/Service/Funeral.vue';
import CalendarServiceBaptism from './components/Calendar/Service/Baptism.vue';
import CalendarControlCitySort from './components/Calendar/Control/CitySort';

import datePicker from 'vue-bootstrap-datetimepicker';
import 'bootstrap/dist/css/bootstrap.css';
import 'pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css';
//import $ from 'jquery';


window._ = require('lodash');
//window.$ = window.jQuery = $;
//import(/* webpackIgnore: true */ 'https://cdnjs.cloudflare.com/ajax/libs/jquery-date-range-picker/0.20.0/jquery.daterangepicker.min.js');


Vue.use(datePicker);


Vue.component('admin-layout', AdminLayout)

Vue.component('calendar-pane-horizontal', CalendarPaneHorizontal);
Vue.component('calendar-pane-vertical', CalendarPaneVertical);
Vue.component('calendar-nav-top', CalendarNavTop);
Vue.component('calendar-nav-control-sidebar', CalendarNavControlSidebar);
Vue.component('calendar-day-header', CalendarDayHeader);
Vue.component('calendar-cell', CalendarCell);
Vue.component('calendar-service', CalendarService);
Vue.component('calendar-service-participants', CalendarServiceParticipants);
Vue.component('calendar-service-wedding', CalendarServiceWedding);
Vue.component('calendar-service-funeral', CalendarServiceFuneral);
Vue.component('calendar-service-baptism', CalendarServiceBaptism);
Vue.component('calendar-control-city-sort', CalendarControlCitySort);


InertiaProgress.init({
    delay: 100,
    color: '#29d',
    includeCSS: true,
    showSpinner: true,
});

Vue.use(InertiaProgress);


Vue.use(LaravelPermission);


Vue.use(EventBus);


Vue.config.productionTip = false


Vue.mixin({
    methods: {
        route: route,
        moment: moment
    }
});
Vue.mixin(require('./mixins/Asset.js'));


// Register a global custom directive called `v-focus`
Vue.directive('focus', {
    // When the bound element is inserted into the DOM...
    inserted: function (el) {
        // Focus the element
        el.focus()
        el.select();
    }
})

// Register a global custom directive called `v-scrollTo`
Vue.directive('scrollTo', {
    // When the bound element is inserted into the DOM...
    inserted: function (el) {
        // Focus the element
        el.scrollTo()
    }
})

const bindCustomEvent = {
    getName: function(binding) {
        return binding.arg + '.' +
            Object.keys(binding.modifiers).map(key => key).join('.');
    },
    bind: function(el, binding, vnode) {
        const eventName = bindCustomEvent.getName(binding);
        document.addEventListener(eventName, binding.value);
    },
    unbind: function(el, binding) {
        const eventName = bindCustomEvent.getName(binding);
        document.removeEventListener(eventName, binding.value);
    }
};

// register a global custom directive called v-bind-custom-event
Vue.directive('bindCustomEvent', bindCustomEvent);



Vue.use(InertiaApp)

jQuery.extend(true, jQuery.fn.datetimepicker.defaults, {
    icons: {
        time: 'far fa-clock',
        date: 'far fa-calendar',
        up: 'fas fa-arrow-up',
        down: 'fas fa-arrow-down',
        previous: 'fas fa-chevron-left',
        next: 'fas fa-chevron-right',
        today: 'fas fa-calendar-check',
        clear: 'far fa-trash-alt',
        close: 'far fa-times-circle'
    }
});


const app = document.getElementById('app')

window.vm = new Vue({
    render: h => h(InertiaApp, {
        props: {
            initialPage: JSON.parse(app.dataset.page),
            resolveComponent: name => import(`./Pages/${name}`).then(module => module.default),
        },
    }),
}).$mount(app)

