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
import Vue from 'vue'

import AdminLayout from "./Pages/Layouts/AdminLayout";

import CalendarPaneHorizontal from './components/Calendar/Pane/Horizontal';
import CalendarNavTop from './components/Calendar/Nav/Top.vue';
import CalendarDayHeader from './components/Calendar/Day/Header';
import CalendarDayColumn from './components/Calendar/Day/Column';
import CalendarCityRow from './components/Calendar/City/Row';
import CalendarService from './components/Calendar/Service.vue';
import CalendarServiceParticipants from './components/Calendar/Service/Participants.vue';
import CalendarServiceWedding from './components/Calendar/Service/Wedding.vue';
import CalendarServiceFuneral from './components/Calendar/Service/Funeral.vue';
import CalendarServiceBaptism from './components/Calendar/Service/Baptism.vue';

Vue.component('admin-layout', AdminLayout)

Vue.component('calendar-pane-horizontal', CalendarPaneHorizontal);
Vue.component('calendar-nav-top', CalendarNavTop);
Vue.component('calendar-day-header', CalendarDayHeader);
Vue.component('calendar-day-column', CalendarDayColumn);
Vue.component('calendar-city-row', CalendarCityRow);
Vue.component('calendar-service', CalendarService);
Vue.component('calendar-service-participants', CalendarServiceParticipants);
Vue.component('calendar-service-wedding', CalendarServiceWedding);
Vue.component('calendar-service-funeral', CalendarServiceFuneral);
Vue.component('calendar-service-baptism', CalendarServiceBaptism);



Vue.config.productionTip = false


Vue.mixin({
    methods: {
        route: route,
        moment: moment
    }
});


Vue.use(InertiaApp)

const app = document.getElementById('app')

new Vue({
    render: h => h(InertiaApp, {
        props: {
            initialPage: JSON.parse(app.dataset.page),
            resolveComponent: name => import(`./Pages/${name}`).then(module => module.default),
        },
    }),
}).$mount(app)

