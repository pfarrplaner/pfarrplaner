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
    <div class="liturgy-tree pb-4">
        <div v-if="reloadingTree" class="tree-loader">
            <span class="mdi mdi-spin mdi-loading"></span>
        </div>
        <div v-else>
            <template slot="toolbar">
            </template>
            <div class="row py-2 border-bottom mb-2">
                <div class="col-md-6">
                    <button class="btn btn-success" @click="addBlock"><span class="mdi mdi-format-section"></span>
                        Abschnitt
                        hinzufügen...
                    </button>
                    <button class="btn btn-light" @click.prevent="modalOpen = true">Ablaufelemente importieren...
                    </button>
                </div>
                <div class="col-md-6 text-right">
                    <div class="dropdown" v-if="hasDownload">
                        <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                title="Dokumente herunterladen">
                            <span class="mdi mdi-download"></span> Herunterladen
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <div v-for="sheet in sheets">
                                <liturgy-sheet-link :service="service" :sheet="sheet"
                                                    @open="dialogs[sheet.key] = true"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <draggable :list="blocks" group="blocks" v-bind:class="{ghostClass: 'ghost-block'}"
                       class="liturgy-blocks-list" :key="treeState+blocks.length"
                       @start="focusOff" @end="saveState" :disabled="!editable" handle=".handle">
                <div v-for="(block,blockIndex) in blocks" class="liturgy-block"
                     :class="{focused: (focusedBlock == blockIndex) && (focusedItem == null)}"
                     @click="focusBlock(blockIndex)" >
                    <div class="row" :ref="'block'+blockIndex" :key="'block'+blockIndex">
                        <div class="col-11 liturgy-block-title">
                        <span class="mdi mdi-drag-horizontal handle mr-1"
                              title="Klicken und ziehen, um die Position im Ablauf zu verändern"></span>
                            <span class="mdi mdi-chevron-right-circle" style="display: none;"></span> {{ block.title }}
                        </div>
                        <div class="col-1 text-right" v-if="editable">
                            <button @click.stop="deleteBlock(blockIndex)" class="btn btn-sm btn-danger"
                                    title="Abschnitt löschen">
                                <span class="mdi mdi-delete"></span>
                            </button>
                        </div>
                    </div>
                    <div class="row" v-if="editable">
                        <div class="col-12">
                            <button @click.stop="addItem(blockIndex, 'Freetext')" class="btn btn-sm btn-light"
                                    title="Freitext hinzufügen"><span class="mdi mdi-text"></span>
                            </button>
                            <button @click.stop="addItem(blockIndex, 'Psalm')" class="btn btn-sm btn-light"
                                    title="Psalm hinzufügen"><span class="mdi mdi-hands-pray"></span>
                            </button>
                            <button @click.stop="addItem(blockIndex, 'Reading')" class="btn btn-sm btn-light"
                                    title="Schriftlesung hinzufügen"><span class="mdi mdi-book-open-variant"></span>
                            </button>
                            <button @click.stop="addItem(blockIndex, 'Sermon')" class="btn btn-sm btn-light"
                                    title="Predigt hinzufügen"><span
                                class="mdi mdi-microphone"></span></button>
                            <button @click.stop="addItem(blockIndex, 'Song')" class="btn btn-sm btn-light"
                                    title="Lied hinzufügen"><span class="mdi mdi-music"></span></button>
                            <button @click.stop="addItem(blockIndex, 'Liturgic')" class="btn btn-sm btn-light"
                                    title="Liturgischen Text hinzufügen"><span
                                class="mdi mdi-text-box"></span></button>
                        </div>
                    </div>
                    <details-pane v-if="block.editing == true" :service="service" :element="block"
                                  @unfocus="cancelEditing($event, blockIndex)"
                                  :agenda-mode="agendaMode" :markers="markers"/>

                    <draggable :list="block.items" group="items" class="liturgy-items-list" handle=".handle"
                               v-bind:class="{ghostClass: 'ghost-item'}" @start="focusOff" @end="saveState"
                               :disabled="!editable">
                        <div v-for="(item,itemIndex) in block.items" class="liturgy-item"
                             @click.stop="focusItem(blockIndex, itemIndex)"
                             :class="{focused: (focusedBlock == blockIndex) && (focusedItem == itemIndex)}"
                             :data-block-index="blockIndex" :data-item-index="itemIndex">
                            <div class="row item" :ref="'block'+blockIndex+'_item'+itemIndex"
                                 title="Klicken, um zu bearbeiten.">
                                <div class="col-sm-3 item-title">
                                    <span class="fa data-type-icon handle mr-1" :class="icons[item.data_type]"
                                          title="Klicken und ziehen, um die Position im Ablauf zu verändern"></span>
                                    <span class="mdi mdi-chevron-right-circle"
                                          style="display: none;"></span> {{ item.title }}
                                </div>
                                <div class="col-sm-4" v-if="item.data_type == 'sermon'">
                                    <div v-if="myService.sermon === null">
                                        <form-selectize v-if="sermons.length > 0" :options="sermons" id-key="id"
                                                        title-key="title"
                                                        label="Bestehende Predigt auswählen"
                                                        :settings="sermonSelectizeSettings"
                                                        @input="setSermon($event, item)"/>
                                        <inertia-link :href="route('service.sermon.editor', {service: myService.slug})"
                                                      @click.stop=""
                                                      class="btn btn-success"
                                                      title="Hier klicken, um die Predigt jetzt anzulegen">
                                            Neue Predigt anlegen
                                        </inertia-link>
                                    </div>
                                    <div v-else>
                                        <inertia-link :href="route('sermon.editor', {sermon: myService.sermon.id})"
                                                      @click.stop="" title="Hier klicken, um die Predigt zu bearbeiten">
                                            {{ myService.sermon.title }}<span
                                            v-if="myService.sermon.subtitle">: {{ myService.sermon.subtitle }}</span>
                                        </inertia-link>
                                        <button class="btn btn-sm btn-light ml-1" @click="setSermon(null, item)"
                                                title="Verknüpfung mit dieser Predigt aufheben">
                                            <span class="mdi mdi-link-off"></span>
                                        </button>
                                        <br/>
                                        <small>{{ myService.sermon.reference }}</small>
                                    </div>
                                </div>
                                <div class="col-sm-4" v-else>{{ itemDescription(item) }}
                                    <span v-if="item.data.needs_replacement" class="badge"
                                          :class="dataReplacerClass(item)">
                                        <span class="mdi mdi-account" :title="dataReplacerTitle(item)"></span>
                                    </span>
                                    <span v-if="(item.data_type=='song') && item.data.song && item.data.song.notation"
                                          class="mdi mdi-music text-success"
                                          title="Zu diesem Lied sind Noten vorhanden."/>
                                </div>
                                <div class="col-sm-2 responsible-list"
                                     @click="editResponsibles(blockIndex, itemIndex, item)">
                                    <people-pane v-if="item.editResponsibles==true" :service="service" :element="item"
                                                 @close="doneEditingResponsibles(item)"
                                                 :ministries="ministries"/>
                                    <div v-else>
                                        <div v-if="item.data.responsible.length > 0">
                                            <span class="badge badge-light" v-for="record in item.data.responsible"
                                                  v-html="displayResponsible(record)"/>
                                        </div>
                                        <div v-else>
                                            <div v-if="editable">
                                                <span class="mdi mdi-account-multiple"></span> Hier klicken, um
                                                Verantwortliche
                                                auszuwählen.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="row">
                                        <item-starting-time class="col-6" :item="item" :service="service"/>
                                        <item-text-stats class="col-6" :item="item" :service="service"/>
                                    </div>
                                </div>
                                <div class="col-1 text-right" v-if="editable">
                                    <button @click.stop="deleteItem(blockIndex, itemIndex)"
                                            class="btn btn-sm btn-danger" title="Element löschen">
                                        <span class="mdi mdi-delete"></span>
                                    </button>
                                </div>
                            </div>
                            <details-pane v-if="item.editing == true" :service="service" :element="item"
                                          :key="treeState+blockIndex+'_'+itemIndex+'_'+(item.editing ? 1 : 2)"
                                          @unfocus="cancelEditing($event, blockIndex, itemIndex)"
                                          :agenda-mode="agendaMode" :markers="markers"/>
                        </div>
                    </draggable>
                </div>
            </draggable>
            <div class="row" v-if="blocks.length > 0">
                <div class="col-sm-7"></div>
                <div class="col-sm-2" style="border-top: solid 1px lightgray;">
                    <small>Berechnetes Ende:</small>
                </div>
                <div class="col-sm-2">
                    <div class="row">
                        <item-starting-time class="col-6" style="border-top: solid 1px lightgray;" :item="{id: -1}"
                                            :service="service"/>
                        <item-starting-time class="col-6" style="border-top: solid 1px lightgray;" :item="{id: -1}"
                                            :service="service" start="00:00"/>
                    </div>
                </div>
            </div>
            <modal title="Elemente importieren" v-if="modalOpen" min-height="50vh"
                   @close="importElements" @cancel="modalOpen = false;"
                   close-button-label="Importieren" cancel-button-label="Abbrechen" max-width="800">
                <div v-if="importFrom != null">
                    <selectize class="form-control source-select" v-model="importFrom" :settings="{
                                placeholder: sourceWait,
                            }">
                        <optgroup label="Vorlagen">
                            <option v-for="agenda in agendas" :value="agenda.id">
                                {{ agenda.text }}
                            </option>
                        </optgroup>
                        <optgroup label="Gottesdienste">
                            <option v-for="service in services" :value="myService.id">
                                {{ myService.text }}
                            </option>
                        </optgroup>
                    </selectize>
                </div>
                <div v-else class="text-align: right; width: 100%; color: darkgray;">
                    Importmöglichquellen werden geladen... <span class="mdi mdi-spin mdi-loading"></span>
                </div>
            </modal>
            <modal v-for="(sheet,sheetKey) in sheets" v-if="dialogs[sheet.key]" :title="sheet.title + ' herunterladen'"
                   :key="'dlg'+sheet.key"
                   @close="downloadConfiguredSheet(sheet)"
                   @cancel="dialogs[sheet.key] = false"
                   close-button-label="Herunterladen" cancel-button-label="Abbrechen">
                <component :is="sheet.configurationComponent" :service="service" :sheet="sheet"/>
            </modal>
        </div>
    </div>
