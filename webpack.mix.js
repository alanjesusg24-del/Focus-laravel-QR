const mix = require('laravel-mix');

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

 mix.js('resources/js/app.js', 'public/js')
 .postCss('resources/css/app.css', 'public/css', [
     //
 ]);
 
 // Compile Volt SCSS (enables overriding variables in resources/scss/custom/_variables.scss)
 mix.sass('resources/scss/volt.scss', 'public/css')
   .options({ processCssUrls: false });

// Silence Sass deprecation warnings coming from node_modules (Bootstrap, etc.)
mix.override((config) => {
    const ensureQuietDeps = (useArray) => {
        if (!Array.isArray(useArray)) return;
        useArray.forEach((u) => {
            if (u && u.loader && u.loader.includes('sass-loader')) {
                u.options = {
                    ...(u.options || {}),
                    sassOptions: {
                        ...(u.options && u.options.sassOptions ? u.options.sassOptions : {}),
                        quietDeps: true,
                        silenceDeprecations: [
                            'legacy-js-api',
                            'import',
                            'global-builtin',
                            'color-functions'
                        ],
                    },
                };
            }
        });
    };

    (config.module.rules || []).forEach((rule) => {
        if (rule.oneOf) {
            rule.oneOf.forEach((one) => ensureQuietDeps(one.use));
        }
        ensureQuietDeps(rule.use);
    });
});