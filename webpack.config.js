const path = require('path');
const glob = require('glob')
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const PurgecssPlugin = require('purgecss-webpack-plugin')

const PATHS = {
	src: path.join(__dirname, '')
}

module.exports = (env, argv) => {
	const isProductionMode = (argv.mode === 'production');
	let plugins = [];

	plugins.push(
		new MiniCssExtractPlugin({
			filename: "css/[name].css",
		})
	);

	if (isProductionMode) {
		plugins.push(
			new PurgecssPlugin({
				paths: glob.sync(`${PATHS.src}/**/*.php`, { nodir: true })
			}),
		);
	}

	const config = {
		context: path.resolve(__dirname, "src"),

		devServer: {
			devMiddleware: {
				index: true,
				publicPath: "/assets",
			},
			watchFiles: ["src/**/*"],
		},
		entry: [
			"/js/app.js",
			"/scss/app.scss",
		],
		output: {
			filename: "js/app.js",
			path: path.resolve(__dirname, "assets"),
		},

		module: {
			rules: [
				{
					test: /\.(sa|sc|c)ss$/,
					use: [
						MiniCssExtractPlugin.loader,
						"css-loader",
						"sass-loader"
					],
				},
			]
		},
		plugins: plugins,

		optimization: {
			minimizer: [
				new TerserPlugin(),
			],
		},

	};

	return config;
};
