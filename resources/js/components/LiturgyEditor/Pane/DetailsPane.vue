<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
  - @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
  - @link https://github.com/potofcoffee/pfarrplaner
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
    <div class="liturgy-editor-details-pane">
        <component :is="editorComponent" :element="element" v-model="element" :service="service"/>
    </div>
</template>

<script>

import BlockEditor from "../Editors/BlockEditor";
import FreetextEditor from "../Editors/FreetextEditor";
import LiturgicEditor from "../Editors/LiturgicEditor";
import PsalmEditor from "../Editors/PsalmEditor";
import ReadingEditor from "../Editors/ReadingEditor";
import SermonEditor from "../Editors/SermonEditor";
import SongEditor from "../Editors/SongEditor";

export default {
    name: "DetailsPane",
    components: {
        BlockEditor,
        FreetextEditor,
        LiturgicEditor,
        PsalmEditor,
        ReadingEditor,
        SermonEditor,
        SongEditor
    },
    props: ['element', 'service'],
    data() {
        return {
            editorComponent: this.element.data_type.charAt(0).toUpperCase() + this.element.data_type.slice(1)+'Editor',
        }
    }
}
</script>

<style scoped>

</style>
