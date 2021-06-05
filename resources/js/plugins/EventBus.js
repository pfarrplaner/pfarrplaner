/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/pfarrplaner/pfarrplaner
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

/**
 * @source https://yavuztas.dev/javascript/vuejs/2019/11/10/type-based-global-events-in-vuejs.html
 */
export default {

    $eventBus: null,

    install (Vue, options) {
        this.$eventBus = new Vue()
    },

    listen (eventClass, handler) {
        this.$eventBus.$on(eventClass.name, handler)
    },

    listenOnce (eventClass, handler) {
        this.$eventBus.$once(eventClass.name, handler)
    },

    remove (eventClass, handler) {
        if (handler) {
            this.$eventBus.$off(eventClass.name, handler)
        } else {
            this.$eventBus.$off(eventClass.name)
        }
    },

    removeAll () {
        this.$eventBus.$off()
    },

    publish (event) {
        this.$eventBus.$emit(event.constructor.name, event)
    }

}
