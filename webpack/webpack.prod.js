const { merge } = require('webpack-merge');
const common = require('../webpack.config.js');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const { WebpackManifestPlugin } = require('webpack-manifest-plugin');
const path = require('path');

const PATHS = {
  dist: path.join(__dirname, '../web/assets/dist'),
};

module.exports = merge(common, {
  mode: 'production',
  devtool: 'source-map',
  output: {
    path: PATHS.dist,
    filename: 'js/[name].[contenthash].js',
    publicPath: '/assets/dist/',
  },
  module: {
    rules: [
      {
        test: /\.(sa|sc|c)ss$/,
        use: [
          MiniCssExtractPlugin.loader,
          {
            loader: 'css-loader',
            options: {
              sourceMap: false,
              url: true
            }
          },
          {
            loader: 'postcss-loader',
            options: {
              sourceMap: false,
              postcssOptions: {
                plugins: ['autoprefixer']
              }
            }
          },
          {
            loader: 'sass-loader',
            options: {
              sourceMap: false,
              implementation: require('sass'),
              sassOptions: {
                quietDeps: true,
                silenceDeprecations: ['legacy-js-api', 'import', 'color-functions', 'global-builtin', 'mixed-decls', 'slash-div'],
              }
            }
          }
        ],
      },
      {
        test: /\.(woff|woff2|eot|ttf|svg)$/,
        type: 'asset/resource',
        generator: {
          filename: 'webfonts/[name][ext]'
        }
      }
    ],
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: 'css/[name].[contenthash].css',
    }),
    new WebpackManifestPlugin({
      fileName: 'manifest.json',
      publicPath: '/assets/dist/',
      filter: (file) => file.isInitial,
      generate: (seed, files) => {
        const manifestFiles = {};
        files.forEach(file => {
          const originalName = file.name.replace(/\.[0-9a-f]+\./, '.');
          manifestFiles[originalName] = file.path;
          console.log(manifestFiles[originalName]);
        });
        console.log(manifestFiles);
        return manifestFiles;
      }
    }),
  ],
  optimization: {
    minimize: true,
  }
});