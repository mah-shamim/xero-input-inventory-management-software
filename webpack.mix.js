const mix = require('laravel-mix')
const tailwindcss = require('tailwindcss')


// '#' mean views directory
mix.webpackConfig({
    resolve: {
        alias: {
            '~': path.resolve(
                __dirname,
                'resources/js/components'
            ),
            'g~': path.resolve(
                __dirname,
                'resources/js/components/global'
            ),
            'i#': path.resolve(
                __dirname,
                'resources/js/views/inventory'
            ),
            'p#': path.resolve(
                __dirname,
                'resources/js/views/payroll'
            ),
            '#': path.resolve(
                __dirname,
                'resources/js/views'
            )
        }
    }
});

mix.copy('node_modules/bootstrap/dist/css/bootstrap.min.css', 'public/css/bootstrap.min.css')
//inventory
mix.js('resources/js/inventoryApp.js', 'public/js')
    .sass('resources/sass/inventoryApp.scss', 'public/css')
    .options({
        processCssUrls: true,
        sourceMap: true,
        postCss: [
            tailwindcss('tailwind.config.js'),
        ]
    })
    .version()
    .browserSync({"proxy": "localhost:8000"});

//payroll
mix.js('resources/js/payrollApp.js', 'public/js')
    .sass('resources/sass/payrollApp.scss', 'public/css')
    .options({
        processCssUrls: true,
        sourceMap: true,
        postCss: [
            tailwindcss('tailwind.config.js'),
        ]
    })
    .version()
    .browserSync({"proxy": "localhost:8000"});


//report
mix.js('resources/js/reportApp.js', 'public/js')
    .sass('resources/sass/reportApp.scss', 'public/css')
    .options({
        processCssUrls: true,
        sourceMap: true,
        postCss: [
            tailwindcss('tailwind.config.js'),
        ]
    })
    .version()
    .browserSync({"proxy": "localhost:8000"});


mix.js('resources/documentations/documentation.js', 'public/documentations/js')
    .sass('resources/documentations/documentation.scss', 'public/documentations/css')
    .options({
        processCssUrls: true,
        sourceMap: true,
        postCss: [
            tailwindcss('tailwind.config.js'),
        ]
    })
    .version()
    .browserSync({"proxy": "localhost:8000"});


//home
mix.js('resources/js/static.js', 'public/js')
    .sass('resources/sass/static.scss', 'public/css')
    .options({
        processCssUrls: true,
        sourceMap: true,
        postCss: [
            tailwindcss('tailwind.config.js'),
        ]
    })
    .browserSync({
        host: '0.0.0.0',
        proxy: 'localhost:8000',
        open: false,
        watchOptions: {
            usePolling: true,
            interval: 500
        }
    });
