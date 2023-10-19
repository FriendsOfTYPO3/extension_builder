"use strict";

const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');
const TerserPlugin = require('terser-webpack-plugin');

module.exports = {
    entry: {
        backend: './Build/Sources/styles/index.scss',
        main: './Build/Sources/index.js',
    },
    externals: {},
    output: {
        libraryTarget: 'umd',
        path: __dirname + '/../Resources/Public/JavaScript/',
    },
    optimization: {
        minimizer: [
            new TerserPlugin({}),
        ]
    },
    resolve: {
        extensions: ['*', '.js', '.jsx'],
    },
    watchOptions: {
        poll: true,
        ignored: '**/node_modules',
    }
};
