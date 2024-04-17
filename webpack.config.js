const webpack = require('webpack');
const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const OptimizeCSSAssetsPlugin = require("optimize-css-assets-webpack-plugin");
const TerserPlugin = require('terser-webpack-plugin');
const CleanWebpackPlugin = require('clean-webpack-plugin');

var date = new Date();

var dateString = [date.getFullYear(), date.getMonth() + 1, date.getDate(), date.getHours(), date.getMinutes(), date.getSeconds()].join('');

module.exports = {
    entry: {
        app: './www/assets/app.js'
    },
    output: {
        path: path.join(__dirname, 'www/dist'),
        filename: 'app.' + dateString + '.bundle.js'
    },
    module: {
        rules: [
            {
                test: /\.css$/, use: [
                    MiniCssExtractPlugin.loader,
                    'css-loader',
                ]
            },
            {
                test: require.resolve('jquery'),
                use: [{
                    loader: 'expose-loader',
                    options: 'jQuery'
                }, {
                    loader: 'expose-loader',
                    options: '$'
                }]
            },
            { test: /\.woff(2)?(\?v=[0-9]\.[0-9]\.[0-9])?$/, loader: "url-loader?limit=10000&mimetype=application/font-woff" },
            { test: /\.(ttf|eot|svg)(\?v=[0-9]\.[0-9]\.[0-9])?$/, loader: "file-loader"},
            { test: /\.(jpe?g|png|gif)$/i, use: ['file-loader?name=images/[name].[ext]']}
        ]
    },
    optimization: {
        minimize: true,
        minimizer: [
            new TerserPlugin({
                cache: true,
                parallel: true,
            }),
            new OptimizeCSSAssetsPlugin({})
        ]
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: 'app.' + dateString + '.bundle.css',
        }),
        new webpack.ProvidePlugin({
            'window.Nette': 'nette-forms',
            'Nette': 'nette-forms'
        }),
        new CleanWebpackPlugin()
    ]
};
