const path = require('path')
const HTMLWebpackPlugin = require('html-webpack-plugin')
const { CleanWebpackPlugin } = require('clean-webpack-plugin')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')

const isDev = process.env.NODE_ENV === 'development'
const page = 'admin'
const filename = (ext) =>
  isDev
    ? `../../dist/${page}/[name].[hash].${ext}`
    : `../../dist/${page}/[name].[hash].${ext}`
const babelOptions = (preset) => {
  const opts = {
    presets: ['@babel/preset-env'],
    plugins: ['@babel/plugin-proposal-class-properties'],
  }

  if (preset) {
    opts.presets.push(preset)
  }

  return opts
}
const cssLoaders = (extra) => {
  const loaders = [
    {
      loader: MiniCssExtractPlugin.loader,
      options: {
        hmr: isDev,
        reloadAll: true,
      },
    },
    'css-loader',
  ]

  if (extra) {
    loaders.push(extra)
  }
  return loaders
}
const jsLoaders = () => {
  const loaders = [
    {
      loader: 'babel-loader',
      options: babelOptions(),
    },
  ]

  return loaders
}

module.exports = {
  context: path.resolve(__dirname, 'src/js'),
  mode: 'development',
  entry: {
    [page]: ['@babel/polyfill', `./${page}.js`],
  },
  output: {
    filename: filename('js'),
    path: path.resolve(__dirname, `dist/${page}`),
  },
  devtool: isDev ? 'source-map' : '',
  plugins: [
    new HTMLWebpackPlugin({
      template: `../../view/${page}-page/${page}.html`,
      minify: {
        collapseWhitespace: !isDev,
      },
    }),
    new CleanWebpackPlugin(),
    new MiniCssExtractPlugin({
      filename: filename('css'),
    }),
  ],
  module: {
    rules: [
      {
        test: /\.(ttf|woff|woff2|eot)$/,
        use: ['file-loader'],
      },
      {
        test: /\.css$/,
        use: cssLoaders(),
      },
      {
        test: /\.s[ac]ss$/,
        use: cssLoaders('sass-loader'),
      },
      {
        test: /\.js/,
        exclude: /node_modules/,
        use: jsLoaders(),
      },
    ],
  },
}
