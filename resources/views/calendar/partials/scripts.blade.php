@if($slave)
    <script>var slave = 1;</script>
@else
    <script>var slave = 0;</script>
@endif
<script>

    var month =  {{ $month }};
    var year =  {{ $year }};
    var route = '{{ route('calendar', compact('year', 'month')) }}';

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
        fetch('{{ route('services.currentUser.lastUpdate') }}')
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

</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-sortable/0.9.13/jquery-sortable-min.js"></script>
<script src="{{ asset('js/pfarrplaner/calendar.month.js') }}"></script>
<script src="{{ asset('js/pfarrplaner/loader.js') }}"></script>
