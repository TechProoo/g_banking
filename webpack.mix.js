const mix = require("laravel-mix");
const webpack = require("webpack");

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
            https: require.resolve("https-browserify"),
            http: require.resolve("stream-http"),
            stream: require.resolve("stream-browserify"),
            crypto: require.resolve("crypto-browserify"),
            path: require.resolve("path-browserify"),
            zlib: require.resolve("browserify-zlib"),
            constants: require.resolve("constants-browserify"),
            assert: require.resolve("assert"),
            util: require.resolve("util"),
            fs: false,
            net: false,
            tls: false,
        },
    },
    plugins: [
        new webpack.ProvidePlugin({
            process: "process/browser",
            Buffer: ["buffer", "Buffer"],
        }),
    ],
});

if (mix.inProduction()) {
    mix.version();
}
