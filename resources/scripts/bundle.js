window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');
    require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// adminlte
//require('../../public/adminlte/js/adminlte.js');
require('admin-lte');
require('jquery-ui');
window.moment = require('moment');
require('bootstrap-datepicker');
require('tempusdominus-bootstrap-4');
require('selectize');

import(/* webpackIgnore: true */ 'https://cdnjs.cloudflare.com/ajax/libs/jquery-date-range-picker/0.20.0/jquery.daterangepicker.min.js');

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
            placeholder: 'TT.MM.JJJJ HH:MM',
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
