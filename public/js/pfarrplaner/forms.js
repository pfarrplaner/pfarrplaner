$(document).ready(function () {
    $('.fancy-selectize').selectize();


    $('.datepicker').datepicker({
        format: 'dd.mm.yyyy',
        language: 'de',
    });

    $('.datetimepicker').each(function(){
        var value = $(this).val();
        $(this).datetimepicker({
            locale: 'de',
            format: 'DD.MM.Y HH:mm',
            icons: {
                time: 'fa fa-clock',
                date: 'fa fa-calendar',
                up: 'fa fa-arrow-up',
                down: 'fa fa-arrow-down',
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right',
                today: 'fa fa-calendar-check-o',
                clear: 'fa fa-trash',
                close: 'fa fa-times'
            },
            date: value,
        });
        $(this).val(value);

    });
});
