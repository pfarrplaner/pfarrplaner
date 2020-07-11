/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/potofcoffee/pfarrplaner
 * @version git: $Id$
 *
 * Sponsored by: Evangelischer Kirchenbezirk Balingen, https://www.kirchenbezirk-balingen.de
 *
 * Pfarrplaner is based on the Laravel framework (https://laravel.com).
 * This file may contain code created by Laravel's scaffolding functions.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

function updateCommentCount() {
    $('#commentCount').html($('.commentRow').length);
}

function commentEditButtons() {
    $('.btnEditComment').click(function(event){
        event.preventDefault();
        axios.get($(this).data('route'), {
            id: $(this).data('comment-id'),
        }).then((response) => {
            console.log(response);
            $(this).closest('tr').replaceWith(response.data);
            commentEditButtons();
        }).catch((error)=>{
            console.log(error.response.data)
        });
    });

    $('.btnEditCommentSave').click(function(event){
        event.preventDefault();
        var tr = $(this).closest('tr');
        axios.patch(tr.data('update-route'), {
            id: tr.data('comment-id'),
            'commentable_id': commentOwner,
            'commentable_type': commentOwnerClass,
            'body': tr.find('.editCommentBody').first().val(),
            'private': tr.find('.editCommentPrivate').first().prop('checked'),
        }).then((response) => {
            console.log(response);
            $(this).closest('tr').replaceWith(response.data);
            commentEditButtons();
        }).catch((error)=>{
            console.log(error.response.data)
        });
    });


    $('.btnDeleteComment').click(function(){
        event.preventDefault();
        axios.delete($(this).data('route'), {
            id: $(this).data('comment-id'),
        }).then((response) => {
            console.log(response);
            $(this).closest('tr').remove();
            updateCommentCount();
            setDirty();
        });
    });
}

$(document).ready(function(){
    $('#newCommentSave').click(function(event){
        event.preventDefault();

        axios.post(commentRoute, {
            'commentable_id': commentOwner,
            'commentable_type': commentOwnerClass,
            'body': $('#newCommentRow .editCommentBody').first().val(),
            'private': $('#newCommentRow .editCommentPrivate').first().prop('checked'),
        })
    .then((response)=>{
            console.log(response);
            $('#newCommentRow').before(response.data);
            updateCommentCount();
            setDirty();
            $('#newCommentRow .editCommentBody').first().val('');
            $('#newCommentRow .editCommentPrivate').first().prop('checked', false);
            commentEditButtons();
        }).catch((error)=>{
            console.log(error.response.data)
        });
    });

    commentEditButtons();
});

