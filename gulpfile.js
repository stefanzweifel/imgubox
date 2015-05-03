var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Less
 | file for our application, as well as publishing vendor resources.
 |
 */

var paths = {
    'jquery': './resources/vendor/jquery/',
    'bootstrap': './resources/vendor/bootstrap-sass/assets/'
}


elixir(function(mix) {


    mix.sass("app.scss", 'public/css/', {includePaths: [paths.bootstrap + 'stylesheets/']})
        .copy(paths.bootstrap + 'fonts/bootstrap/**', 'public/fonts')
        .scripts([
            'vendor/jquery/dist/jquery.js',
            'vendor/bootstrap/dist/js/bootstrap.min.js',
            'js/app.js',
        ], 'public/js/app.js', 'resources/');





});
