const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const { WebpackManifestPlugin } = require('webpack-manifest-plugin');

module.exports = {
	entry: {
		app: './www/assets/app.js'
	},
	output: {
		path: path.join(__dirname, 'www/dist'),
		filename: 'app.[contenthash].js',
		publicPath: '',
		/* nahrazuje clean-webpack-plugin */
		clean: true
	},
	resolve: {
		/* datagrid se importuje přímo z .ts zdrojů */
		extensions: ['.ts', '.js']
	},
	module: {
		rules: [
			{
				test: /\.ts$/,
				resolve: {fullySpecified: false},
				use: [{
					loader: 'ts-loader',
					options: {
						transpileOnly: true,
						allowTsInNodeModules: true,
						configFile: path.join(__dirname, 'tsconfig.json')
					}
				}]
			},
			{
				test: /\.css$/,
				use: [MiniCssExtractPlugin.loader, 'css-loader']
			}
		]
	},
	optimization: {
		/* '...' ponechá vestavěný Terser, přidáváme jen minifikaci CSS */
		minimizer: ['...', new CssMinimizerPlugin()]
	},
	plugins: [
		new MiniCssExtractPlugin({filename: 'app.[contenthash].css'}),
		new WebpackManifestPlugin({})
	]
};
