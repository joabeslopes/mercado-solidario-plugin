import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue()],
  base: '/wp-content/plugins/mercado-solidario/frontend/dist',
  build: {
    rollupOptions: {
      input: {
        checkin: path.resolve(__dirname, 'src/pages/Checkin/index.html'),
        checkout: path.resolve(__dirname, 'src/pages/Checkout/index.html'),
        families: path.resolve(__dirname, 'src/pages/Families/index.html')
      }
    }
  }
})
