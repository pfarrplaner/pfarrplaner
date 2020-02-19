const mix = require('laravel-mix');

module.exports = {
    module: {
        rules: [
            {
                test: /\.css$/i,
                use: ['style-loader', 'css-loader'],
            },
        ],
    },
};

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

//mix.js('resources/js/app.js', 'public/js-old')
//   .sass('resources/sass/app.scss', 'public/css-old');

mix.js('resources/scripts/bundle.js', 'public/js')
    .styles(['https://fonts.googleapis.com/css?family=Nunito',
    'resources/css/pfarrplaner.css'], 'public/css/pfarrplaner.css');
    //.js('resources/scripts/pfarrplaner-scripts.js', 'public/js');
