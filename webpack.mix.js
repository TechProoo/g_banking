const mix = require("laravel-mix");

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js("resources/js/app.js", "public/js");

// Provide polyfills for node core modules that older webpack configurations expect
mix.webpackConfig({
    resolve: {
        fallback: {
            https: require.resolve("https-browserify")
        }
    }
});

if (mix.inProduction()) {
    mix.version();
}
