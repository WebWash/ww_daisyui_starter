import { defineConfig } from "vite";
import tailwindcss from "@tailwindcss/vite";

const isWatch = process.argv.includes("--watch") || process.argv.includes("-w");

export default defineConfig({
  plugins: [tailwindcss()],
  build: {
    outDir: "dist",
    watch: isWatch ? { exclude: ["dist/**", "node_modules/**"] } : null,
    rolldownOptions: {
      input: "src/css/style.css",
      output: {
        assetFileNames: "[name][extname]",
      },
    },
  },
});
