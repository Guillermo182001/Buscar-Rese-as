const { defineConfig } = require('@vue/cli-service')
module.exports = defineConfig({
  transpileDependencies: true,
  devServer: {
    port: 8080,
    allowedHosts: "all",
    host: "0.0.0.0", // Acepta conexiones externas (necesario para ngrok)
    client: {
      webSocketURL: 'wss://3f5b-38-25-17-101.ngrok-free.app/ws',
    }
  }
})
