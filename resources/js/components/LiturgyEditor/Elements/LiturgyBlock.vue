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
    <div class="liturgy-block" :class="{focused: focused}" @click="focus">
        <div class="row">
            <div class="col-6 liturgy-block-title" @click="changeTitle">{{ block.title }}</div>
            <div class="col-6 text-right">
                <button @click="addItem" class="btn btn-sm btn-light" title="Freitext hinzufügen"><span class="fa fa-file"></span>
                </button>
                <button @click="addItem" class="btn btn-sm btn-light" title="Psalm hinzufügen"><span class="fa fa-praying-hands"></span>
                </button>
                <button @click="addItem" class="btn btn-sm btn-light" title="Schriftlesung hinzufügen"><span class="fa fa-bible"></span>
                </button>
                <button @click="addItem" class="btn btn-sm btn-light" title="Predigt hinzufügen"><span
                    class="fa fa-microphone-alt"></span></button>
                <button @click="addItem" class="btn btn-sm btn-light" title="Lied hinzufügen"><span class="fa fa-music"></span></button>
                <button @click="addItem" class="btn btn-sm btn-light" title="Vorderfinierten Text hinzufügen"><span
                    class="fa fa-file-alt"></span></button>
                <button class="btn btn-sm btn-danger" title="Abschnitt löschen"
                        @click="deleteBlock"><span class="fa fa-trash"></span>
                </button>
            </div>
        </div>
        <draggable :list="items" class="liturgy-items-list" group="items" @change="updateBlock">
            <div v-for="item in items" class="liturgy-item">
                <div class="row">
                    <div class="col-8">{{ item.title }}</div>
                    <div class="col-4"><input type="text" class="form-control" /></div>
                </div>
            </div>
        </draggable>
    </div>
</template>

<script>
import draggable from "vuedraggable";

export default {
    name: "LiturgyBlock",
    components: {
        draggable,
    },
    data() {
        return {
            blockData: this.block,
            items: this.block.items || [],
            focused: false,
        }
    },
    props: ['block', 'index'],
    methods: {
        focus() {
            this.focused = !this.focused;
        },
        deleteBlock() {
            this.$emit('delete-block');
        },
        changeTitle() {
            this.blockData.title = prompt('Wie soll dieser Abschnitt heißen?', this.blockData.title);
            this.updateBlock();
        },
        addItem() {
            this.items.push({title: 'Neues Element #'+(this.index+1)+'.'+(this.items.length+1)})
            this.updateBlock();
        },
        updateBlock() {
            this.blockData.items = this.items;
            this.$emit('update-block', this.index, this.blockData);
        }
    }
}
</script>

<style scoped>
.liturgy-block {
    padding: 3px 5px;
    margin: 5px;
    border-top: solid 1px lightgray;
    border-bottom: solid 1px lightgray;
}

.liturgy-block.focused {
    box-shadow: 0 0 5px rgba(81, 203, 238, 1);
    border: 1px solid rgba(81, 203, 238, 1);
}

.liturgy-items-list {
    min-height: 10px;
    background: #eeeeee;
}

.liturgy-block-title {
    font-weight: bold;
    font-size: 1.4em;
    color: rgb(145, 45, 125);
}
.liturgy-item {
    border-top: dotted 1px gray;
    padding: 3px 0;
    margin: 3px 5px;
}
</style>
