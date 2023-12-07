// webpack.config.js
const path = require('path');

module.exports = {
  entry: './src/index.js', // Your main entry point
  output: {
    filename: 'bundle.js', // Output file name
    path: path.resolve(__dirname, 'public/dist'), // Output directory
  },
  devtool: 'source-map',
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
        },
      },
      {
        test: /\.css$/,
        use: ['style-loader', 'css-loader'],
      },
      {
        test: /\.(woff|woff2|eot|ttf|otf)$/,
        use: ['file-loader'],
      },
    ]
  },
};
