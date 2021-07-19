const mix = require('laravel-mix');

mix.options({
    uglify: {
        uglifyOptions: {
            compress: true,
            mangle: true,
            output: {
                comments: false,
                beautify: false,
            }
        },
    }
})

mix.styles([
    'assets/css/fomantic-ui/transition.min.css',
    'assets/css/fomantic-ui/dropdown.min.css',
    'assets/css/fomantic-ui/dimmer.min.css',
    'assets/css/fomantic-ui/modal.min.css',
    'assets/css/fomantic-ui/popup.min.css',
    'assets/css/fomantic-ui/table.min.css',
    'assets/css/fomantic-ui/calendar.min.css',
    'assets/css/fomantic-ui/toast.min.css',
    'assets/css/fomantic-ui/tab.min.css',
    'assets/css/swiper-bundle.min.css',
    'assets/css/styles.css',
    'assets/css/instructor-role-plugin.css',
    'assets/css/quiz-reporting-plugin.css',
    //'assets/css/ld-focus-dark-mode.css'
], 'build/styles.css');

mix.scripts([
    'assets/js/fomantic-ui/transition.min.js',
    'assets/js/fomantic-ui/dropdown.min.js',
    'assets/js/fomantic-ui/dimmer.min.js',
    'assets/js/fomantic-ui/modal.min.js',
    'assets/js/fomantic-ui/popup.min.js',
    'assets/js/fomantic-ui/table.min.js',
    'assets/js/fomantic-ui/calendar.min.js',
    'assets/js/fomantic-ui/toast.min.js',
    'assets/js/fomantic-ui/tab.min.js',
    'assets/js/swiper-bundle.min.js',
    'assets/js/scripts.js'
], 'build/scripts.js');

mix.js([
    'assets/js/app.js'
], 'build/app.js').vue();