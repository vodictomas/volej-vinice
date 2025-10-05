const webpack = require('webpack');
const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const { WebpackManifestPlugin } = require('webpack-manifest-plugin');

var date = new Date();

var dateString = [date.getFullYear(), date.getMonth() + 1, date.getDate(), date.getHours(), date.getMinutes(), date.getSeconds()].join('');

module.exports =
    {
        entry: {
            app: './www/assets/app.js'
        },
        output: {
            path: path.join(__dirname, '/www/dist'),
            filename: 'app.' + dateString + '.bundle.js',
            publicPath: ''
        },
        module: {
            rules: [
                {test: /\.css$/, use: [MiniCssExtractPlugin.loader, 'css-loader']},
                {test: require.resolve('jquery'), use: [{loader: 'expose-loader', options: {exposes: {globalName: 'jQuery', override: true}}}, {loader: 'expose-loader', options: {exposes: {globalName: '$', override: true}}}]},
                {test: /\.woff(2)?(\?v=[0-9]\.[0-9]\.[0-9])?$/, dependency: { not: ['url'] }, use: [{loader: "url-loader", options: {limit: 10000, mimetype: 'application/font-woff'}}], type: 'asset/resource'},
                {test: /\.(ttf|eot|svg)(\?v=[0-9]\.[0-9]\.[0-9])?$/, loader: "file-loader"},
                {test: /\.(jpe?g|png|gif)$/i, use: ['file-loader?name=image/[name].[ext]']}
            ]
        },
        optimization: {
            minimize: true,
            minimizer: [
                new TerserPlugin({}),
                new CssMinimizerPlugin({})
            ]
        },
        plugins: [
            new MiniCssExtractPlugin({
                filename: '[name].[chunkhash].css'
            }),
            new webpack.ProvidePlugin({
                'window.Nette': 'nette-forms',
                'Nette': 'nette-forms',
                'naja': ['naja', 'default'],
                'bootstrap': 'bootstrap'
            }),
            new CleanWebpackPlugin(),
            new WebpackManifestPlugin({})
        ]
    };

