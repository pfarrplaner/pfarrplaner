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


});

