<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
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
    <form @submit.prevent="closeModal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-dialog" role="document" tabindex="-1">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ title }}</h5>
                            <button v-if="allowCancel" type="button" class="close" data-dismiss="modal"
                                    aria-label="Fenster schließen" @click.prevent="cancelModal">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" ref="body">
                            <slot />
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary"
                                    @click.prevent="closeModal">{{ closeButtonLabel }}
                            </button>
                            <button v-if="allowCancel" type="button" class="btn btn-secondary" @click.prevent="cancelModal">
                                {{ cancelButtonLabel }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>

<script>
export default {
    name: "Modal",
    props: {
        title: String,
        allowCancel: {
            type: Boolean,
            default: true,
        },
        show: {
            type: Boolean,
            default: true,
        },
        closeButtonLabel: {
            type: String,
            default: 'Schließen',
        },
        cancelButtonLabel: {
            type: String,
            default: 'Abbrechen',
        }
    },
    data() {
        return {
            showModal: this.show,
        }
    },
    mounted() {
        this.$emit('shown', this.$refs.body);
    },
    methods: {
        closeModal() {
            this.$emit('close');
        },
        cancelModal() {
            this.$emit('cancel');
        },
    }
}
</script>

<style scoped>
.modal-mask {
    position: fixed;
    z-index: 9998;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, .5);
    display: table;
    transition: opacity .3s ease;
}

.modal-wrapper {
    display: table-cell;
    vertical-align: middle;
}


</style>
