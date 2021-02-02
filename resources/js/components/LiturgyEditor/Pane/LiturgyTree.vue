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
            <draggable :list="blocks" group="blocks" v-bind:="{ghostClass: 'ghost-block'}" class="liturgy-blocks-list"
                       @start="focusOff" @end="saveState" :disabled="!editable">
                <div v-for="block,blockIndex in blocks" class="liturgy-block"
                     :class="{focused: (focusedBlock == blockIndex) && (focusedItem == null)}"
                     @click="focusBlock(blockIndex)">
                    <div class="row">
                        <div class="col-8 liturgy-block-title">
                            <span class="fa fa-chevron-circle-right" style="display: none;"></span> {{ block.title }}
                        </div>
                        <div class="col-4 text-right" v-if="editable">
                            <button @click.stop="addItem(blockIndex, 'Freetext')" class="btn btn-sm btn-light"
                                    title="Freitext hinzufügen"><span class="fa fa-file"></span>
                            </button>
                            <button @click.stop="addItem(blockIndex, 'Psalm')" class="btn btn-sm btn-light"
                                    title="Psalm hinzufügen"><span class="fa fa-praying-hands"></span>
                            </button>
                            <button @click.stop="addItem(blockIndex, 'Reading')" class="btn btn-sm btn-light"
                                    title="Schriftlesung hinzufügen"><span class="fa fa-bible"></span>
                            </button>
                            <button @click.stop="addItem(blockIndex, 'Sermon')" class="btn btn-sm btn-light"
                                    title="Predigt hinzufügen"><span
                                class="fa fa-microphone-alt"></span></button>
                            <button @click.stop="addItem(blockIndex, 'Song')" class="btn btn-sm btn-light"
                                    title="Lied hinzufügen"><span class="fa fa-music"></span></button>
                            <button @click.stop="addItem(blockIndex, 'Liturgic')" class="btn btn-sm btn-light"
                                    title="Liturgischen Text hinzufügen"><span
                                class="fa fa-file-alt"></span></button>
                            <button @click.stop="deleteBlock(blockIndex)" class="btn btn-sm btn-danger"
                                    title="Abschnitt löschen">
                                <span class="fa fa-trash"></span>
                            </button>
                        </div>
                    </div>
                    <details-pane v-if="block.editing == true" :service="service" :element="block"/>

                    <draggable :list="block.items" group="items" class="liturgy-items-list"
                               v-bind:="{ghostClass: 'ghost-item'}" @start="focusOff" @end="saveState"
                               :disabled="!editable">
                        <div v-for="item,itemIndex in block.items" class="liturgy-item"
                             @click.stop="focusItem(blockIndex, itemIndex)"
                             :class="{focused: (focusedBlock == blockIndex) && (focusedItem == itemIndex)}">
                            <div class="row">
                                <div class="col-10 item-title"><span class="fa fa-chevron-circle-right" style="display: none;"></span> {{ item.title }}</div>
                                <div class="col-2 text-right" v-if="editable">
                                    <button @click.stop="deleteItem(blockIndex, itemIndex)"
                                            class="btn btn-sm btn-danger" title="Element löschen">
                                        <span class="fa fa-trash"></span>
                                    </button>
                                </div>
                            </div>
                            <details-pane v-if="item.editing == true" :service="service" :element="item"/>
                        </div>
                    </draggable>
                </div>
            </draggable>
        </div>
        <div class="card-footer">
            <button class="btn btn-success" @click="addBlock"><span class="fa fa-paragraph"></span> Abschnitt hinzufügen
            </button>
        </div>
    </div>
</template>

<script>
import draggable from 'vuedraggable'
import LiturgyBlock from "../Elements/LiturgyBlock";
import {PsalmType, SongType} from "../Types/LiturgyItemTypes";
import DetailsPane from "./DetailsPane";

