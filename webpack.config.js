"use strict";

const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');
const TerserPlugin = require('terser-webpack-plugin');

module.exports = {
    mode: 'production',
    entry: {
        backend: './Build/Sources/styles/index.scss',
        main: './Build/Sources/index.js',
    },
    externals: {},
    output: {
        libraryTarget: 'amd',
        path: __dirname + '/Resources/Public/JavaScript/Webpack/',
    },
    optimization: {
        minimizer: [
            new TerserPlugin({}),
        ]
    },
    module: {
        rules: [
            {
                test: /\.(js|jsx)$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env', '@babel/preset-react'],
                    },
                },
            },
            {
                test: /\.(css|scss)$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    'css-loader',
                    'postcss-loader',
                    'sass-loader'
                ]
            },
            {
                test: /\.(css|scss)$/,
                use: [
                    // ...
                ],
            },
        ],
    },
    plugins: [
        new RemoveEmptyScriptsPlugin(),
        new MiniCssExtractPlugin({
            filename: '../Css/[name].min.css',
        })
    ],
    resolve: {
        extensions: ['*', '.js', '.jsx'],
    },
};
