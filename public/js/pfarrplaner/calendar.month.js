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

$(document).ready(function () {

    // enable settings button
    $('#toggleControlSidebar').show();

    // toggle for limited days
    $('.limited').on('click', function (e) {
        if (e.target !== this) return;
        $('[data-day=' + $(this).data('day') + ']').toggleClass('collapsed');
    })

    // toggle all limited days
    $('.btn-toggle-limited-days').on('click', function (e) {
        e.preventDefault();
        $(this).find('span').toggleClass('fa-square').toggleClass('fa-check-square');
        setLimitedColumnStatus();
    });
    setLimitedColumnStatus();

    // open limited days with services that belong to me
    $('.limited .service-entry.mine').each(function(){
        $('[data-day=' + $(this).data('day') + ']').removeClass('collapsed');
    });

    if (slave) {
        var t = setInterval(checkForUpdates, 2000);
    }



    $('input[name=orientation]').change(function(){
        window.location.href = route+'?orientation='+$('input[name=orientation]:checked').val();
    });


    function setCitySortValue() {
        var value = [];
        $('#userCities li').each(function () {
            if ($(this).data('city')) value.push($(this).data('city'));
        });
        $('#applySorting').attr('href', route+'?sort='+value.join(','));
    }

    setCitySortValue();

    $('.citySort').sortable({
        group: 'citySort',
        pullPlaceholder: false,
        // animation on drop
        onDrop: function ($item, container, _super) {
            _super($item, container);
            setCitySortValue();
        },

        // set $item relative to cursor position
        onDragStart: function ($item, container, _super) {
            var offset = $item.offset(),
                pointer = container.rootGroup.pointer;

            adjustment = {
                left: pointer.left - offset.left,
                top: pointer.top - offset.top
            };

            _super($item, container);
        },
        onDrag: function ($item, position) {
            $item.css({
                left: position.left - adjustment.left,
                top: position.top - adjustment.top
            });
        }
    });


    $('#ctrlNameSort').change(function(){
        window.location.href = $(this).data('route')+'?name_format='+$('#ctrlNameSort option:selected').val();
    });

    $('#ctrlFilterLocation').selectize();
    $('#applyFilter').click(function(e){
        e.preventDefault();
        window.location.href = $('#ctrlFilterLocation').data('route')+'?filter_location='+$('#ctrlFilterLocation').val().join(',');
    });

});