</template>

<script>
import draggable from 'vuedraggable'
import LiturgyBlock from "../Elements/LiturgyBlock";
import DetailsPane from "./DetailsPane";
import PeoplePane from "./PeoplePane";
import Selectize from "vue2-selectize";
import Modal from "../../Ui/modals/Modal";
import LiturgySheetLink from "../Elements/LiturgySheetLink";
import FormSelectize from "../../Ui/forms/FormSelectize";
import FullTextLiturgySheetConfiguration from "../LiturgySheets/FullTextLiturgySheetConfiguration";
import A4WordSpecificLiturgySheetConfiguration from "../LiturgySheets/A4WordSpecificLiturgySheetConfiguration";
import SongPPTLiturgySheetConfiguration from "../LiturgySheets/SongPPTLiturgySheetConfiguration";
import SongSheetLiturgySheetConfiguration from "../LiturgySheets/SongSheetLiturgySheetConfiguration";
import ItemTextStats from "../Elements/ItemTextStats";
import ItemStartingTime from "../Elements/ItemStartingTime";
import NavButton from "../../Ui/buttons/NavButton";

export default {
    name: "LiturgyTree",
    components: {
        NavButton,
        ItemStartingTime,
        ItemTextStats,
        FormSelectize,
        LiturgySheetLink,
        Modal,
        LiturgyBlock,
        DetailsPane,
        PeoplePane,
        draggable,
        Selectize,
        FullTextLiturgySheetConfiguration,
        SongPPTLiturgySheetConfiguration,
        A4WordSpecificLiturgySheetConfiguration,
        SongSheetLiturgySheetConfiguration,
    },
    props: {
        service: Object,
        sheets: Object,
        agendaMode: {
            type: Boolean,
            default: false,
        },
        autoFocusBlock: {
            type: String,
            default: null,
        },
        autoFocusItem: {
            type: String,
            default: null,
        },
        ministries: {
            type: Object,
            default: [],
        },
        markers: {
            type: Object,
            default: null,
        }
    },
    /**
     * Load existing sources
     * @returns {Promise<void>}
     */
    async created() {
        axios.get(route('liturgy.sources', this.myService.slug)).then(response => {
            if (response.data) {
                this.agendas = response.data.agendas;
                this.services = response.data.services;
                this.sourceWait = 'Ablaufelemente importieren...';
                this.importFrom = -1;
            }
        });

        axios.get(route('liturgy.sermons', this.myService.slug)).then(response => this.sermons = response.data);
        axios.get(route('liturgy.text.list', this.myService.slug)).then(response => this.texts = response.data);
        axios.get(route('api.liturgy.song.select', {api_token: this.apiToken})).then(response => {
            this.songList = response.data;
        });
    },
    mounted() {
        if (this.autoFocusItem && this.autoFocusBlock) {
            var autoFocusBlock = parseInt(this.autoFocusBlock);
            var autoFocusItem = parseInt(this.autoFocusItem);
            var foundBlock = false;
            var foundItem = false;
            this.blocks.forEach(function (block, blockIndex) {
                block.items.forEach(function (item, itemIndex) {
                    if ((block.id == autoFocusBlock) && (item.id == autoFocusItem)) {
                        foundBlock = blockIndex;
                        foundItem = itemIndex;
                    }
                }, this);
            }, this);
            if ((foundBlock !== false) && (foundItem !== false)) this.focusItem(foundBlock, foundItem);
        } else {
            if (this.autoFocusBlock) {
                var autoFocusBlock = parseInt(this.autoFocusBlock);
                var foundBlock = false;
                this.blocks.forEach(function (block, blockIndex) {
                    if (block.id == autoFocusBlock) foundBlock = blockIndex;
                }, this);
                if (foundBlock !== false) this.focusBlock(foundBlock);
            }
        }
    },
    beforeUnmount() {
        // here we need to do some dirty checking and saving!
    },
    data() {
        let myService = this.service;
        if (undefined != myService.liturgy_blocks) {
            var myBlocks = myService.liturgy_blocks;
        } else {
            var myBlocks = [];
        }
        myBlocks.forEach(function (val, idx) {
            myBlocks[idx].data_type = 'block';
            myBlocks[idx].typeDescription = 'Abschnitt';
            myBlocks[idx].editing = false;
            myBlocks[idx].items.forEach(function (val2, idx2) {
                myBlocks[idx].items[idx2].editing = false;
                myBlocks[idx].items[idx2].editResponsibles = false;
                if (undefined == myBlocks[idx].items[idx2].data.responsible) myBlocks[idx].items[idx2].data.responsible = [];
            });
        });

        var dialogs = {};
        Object.entries(this.sheets).forEach(sheet => {
            if (sheet[1].configurationComponent) dialogs[sheet[1].key] = false;
        });

        return {
            myService,
            apiToken: this.$page.props.currentUser.data.api_token,
            reloadingTree: false,
            treeState: Math.random().toString(36).substr(2, 9),
            icons: {
                freetext: 'mdi mdi-text',
                psalm: 'mdi mdi-hands-pray',
                reading: 'mdi mdi-book-open-variant',
                sermon: 'mdi mdi-microphone',
                song: 'mdi mdi-music',
                liturgic: 'mdi mdi-text-box',
            },
            blocks: myBlocks,
            focusedBlock: null,
            focusedItem: null,
            editable: true,
            importFrom: null,
            services: [],
            agendas: [],
            hasDownload: (myBlocks.length > 0) && (Object.keys(this.sheets).length > 0),
            sourceWait: 'Bitte warten, Quellen werden geladen...',
            modalOpen: false,
            dialogs: dialogs,
            sermons: [],
            songList: [],
            texts: [],
            sermonSelectizeSettings: {
                searchField: ['title'],
            }
        }
    },
    methods: {
        addBlock() {
            axios.post(route('api.liturgy.block.store', {
                api_token: this.apiToken,
                service: this.service.id,
            }), {
                title: 'Abschnitt ' + (this.blocks.length + 1)
            }).then(response => {
                let block = response.data;
                if (undefined == block.items) block.items = [];
                block.data_type = 'block';
                block.typeDescription = 'Abschnitt';
                block.editing = false;
                let blockIndex = this.blocks.push(response.data);
                this.focusBlock(blockIndex - 1);
            });
        },
        deleteBlock(index) {
            axios.delete(route('api.liturgy.block.destroy', {
                api_token: this.apiToken,
                block: this.blocks[index].id
            })).then(response => {
                this.blocks.splice(index, 1);
            });
        },
        saveState() {
            var i = 0;
            this.blocks.forEach(function (block) {
                block.sortable = i++;
                var j = 0;
                block.items.forEach(function (item) {
                    item.sortable = j++;
                })
            })

            axios.post(route('api.liturgy.tree.save', {service: this.service.id, api_token: this.apiToken}), {
                blocks: this.blocks
            })
                .then(response => this.reloadTree(response.data));
        },
        addItem(blockIndex, type) {
            if (!this.editable) return false;
            var obj;
            switch (type) {
                case 'Freetext':
                    obj = {
                        title: 'Freier Text',
                        data_type: 'freetext',
                        data: {description: ''},
                    };
                    break;
                case 'Liturgic':
                    obj = {
                        title: 'Liturgischer Text',
                        data_type: 'liturgic',
                        data: {id: -1, title: '', text: ''}
                    };
                    break;
                case 'Psalm':
                    obj = {
                        title: 'Psalmgebet',
                        data_type: 'psalm',
                    };
                    break;
                case 'Reading':
                    obj = {
                        title: 'Schriftlesung',
                        data_type: 'reading',
                        data: {reference: ''},
                    };
                    break;
                case 'Sermon':
                    obj = {
                        title: 'Predigt',
                        data_type: 'sermon',
                    };
                    break;
                case 'Song':
                    obj = {
                        title: 'Lied',
                        data_type: 'song',
                    };
                    break;
            }
            axios.post(route('api.liturgy.item.store', {
                block: this.blocks[blockIndex].id,
                api_token: this.apiToken
            }), obj)
                .then(response => {
                    let item = response.data.item;
                    if (undefined == item.data.responsible) item.data.responsible = [];
                    let itemIndex = this.blocks[blockIndex].items.push(item);
                    this.focusItem(blockIndex, itemIndex - 1);
                });
            //var index = this.blocks[blockIndex].items.push(obj);
        },
        deleteItem(blockIndex, itemIndex) {
            axios.delete(route('api.liturgy.item.destroy', {
                api_token: this.apiToken,
                item: this.blocks[blockIndex].items[itemIndex].id,
            })).then(response => {
                this.blocks[blockIndex].items.splice(itemIndex, 1);
            });
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
                this.scrollToRef('block' + blockIndex);
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
        itemDescription(item) {
            switch (item.data_type) {
                case 'freetext':
                    if (null === item.data.description) return '';
                    if (undefined === item.data.description) return '';
                    return item.data.description.length > 40 ? item.data.description.substr(0, 40) + '...' : item.data.description;
                case 'liturgic':
                    return item.data.title;
                case 'psalm':
                    if (undefined == item.data.psalm) return '';
                    var title = item.data.psalm.title;
                    if (item.data.psalm.reference) {
                        title = item.data.psalm.reference + ' ' + title;
                    }
                    if (item.data.psalm.songbook_abbreviation) {
                        title = item.data.psalm.songbook_abbreviation + ' ' + title;
                    } else if (item.data.psalm.songbook) {
                        title = item.data.psalm.songbook + ' ' + title;
                    }
                    return title;
                case 'sermon':
                    return;
                case 'reading':
                    return item.data.reference;
                case 'song':
                    if (undefined == item.data.song) return '';
                    if (undefined == item.data.song.song) return '';
                    console.log(item.data.song);
                    var title = item.data.song.song.title;
                    if (item.data.song.altEG) {
                        title = '(EG '+item.data.song.altEG+') '+title;
                    }
                    if (item.data.song.reference) {
                        title = item.data.song.reference + ' ' + title;
                    }
                    if (item.data.song.code) {
                        title = item.data.song.code + ' ' + title;
                    } else if (item.data.song.songbook) {
                        title = item.data.song.songbook.name + ' ' + title;
                    }
                    if (item.data.verses) {
                        title = title + ', ' + item.data.verses;
                    }
                    return title;
            }
            return '';
        },
        updateFocus(object) {
            this.editable = (object === null);
            this.$emit('update-focus', this.focusedBlock, this.focusedItem, object);
        },
        reloadTree(data = null) {
            if (!data) return;
            if (data.service) this.myService = data.service;
            if (data.tree) {
                this.myService.liturgy_blocks = data.tree;
                this.blocks = data.tree;
            }

            for (const idx in this.blocks) {
                this.blocks[idx].data_type = 'block';
                this.blocks[idx].typeDescription = 'Abschnitt';
                this.blocks[idx].editing = false;
                for (const idx2 in this.blocks[idx].items) {
                    this.blocks[idx].items[idx2].editing = false;
                    this.blocks[idx].items[idx2].editResponsibles = false;
                    if (undefined == this.blocks[idx].items[idx2].data.responsible) this.blocks[idx].items[idx2].data.responsible = [];
                }
            }

            this.reloadingTree = false;
            this.treeState = Math.random().toString(36).substr(2, 9);
        },
        save() {
            this.reloadingTree = true;
            axios.post(route('api.liturgy.tree.save', {
                service: this.service.id,
                api_token: this.apiToken
            }), {blocks: this.blocks})
                .then(response => this.reloadTree(response.data));
        },
        editResponsibles(blockIndex, itemIndex, item) {
            this.editable = false;
            this.focusItem(blockIndex, itemIndex);
            item.editResponsibles = true;
        },
        doneEditingResponsibles(item) {
            item.editResponsibles = false;
            this.focusOff();
            this.editable = true;
        },
        displayResponsible(record) {
            var title = '';
            if (typeof record != 'string') return;
            var tmp = record.split(':');
            if (tmp[0] == 'user') {
                this.myService.participants.forEach(function (person) {
                    if (person.id == tmp[1]) title = '<span class="mdi mdi-account-check"></span> ' + person.name;
                });
                return title;
            } else if (tmp[0] == 'ministry') {
                switch (tmp[1]) {
                    case 'pastors':
                        return '<span class="mdi mdi-account-multiple"></span> Pfarrer*in';
                    case 'organists':
                        return '<span class="mdi mdi-account-multiple"></span> Organist*in';
                    case 'sacristans':
                        return '<span class="mdi mdi-account-multiple"></span> Mesner*in';
                }
                return "<span class=\"mdi mdi-account-multiple\"></span> " + tmp[1];
            } else {
                return '<span class="mdi mdi-account-question"></span> ' + tmp[1];
            }
        },
        importElements() {
            this.modalOpen = false;
            if (this.importFrom == -1) return;
            this.reloadingTree = true;
            axios.post(route('api.liturgy.tree.import', {
                api_token: this.apiToken,
                service: this.service.id,
                source: this.importFrom,
            })).then(response => {
                this.reloadTree(response.data);
            });
        },
        downloadConfiguredSheet(sheet) {
            document.getElementById('frm' + sheet.key).submit();
            this.dialogs[sheet.key] = false;
        },
        dataReplacerTitle(item) {
            if (!item.data.needs_replacement) return '';
            var t = 'Dieses Element wird mit Hilfe von persönlichen Daten ';
            var error = '';
            var replacerObject = null;
            this.service[item.data.needs_replacement + 's'].forEach(obj => {
                if (obj.id == item.data.replacement) replacerObject = obj;
            })
            if (null === replacerObject) item.data.replacement = null;
            switch (item.data.needs_replacement) {
                case 'funeral':
                    t += 'für eine Bestattung';
                    if (!item.data.replacement) {
                        error = 'Es ist noch keine Bestattung ausgewählt!';
                        if (this.myService.funerals.length == 0) error += ' Dem Gottesdienst sind keine Bestattungen zugeordnet!';
                    } else {
                        t += ' (' + replacerObject.buried_name + ')';
                    }
                    break;
                case 'baptism':
                    t += 'für eine Taufe';
                    if (!item.data.replacement) {
                        3
                        error = 'Es ist noch keine Taufe ausgewählt!';
                        if (this.myService.baptisms.length == 0) error += ' Dem Gottesdienst sind keine Taufen zugeordnet!';
                    } else {
                        t += ' (' + replacerObject.candidate_name + ')';
                    }
                    break;
                case 'wedding':
                    t += 'für eine Trauung';
                    if (!item.data.replacement) {
                        error = 'Es ist noch keine Trauung ausgewählt!';
                        if (this.myService.weddings.length == 0) error += ' Dem Gottesdienst sind keine Trauungen zugeordnet!';
                    } else {
                        t += ' (' + replacerObject.spouse1_name + ' / ' + replacerObject.spouse2_name + ')';
                    }
                    break;
            }
            ;
            t += ' angepasst.' + (error ? ' ' + error : '');
            return (t)
        },
        dataReplacerClass(item) {
            if (!item.data.needs_replacement) return '';
            if (!item.data.replacement) return 'badge-danger';
            return 'badge-success';
        },
        setSermon(e, item) {
            this.myService.sermon_id = e;
            axios.patch(route('service.setsermon', this.myService.slug), {sermon_id: e ?? null});
            if (e) {
                this.sermons.forEach(sermon => {
                    if (sermon.id == e) this.myService.sermon = sermon;
                });
            } else {
                this.myService.sermon = null;
            }
            item.editing = false;
            this.focusedItem = null;
            this.focusedBlock = null;
        },
        scrollToRef(refId) {
            this.$nextTick(function () {
                let el = this.$refs[refId].$el;
                if (undefined == el) return;
                window.scrollTo(el.offsetLeft, el.offsetTop);
            });
        },
        cancelEditing(item, blockIndex, itemIndex = null)  {
            if (undefined == item.data.responsible) item.data.responsible = [];
            console.log('cancelEditing', blockIndex, itemIndex);
            if (null !== itemIndex) {
                this.blocks[blockIndex].items[itemIndex] = item;
                this.blocks[blockIndex].items[itemIndex].editing = false;
            } else {
                this.blocks[blockIndex] = item;
                this.blocks[blockIndex].editing = false;
            }
            this.treeState = Math.random().toString(36).substr(2, 9);
            this.$forceUpdate();
            this.focusOff();
        }
    },
    provide() {
        const lists = {};

        Object.defineProperty(lists, 'songs', {
            enumerable: true,
            get: () => this.songList,
        });
        Object.defineProperty(lists, 'texts', {
            enumerable: true,
            get: () => this.texts,
        });
        return {
            lists,
        }
    }
}
</script>

<style scoped>

.tree-loader {
    margin-top: 0;
    font-size: 6em;
    padding-top: 25vh;
    text-align: center;
}

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

.responsible-list {
    color: gray;
}

.source-select {
    text-align: left;
}

.ghost-item {

}

.handle {
    cursor: move;
}

.item .handle {
    color: gray;
}

.data-type-icon {
    color: gray;
}

.row.item:hover {
    background-color: rgb(248, 249, 250);
}

</style>
