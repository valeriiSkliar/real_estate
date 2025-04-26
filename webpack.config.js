const path = require('path');
// const { merge } = require('webpack-merge');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');

const PATHS = {
  src: path.join(__dirname, 'src'),
  dist: path.join(__dirname, 'web/assets/dist'),
};

module.exports = {
    entry: {
        main: [`${PATHS.src}/js/main.js`],
        // mobileAds: `${PATHS.src}/js/mobile-advertisements.js`,
        style: [`${PATHS.src}/scss/style.scss`]
    },
    output: {
        path: PATHS.dist,
        filename: 'js/[name].[contenthash].js',
        publicPath: '/assets/dist/',
        clean: true
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: 'css/[name].[contenthash].css'
        }),
        new CopyWebpackPlugin({
            patterns: [
                { 
                    from: `${PATHS.src}/css`, 
                    to: 'css' 
                }
            ]
        })
    ],
    optimization: {
        splitChunks: {
            cacheGroups: {
                vendor: {
                    test: /[\\/]node_modules[\\/]/,
                    name: 'vendors',
                    chunks: 'all'
                }
            }
        }
    },
    resolve: {
        extensions: ['.js', '.json', '.scss', '.css'],
        alias: {
            '@': PATHS.src
        }
    },
    devtool: 'source-map'
};
