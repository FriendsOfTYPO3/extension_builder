const common = require('./webpack.common.config.js');
const {merge} = require('webpack-merge');

module.exports = merge(common, {
    mode: 'development',
    module: {
        rules: [
            {
                test: /\.(js|jsx)$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env', ["@babel/preset-react", {"runtime": "automatic"}]],
                    },
                },
            },
            {
                test: /\.(css|scss)$/,
                use: [
                    'css-loader',
                    'postcss-loader',
                ]
            },
        ],
    },
})
