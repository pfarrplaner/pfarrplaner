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
    <div class="comments-tab">
        <div v-for="(comment,key,index) in service.comments" :key="key" :index="index" class="alert alert-info">
            <div class="row">
                <div class="col-11">
                    <p class="comment-header pull-left">
                        <span class="comment-author"><span class="mdi mdi-account"></span> {{ comment.user.name }}</span>
                        <span v-if="comment.private" class="mdi mdi-lock" title="Dieser Kommentar ist nur für mich sichtbar."></span><br />
                        {{ moment(comment.created_at).locale('de-DE').format('LLLL') }}</p>
                </div>
                <div class="col-1 text-right">
                    <button class="btn btn-sm btn-danger" title="Kommentar löschen" @click.prevent="deleteComment(comment.id, key, index)">
                        <span class="mdi mdi-delete"></span>
                    </button>
                </div>
            </div>
            <p class="comment-body" v-html="comment.body.replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '<br />')" />
        </div>
        <hr />
        <h3>Neuen Kommentar hinzufügen</h3>
        <form-check name="private" v-model="newComment.private" label="Dieser Kommentar soll nur für mich sichtbar sein" />
        <form-textarea name="body" v-model="newComment.body" label="Kommentar" />
        <button class="btn btn-sm btn-secondary" @click.prevent="saveComment">Kommentar speichern</button>
    </div>
</template>

<script>
import FormCheck from "../../Ui/forms/FormCheck";
import FormTextarea from "../../Ui/forms/FormTextarea";
export default {
    name: "CommentsTab",
    components: {FormTextarea, FormCheck},
    props: ['service'],
    data() {
        return {
            myService: this.service,
            newComment: {
                private: false,
                body: '',
                commentable_id: this.service.id,
                commentable_type: 'App\\Service',
            }
        }
    },
    methods: {
        saveComment() {
            axios.post(route('comment.store'), this.newComment, {
                headers: { Accept: 'application/json' }
            })
            .then((response)=>{
                this.myService.comments.push(response.data);
                this.newComment.body = '';
                this.newComment.private = false;
            });
        },
        deleteComment(id, key, index) {
            axios.delete(route('comment.destroy', id))
                .then(this.myService.comments.splice(key, 1));
        }
    }
}
</script>

<style scoped>
    .comment-header {
        font-size: .8em;
    }
    .comment-author {
        font-weight: bold;
    }
</style>
