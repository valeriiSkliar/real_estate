const { merge } = require('webpack-merge');
const common = require('../webpack.config.js');
const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

const PATHS = {
  src: path.join(__dirname, '../src'),
  dist: path.join(__dirname, '../web/assets/dist'),
};

module.exports = merge(common, {
  mode: 'development',
  devtool: 'inline-source-map',
  devServer: {
    port: 8090,
    hot: true,
    headers: {
      'Access-Control-Allow-Origin': '*',
    },
    devMiddleware: {
      publicPath: '/assets/dist/',
      writeToDisk: true,
    },
    static: {
      directory: path.join(__dirname, '../web'),
      publicPath: '/',
    },
  },
  output: {
    path: PATHS.dist,
    publicPath: 'http://localhost:8090/assets/dist/',
    filename: 'js/[name].js',
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
              sourceMap: true,
              url: true,
            }
          },
          {
            loader: 'postcss-loader',
            options: {
              sourceMap: true
            }
          },
          {
            loader: 'sass-loader',
            options: {
              sourceMap: true,
              implementation: require('sass'),
              sassOptions: {
                quietDeps: true,
                api: 'modern',
                outputStyle: 'expanded',
                includePaths: [path.resolve(__dirname, '../src/scss')],
                silenceDeprecations: ['legacy-js-api', 'import', 'color-functions', 'global-builtin', 'mixed-decls', 'slash-div'],
                // logger: {
                //   warn: function(message) {
                //     return message.includes('mixed-decls') ? null : console.warn(message);
                //   }
                // }
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
      filename: 'css/[name].css',
    }),
  ],
  watchOptions: {
    ignored: /node_modules/,
    poll: 1000,
  },
});