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
    <ul class="pagination">
        <li :class="['page-item', disabledPrevious && 'disabled']">
            <a
                class="page-link"
                href="#"
                title="Zurück"
                :tabindex="disabledPrevious && '-1'"
                :aria-disabled="disabledPrevious && 'true'"
                @click.prevent="setActive(dsPage !== 1 && dsPagecount !== 0 ? dsPage - 1 : dsPage)"
            >
                <span class="fa fa-chevron-left"></span>
            </a>
        </li>
        <template v-for="(item, index) in dsPages">
            <li :key="index" :class="['page-item', item === dsPage && 'active', item === morePages && 'disabled']">
                <a v-if="item !== morePages" class="page-link" href="#" @click.prevent="setActive(item)">
                    {{ item }}
                </a>
                <span v-else class="page-link">
          {{ item }}
        </span>
            </li>
        </template>
        <li :class="['page-item', disabledNext && 'disabled']">
            <a
                class="page-link"
                href="#"
                title="Weiter"
                :tabindex="disabledNext && '-1'"
                :aria-disabled="disabledNext && 'true'"
                @click.prevent="setActive(dsPage !== dsPagecount && dsPagecount !== 0 ? dsPage + 1 : dsPage)"
            >
                <span class="fa fa-chevron-right"></span>
            </a>
        </li>
    </ul>
</template>

<script>

export default {
    inject: ['datasetI18n', 'setActive', 'rdsPages', 'rdsPagecount', 'rdsPage'],
    data: function () {
        return {
            morePages: '...'
        }
    },
    computed: {
        /* Setup reactive injects */
        dsPages() {
            return this.rdsPages()
        },
        dsPagecount() {
            return this.rdsPagecount()
        },
        dsPage() {
            return this.rdsPage()
        },
        /* Normal computeds */
        disabledPrevious() {
            return this.dsPage === 1
        },
        disabledNext() {
            return this.dsPage === this.dsPagecount || this.dsPagecount === 0
        }
    }
}
</script>
