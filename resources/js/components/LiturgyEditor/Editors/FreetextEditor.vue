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
    <div class="liturgy-item-freetext-editor">
        <div class="form-group">
            <label for="title">Titel im Ablaufplan</label>
            <input class="form-control" v-model="editedElement.title" v-focus/>
        </div>


        <div class="form-group">
            <label for="description">Beschreibender Text</label>
            <div class="dropdown mb-1">
                <div class="btn-group">
                    <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                            @click="toggleTextDropdown"
                            title="Textbaustein einfügen">
                        <span class="mdi mdi-text"></span> Textbaustein einfügen
                    </button>
                    <button class="btn btn-light dropdown-toggle" type="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                            @click="toggleWordImportDropdown"
                            title="Aus Worddokument importieren">
                        <span class="mdi mdi-file-word"></span>
                    </button>
                </div>
                <div class="dropdown-menu bg-light" :style="{display: showTextDropdown ? 'block' : 'none'}"
                     aria-labelledby="dropdownMenuButton">
                    <div class="dropdown-form p-1">
                        <div class="row">
                            <div class="col-sm-6">
                                <div v-if="lists.texts.length > 0">
                                    <form-selectize label="Textbaustein" :options="lists.texts"
                                                    title-key="title"
                                                    v-model="selectedText" :key="lists.texts.length"/>
                                </div>
                                <nav-button type="secondary" icon="mdi mdi-text"
                                            @click="insertText(lists.texts.filter(item => item.id == selectedText)[0].text); showTextDropdown = false;"
                                            title="Einfügen">Einfügen
                                </nav-button>
                            </div>
                            <div class="col-sm-6">
                                <nl2br tag="div" v-if="selectedText"
                                       :text="lists.texts.filter(item => item.id == selectedText)[0].text"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="dropdown-menu bg-light" :style="{display: showWordImportDropdown ? 'block' : 'none'}"
                     aria-labelledby="dropdownMenuButton">
                    <div class="dropdown-form p-1">
                        <form-file-upload @input="upload"
                                          no-url="1" no-pixabay="1" no-camera="1" no-description="1" />
                    </div>
                </div>
            </div>
            <textarea class="form-control" v-model="editedElement.data.description" ref="textEditor"
                      rows="15"></textarea>
            <text-stats :text="editedElement.data.description"/>
        </div>
    </div>
</template>

<script>
import Nl2br from 'vue-nl2br';
import TextStats from "../Elements/TextStats";
import FormInput from "../../Ui/forms/FormInput";
import FormSelectize from "../../Ui/forms/FormSelectize";
import NavButton from "../../Ui/buttons/NavButton";
import FormFileUploader from "../../Ui/forms/FormFileUploader";
import FormFileUpload from "../../Ui/forms/FormFileUpload";

export default {
    name: "FreetextEditor",
    components: {FormFileUpload, NavButton, FormSelectize, FormInput, TextStats, Nl2br},
    inject: ['lists'],
    props: {
        element: Object,
        service: Object,
        agendaMode: {
            type: Boolean,
            default: false,
        },
        markers: {
            type: Object,
            default: null,
        },
    },
    data() {
        var e = this.element;
        if (undefined == e.data.description) e.data.description = '';
        return {
            apiToken: this.$page.props.currentUser.data.api_token,
            editedElement: e,
            selectedText: '',
            showTextDropdown: false,
            showWordImportDropdown: false,
        };
    },
    methods: {
        insertText(text) {
            var myField = this.$refs['textEditor'];

            if (myField.selectionStart || myField.selectionStart == '0') {
                var startPos = myField.selectionStart;
                var endPos = myField.selectionEnd;
                this.editedElement.data.description = myField.value.substring(0, startPos)
                    + text
                    + myField.value.substring(endPos, myField.value.length);
                myField.focus();
                myField.selectionStart = myField.selectionEnd = myField.selectionEnd + text.length;
            } else {
                this.editedElement.data.description += text;
                myField.focus();
            }
        },
        toggleTextDropdown() {
            this.showTextDropdown = !this.showTextDropdown;
            if (!this.showTextDropdown) this.$refs['textEditor'].focus();
        },
        toggleWordImportDropdown() {
            this.showWordImportDropdown = !this.showWordImportDropdown;
            if (!this.showWordImportDropdown) this.$refs['textEditor'].focus();
        },
        upload(file) {
            let fd = new FormData();
            fd.append('import', file);

            this.uploading = true;
            axios.post(route('api.liturgy.text.import', {
                api_token: this.apiToken,
            }), fd, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(response => {
                this.showWordImportDropdown = false;
                this.insertText(response.data);
                this.$refs['textEditor'].focus();
            });
        },
    },
}
</script>

<style scoped>
.liturgy-item-freetext-editor {
    padding: 5px;
}

.help {
    margin-top: .5em;
    margin-bottom: .5em;
    font-size: .8em;
}

.help-title {
    font-weight: bold;
    margin-bottom: .25em;
}

.dropdown-menu {
    width: 100% !important;
}
</style>
