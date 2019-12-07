@if($slave)
    <script>var slave = 1;</script>
@else
    <script>var slave = 0;</script>
@endif
<script>
    function setLimitedColumnStatus() {
        if ($('.btn-toggle-limited-days').find('span').first().hasClass('fa-square')) {
            $('.btn-toggle-limited-days').attr('title', 'Alle ausgeblendeten Tage einblenden');
            $('.limited').addClass('collapsed');
            $.get('{{ route('showLimitedColumns', ['switch' => 0]) }}');
        } else {
            $('.btn-toggle-limited-days').attr('title', 'Alle ausblendbaren Tage ausblenden');
            $('.limited').removeClass('collapsed');
            $.get('{{ route('showLimitedColumns', ['switch' => 1]) }}');
        }
    }

    var lastUpdate = null;

    function checkForUpdates() {
        $('#heartbeat').removeClass('fa-sync').addClass('fa-heart').css('color', 'red');
        fetch('{{ route('lastUpdate') }}')
            .then(response => response.json())
            .then(data => {
                if (null === lastUpdate) {
                    lastUpdate = data.update
                } else {
                    if (data.update != lastUpdate) {
                        $(".page").delay(800).fadeOut(400, function () {
                            $(".loader").fadeIn(400);
                        });
                        window.location.href = data.route;
                    }
                }
                $('#heartbeat').removeClass('fa-heart').addClass('fa-sync').css('color', '#fff');
            })
    }

    $(document).ready(function () {

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
        $('.limited .service-entry.mine').parent().parent().removeClass('collapsed');

        if (slave) {
            var t = setInterval(checkForUpdates, 2000);
        }
    });

</script>
<script src="{{ asset('js/pfarrplaner/loader.js') }}"></script>