export default {
    name: "LiturgyTree",
    components: {
        LiturgyBlock,
        DetailsPane,
        draggable
    },
    props: ['service'],
    beforeUnmount() {
        // here we need to do some dirty checking and saving!
    },
    data() {
        var myBlocks = this.service.liturgy_blocks;
        myBlocks.forEach(function (val, idx) {
            myBlocks[idx].data_type = 'block';
            myBlocks[idx].typeDescription = 'Abschnitt';
            myBlocks[idx].editing = false;
            myBlocks[idx].items.forEach(function (val2, idx2) {
                myBlocks[idx].items[idx2].editing = false;
            });
        });

        return {
            blocks: myBlocks,
            focusedBlock: null,
            focusedItem: null,
            editable: true,
        }
    },
    methods: {
        addBlock() {
            this.$inertia.post('/liturgy/' + this.service.id + '/block',
                {title: 'Abschnitt ' + (this.blocks.length + 1)},
                {
                    preserveState: false
                }
            );
        },
        deleteBlock(index) {
            this.$inertia.delete(route('liturgy.block.destroy', {
                service: this.service.id,
                block: this.blocks[index].id,
            }), {
                preserveState: false
            });
        },
        saveState() {
            var i = 0;
            this.blocks.forEach(function (block) {
                block.sortable = i++;
                var j=0;
                block.items.forEach(function (item) {
                    item.sortable = j++;
                })
            })
            this.$inertia.post(route('services.liturgy.save', this.service.id), this.blocks)
        },
        addItem(blockIndex, type) {
            if (!this.editable) return false;
            var obj;
            switch (type) {
                case 'Freetext':
                    this.$inertia.post(route('liturgy.item.store', {
                        service: this.service.id,
                        block: this.blocks[blockIndex].id
                    }), {
                        title: 'Freier Text',
                        data_type: 'freetext',
                        data: {description: ''},
                    }, {preserveState: false});
                    break;
                case 'Liturgic':
                    this.$inertia.post(route('liturgy.item.store', {
                        service: this.service.id,
                        block: this.blocks[blockIndex].id
                    }), {
                        title: 'Liturgischer Text',
                        data_type: 'liturgic',
                        data: { id: -1, title: '', text: ''}
                    }, {preserveState: false});
                    break;
                case 'Psalm':
                    obj = new PsalmType();
                    break;
                case 'Reading':
                    this.$inertia.post(route('liturgy.item.store', {
                        service: this.service.id,
                        block: this.blocks[blockIndex].id
                    }), {
                        title: 'Schriftlesung',
                        data_type: 'reading',
                        data: {reference: ''},
                    }, {preserveState: false});
                    break;
                case 'Sermon':
                    this.$inertia.post(route('liturgy.item.store', {
                        service: this.service.id,
                        block: this.blocks[blockIndex].id
                    }), {
                        title: 'Predigt',
                        data_type: 'sermon',
                    }, {preserveState: false});
                    break;
                case 'Song':
                    obj = new SongType();
                    break;
            }
            var index = this.blocks[blockIndex].items.push(obj);
        },
        deleteItem(blockIndex, itemIndex) {
            this.$inertia.delete(route('liturgy.item.destroy', {
                service: this.service.id,
                block: this.blocks[blockIndex].id,
                item: this.blocks[blockIndex].items[itemIndex].id,
            }), {preserveState: false});
        },
        focusBlock(blockIndex) {
            if (!this.editable) return false;
            if (this.focusedBlock == blockIndex) {
                this.blocks[blockIndex].editing = false;
                this.focusOff();
            } else {
                this.focusOff();
                this.blocks[blockIndex].editing = true;
                this.focusedBlock = blockIndex;
                this.focusedItem = null;
                this.updateFocus(this.blocks[blockIndex]);
            }
        },
        focusItem(blockIndex, itemIndex) {
            if (!this.editable) return false;
            if ((this.focusedBlock == blockIndex) && (this.focusedItem == itemIndex)) {
                this.blocks[blockIndex].items[itemIndex].editing = false;
                this.focusOff();
            } else {
                this.focusOff();
                this.blocks[blockIndex].items[itemIndex].editing = true;
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
            this.editable = (object === null);
            this.$emit('update-focus', this.focusedBlock, this.focusedItem, object);
        },
        save() {
            this.$inertia.post(route('services.liturgy.save', {service: this.service}), {blocks: this.blocks}, {preserveState: true});
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
    margin: 3px 0px;
    cursor: pointer;
}

.liturgy-item.focused .item-title {
    padding-left: 13px;
    font-weight: bold;
}

.liturgy-item.focused .item-title span.fa,
.liturgy-block.focused .liturgy-block-title span.fa {
    display: inline !important;
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
