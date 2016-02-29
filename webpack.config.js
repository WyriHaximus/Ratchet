const path = require('path');
const webpack = require('webpack');

//process.env.NODE_ENV = 'production';

module.exports = {
    entry: {
        client: './client/client.js',
    },
    output: {
        path: 'webroot/js/',
        filename: '[name].js'
    },
    module: {
        loaders: [
            {
                test: /\.js$/,
                loader: 'babel-loader',
                query: {
                    presets: ['es2015', 'react'],
                },
            },
            {
                test: /package.json/,
                loader: 'json-loader',
            }
        ]
    },
    resolve: {
        extensions: ['', '.js']
    },
    plugins: [
        new webpack.optimize.UglifyJsPlugin({
            compress: {
                warnings: false
            }
        }),
    ],
};
