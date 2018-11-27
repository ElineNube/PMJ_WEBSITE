var elixer = require('laravel-elixir');

elixer.config.sourcemap = false;

var gulp = require('gulp');

elixer(function (mix) {

    //compile sass to css

    mix.sass('resources/assets/sass/app.scss', 'resources/assets/css');

    //combine css file

    mix.styles(
        [
            'css/app.css',
            'bower/vendor/slick-carousel/slick/slick.css',


        ], 'public/css/all.css',
        'resources/assets'
    );

    //combine js files

    var bowerPath = 'bower/vendor';
    mix.scripts(
        [
            bowerPath + '/jquery/dist/jquery.min.js',

            bowerPath + '/foundation-sites/dist/js/foundation.min.js',

            bowerPath + '/slick-carousel/slick/slick.min.js'

        ], 'public/js/all.js', 'resources/assets'
    );

});