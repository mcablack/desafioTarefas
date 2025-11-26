const { defineConfig } = require('@vue/cli-service')

module.exports = defineConfig({
  transpileDependencies: true,
  
  devServer: {
    host: '0.0.0.0',
    port: 8080,
    allowedHosts: 'all',
    hot: true,
    liveReload: true,
    client: {
      webSocketURL: {
        hostname: 'localhost',
        pathname: '/ws',
        port: 8080
      }
    }
  },
  
  configureWebpack: {
    watchOptions: {
      poll: 1000,
      aggregateTimeout: 300,
      ignored: /node_modules/
    }
  }
})