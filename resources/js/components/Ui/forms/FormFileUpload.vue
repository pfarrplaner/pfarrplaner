<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
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
    <form-group :name="name" :label="label" :help="help">
        <div class="upload-container">
            <div class="drop-target" :key="dragging" :class="{dragging: dragging}">
                <input class="form-control-file"
                       type="file" :name="name" @change="handleInput" :multiple="multi"
                       @dragenter="dragEnter" @dragleave="dragLeave"/>
                Hier klicken oder Dateien hierher ziehen...
            </div>
        </div>
    </form-group>
</template>

<script>
import FormGroup from "./FormGroup";

export default {
    name: "FormFileUpload",
    props: ['name', 'label', 'help', 'multiple'],
    components: {FormGroup},
    data() {
        return {
            dragging: false,
            multi: this.multiple ? true : false,
        }
    },
    methods: {
        handleInput(event) {
            if (this.multi) {
                Array.from(event.target.files).forEach(file => {
                    this.$emit('input', file)
                });
            } else {
                this.$emit('input', event.target.files[0])
            }
            this.dragging = false;
        },
        dragEnter(e) {
            this.dragging = true;
        },
        dragLeave(e) {
            this.dragging = false;
        },
    }
}
</script>

<style scoped>

.upload-container {
    display: inline-block;
    position: relative;
    height: 6em;
    width: 100%;
}

.drop-target {
    position: absolute;
    top: 0;
    bottom: 0;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: .35rem;
    font-weight: bold;
    color: darkgray;

    min-height: 6rem;
    vertical-align: center;
    text-align: center;
    border: solid 1px rgb(206, 212, 218);
    background-color: #e9ecef;
}

.drop-target.dragging {
    background-color: #fff;
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.form-control-file {
    position: absolute;
    left: 0;
    opacity: 0;
    top: 0;
    bottom: 0;
    width: 100%;
}
</style>
