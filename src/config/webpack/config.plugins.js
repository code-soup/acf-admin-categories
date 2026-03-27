/**
 * Webpack plugins configuration
 */

const webpack = require('webpack');
const { WebpackManifestPlugin } = require('webpack-manifest-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = (config, env) => {
    // Base plugins that are always included
    const basePlugins = [
        new MiniCssExtractPlugin({
            filename: `styles/${config.fileName}.css`,
            chunkFilename: `styles/[id].${config.fileName}.css`,
        }),
        new webpack.ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery',
            'window.jQuery': 'jquery',
        }),
    ];

    // Conditional plugins
    const conditionalPlugins = [];

    // Manifest plugin - only in production
    if (env.isProduction) {
        conditionalPlugins.push(
            new WebpackManifestPlugin({ publicPath: '' })
        );
    }

    // Combine all plugins
    return [...basePlugins, ...conditionalPlugins];
};
