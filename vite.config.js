import { defineConfig } from 'vite'
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
  plugins: [
    tailwindcss(),
  ],
  build: {
    outDir: 'dist',
    watch: {
      exclude: ['dist/**', 'node_modules/**']
    },
    rolldownOptions: {
      input: 'src/css/style.css',
      output: {
        assetFileNames: '[name][extname]'
      }
    }
  }
})
