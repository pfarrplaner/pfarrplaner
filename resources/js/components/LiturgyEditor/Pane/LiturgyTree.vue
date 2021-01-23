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
    <div class="card">
        <div class="card-header">Ablauf der Liturgie</div>
        <div class="card-body">
            <draggable :list="blocks" group="blocks" v-bind:="{ghostClass: 'ghost-block'}" class="liturgy-blocks-list" @start="focusOff">
                <div v-for="block,blockIndex in blocks" class="liturgy-block" :class="{focused: (focusedBlock == blockIndex) && (focusedItem == null)}" @click="focusBlock(blockIndex)">
                    <div class="row">
                        <div class="col-8 liturgy-block-title">
                            {{ block.title}}
                        </div>
                        <div class="col-4 text-right">
                            <button @click.stop="addItem(blockIndex, 'Freetext')" class="btn btn-sm btn-light" title="Freitext hinzufügen"><span class="fa fa-file"></span>
                            </button>
                            <button @click.stop="addItem(blockIndex, 'Psalm')" class="btn btn-sm btn-light" title="Psalm hinzufügen"><span class="fa fa-praying-hands"></span>
                            </button>
                            <button @click.stop="addItem(blockIndex, 'Reading')" class="btn btn-sm btn-light" title="Schriftlesung hinzufügen"><span class="fa fa-bible"></span>
                            </button>
                            <button @click.stop="addItem(blockIndex, 'Sermon')" class="btn btn-sm btn-light" title="Predigt hinzufügen"><span
                                class="fa fa-microphone-alt"></span></button>
                            <button @click.stop="addItem(blockIndex, 'Song')" class="btn btn-sm btn-light" title="Lied hinzufügen"><span class="fa fa-music"></span></button>
                            <button @click.stop="addItem(blockIndex, 'Liturgic')" class="btn btn-sm btn-light" title="Liturgischen Text hinzufügen"><span
                                class="fa fa-file-alt"></span></button>
                        </div>
                    </div>

                    <draggable :list="block.items" group="items" class="liturgy-items-list" v-bind:="{ghostClass: 'ghost-item'}" @start="focusOff">
                        <div v-for="item,itemIndex in block.items" class="liturgy-item" @click.stop="focusItem(blockIndex, itemIndex)" :class="{focused: (focusedBlock == blockIndex) && (focusedItem == itemIndex)}">
                            {{ item.title }}</div>
                    </draggable>
                </div>
            </draggable>
        </div>
        <div class="card-footer">
            <button class="btn btn-success" @click="addBlock"><span class="fa fa-paragraph"></span> Abschnitt hinzufügen
            </button>
            <button class="btn btn-primary" @click="save"">Speichern</button>
        </div>
        <hr />
        <pre>{{ JSON.stringify(blocks, null, 4) }}</pre>
    </div>
</template>

<script>
import draggable from 'vuedraggable'
import LiturgyBlock from "../Elements/LiturgyBlock";
import { BlockType, FreetextType, LiturgicType, PsalmType, ReadingType, SermonType, SongType } from "../Types/LiturgyItemTypes";

export default {
    name: "LiturgyTree",
    components: {
        LiturgyBlock,
        draggable
    },
    props: ['service'],
    beforeUnmount() {
        // here we need to do some dirty checking and saving!
    },
    data() {
        return {
            focusedBlock: null,
            focusedItem: null,
        }
    },
    computed: {
        blocks() {
            var myBlocks = this.service.liturgy_blocks;
            myBlocks.forEach(function(val, idx){
                myBlocks[idx].type = 'block';
                myBlocks[idx].typeDescription = 'Abschnitt';
            });
            return myBlocks;
        }
    },
    methods: {
        addBlock() {
            var index = this.blocks.push(new BlockType());
            this.focusBlock(index-1);
        },
        deleteBlock(index) {
            this.blocks.splice(index, 1);
        },
        addItem(blockIndex, type) {
            var obj;
            switch (type) {
                case 'Freetext':
                    obj = new FreetextType();
                    break;
                case 'Liturgic':
                    obj = new LiturgicType();
                    break;
                case 'Psalm':
                    obj = new PsalmType();
                    break;
                case 'Reading':
                    obj = new ReadingType();
                    break;
                case 'Sermon':
                    obj = new SermonType();
                    break;
                case 'Song':
                    obj = new SongType();
                    break;
            }
            var index = this.blocks[blockIndex].items.push(obj);
            this.focusItem(blockIndex, index-1);
        },
        focusBlock(blockIndex) {
            if (this.focusedBlock == blockIndex) {
                this.focusOff();
            } else {
                this.focusOff();
                this.focusedBlock = blockIndex;
                this.focusedItem = null;
                this.updateFocus(this.blocks[blockIndex]);
            }
        },
        focusItem(blockIndex, itemIndex) {
            if ((this.focusedBlock == blockIndex) && (this.focusedItem == itemIndex)) {
                this.focusOff();
            } else {
                this.focusOff();
                this.focusedBlock = blockIndex;
                this.focusedItem = itemIndex;
                this.updateFocus(this.blocks[blockIndex].items[itemIndex]);
            }
        },
        focusOff() {
            this.focusedBlock = this.focusedItem = null;
            this.updateFocus(null);
        },
        updateFocus(object) {
            console.log('hi');
            this.$emit('update-focus', this.focusedBlock, this.focusedItem, object);
        },
        save() {
            // TODO:
            // save strategy:
            // 1. save blocks
            // 2. save items

y
            this.$inertia.post(route('services.liturgy.save', {service: this.service}), {blocks: this.blocks}, { preserveState: true});
        }
    }
}
</script>

<style scoped>




.liturgy-block {
    border-top: solid 1px darkgray;
    padding: 3px 5px;
    margin: 5px;
    cursor: pointer;
}

.liturgy-block.focused {
    box-shadow: 0 0 5px rgba(81, 203, 238, 1);
    border: 1px solid rgba(81, 203, 238, 1);
}

.liturgy-blocks-list .liturgy-block:first-child {
    border-top: 0;
}


.liturgy-items-list {
    min-height: 10px;
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
    cursor: pointer;
}

.liturgy-item.focused {
    box-shadow: 0 0 5px rgba(81, 203, 238, 1);
    border: 1px solid rgba(81, 203, 238, 1);
}


.liturgy-items-list .liturgy-item:first-child {
    border-top: 0;
}

.ghost-item {

}

</style>
