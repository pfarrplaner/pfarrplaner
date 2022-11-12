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
    <form @submit.prevent.stop="save">
        <div class="liturgy-item-block-editor">
            <default-fields :service="service" :element="editedElement" :agenda-mode="agendaMode"/>
        </div>
    </form>
</template>

<script>
import DefaultFields from "./Elements/DefaultFields";
import TimeFields from "./Elements/TimeFields";
import __ from "lodash";

export default {
    name: "BlockEditor",
    components: {
      TimeFields,
        DefaultFields,
    },
    props: {
        element: Object,
        service: Object,
        agendaMode: {
            type: Boolean,
            default: false,
        }
    },
    data() {
        return {
            apiToken: this.$page.props.currentUser.data.api_token,
            savedState: __.clone(this.element),
            editedElement: this.element,
        };
    },
}
</script>

<style scoped>
.liturgy-item-block-editor {
    padding: 5px;
}

</style>
