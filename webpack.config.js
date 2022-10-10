"use strict";

const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');
const TerserPlugin = require('terser-webpack-plugin');

module.exports = {
    mode: 'production',
    entry: {
        frontend: './Resources/Private/Scss/frontend/frontend.scss',
        backend: './Resources/Private/Scss/backend/backend.scss',
        pagelayout: './Resources/Private/Scss/backend/pagelayout.scss',
        Datatables: './Resources/Private/JavaScript/backend/Datatables.js',
        MassUpdate: './Resources/Private/JavaScript/backend/MassUpdate.js',
        SetupWizard: './Resources/Private/JavaScript/backend/SetupWizard.js',
    },
    externals: {
        "jquery": "jquery",
        "bootstrap": "bootstrap",
        "TYPO3/CMS/Backend/Modal": "TYPO3/CMS/Backend/Modal",
        "TYPO3/CMS/Backend/Severity": "TYPO3/CMS/Backend/Severity"
    },
    output: {
        libraryTarget: 'amd',
        path: __dirname + '/Resources/Public/JavaScript',
    },
    optimization: {
        minimizer: [
            new TerserPlugin({}),
        ]
    },
    module: {
        rules: [
            {
                test: /\.(css|scss)$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    {
                        loader: "css-loader",
                        options: {}
                    },
                    {
                        loader: "postcss-loader",
                        options: {}
                    },
                    {
                        loader: "sass-loader",
                        options: {}
                    }
                ]
            }
        ]
    },
    plugins: [
        new RemoveEmptyScriptsPlugin(),
        new MiniCssExtractPlugin({
            filename: '../Css/[name].min.css',
        })
    ]
};
