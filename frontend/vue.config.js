const path = require('path');
const CompressionPlugin = require("compression-webpack-plugin");
const LicenseWebpackPlugin = require('license-webpack-plugin').LicenseWebpackPlugin;

// TODO dev mode for api.html
// TODO watch mode does not fully work

const themes = {
    'theme-basic': './src/themes/basic.scss',
    'theme-cerulean': './src/themes/cerulean.scss',
    'theme-cosmo': './src/themes/cosmo.scss',
    'theme-cyborg': './src/themes/cyborg.scss',
    'theme-darkly': './src/themes/darkly.scss',
    'theme-flatly': './src/themes/flatly.scss',
    'theme-journal': './src/themes/journal.scss',
    'theme-litera': './src/themes/litera.scss',
    'theme-lumen': './src/themes/lumen.scss',
    'theme-lux': './src/themes/lux.scss',
    'theme-materia': './src/themes/materia.scss',
    'theme-minty': './src/themes/minty.scss',
    'theme-pulse': './src/themes/pulse.scss',
    'theme-sandstone': './src/themes/sandstone.scss',
    'theme-simplex': './src/themes/simplex.scss',
    'theme-sketchy': './src/themes/sketchy.scss',
    'theme-slate': './src/themes/slate.scss',
    'theme-solar': './src/themes/solar.scss',
    'theme-spacelab': './src/themes/spacelab.scss',
    'theme-superhero': './src/themes/superhero.scss',
    'theme-united': './src/themes/united.scss',
    'theme-yeti': './src/themes/yeti.scss',
};

module.exports = () => {
    const watch = process.env.npm_lifecycle_event === 'watch';
    const production = process.env.NODE_ENV === 'production';
    // noinspection JSUnusedGlobalSymbols
    const config = {
        outputDir: path.resolve(__dirname, '../web/dist'),
        publicPath: production || watch ? 'dist/' : '',
        css: {
            extract: true, // necessary to switch themes in dev mode
        },
        configureWebpack: config => {
            config.entry = themes;
            config.entry.main =  './src/main.ts';
            if (production || watch) {
                config.entry.api =  './src/swagger-ui.js';
            }
            if (production) {
                config.plugins.push(new CompressionPlugin({ // v7 needs webpack 5
                    test: /\.(js|css)$/,
                    threshold: 1,
                    compressionOptions: { level: 6 },
                }));
                config.plugins.push(new LicenseWebpackPlugin({
                    perChunkOutput: false,
                }));
            }
            config.optimization = {
                splitChunks: {
                    minSize: 1, // = no common chunk-vendors for both pages
                }
            };
        },
        chainWebpack: config => {
            config.module
                .rule('datatables')
                .test(/datatables\.net.*\.js$/)
                .use('imports-loader')
                .loader('imports-loader')
                .options({
                    additionalCode: 'var define = false;', // Disable AMD
                })
                .end()
            if (!production) {
                config.plugin('preload').tap(options => {
                    options[0].fileBlacklist.push(/css\/theme-/);
                    options[0].fileBlacklist.push(/js\/theme-/);
                    return options
                })
            }
            if (watch) {
                config.plugin('html').tap(args => {
                    args[0].filename = path.resolve(__dirname, '../web/index.html');
                    return args;
                });
            }
        },
        //devServer: { https: true },
    };
    if (production) {
        config.pages = {
            main: {
                filename: production ? path.resolve(__dirname, '../web/index.html') : '',
                template: 'public/index.html',
                entry: 'src/main.ts',
                chunks: ['chunk-vendors', 'chunk-common', 'main'].concat(Object.getOwnPropertyNames(themes))
            },
            api: {
                filename: path.resolve(__dirname, '../web/api.html'),
                template: 'public/api.html',
                entry: 'src/swagger-ui.js',
            },
        };
    }
    return config;
};
