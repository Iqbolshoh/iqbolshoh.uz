import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [react()],
  build: {
    rollupOptions: {
      output: {
        manualChunks(id) {
          if (!id.includes('node_modules')) return;
          if (id.includes('react-dom') || id.includes('/react/') || id.includes('/react@')) return 'react-vendor';
          if (id.includes('react-router-dom') || id.includes('react-router')) return 'router';
          if (id.includes('framer-motion') || id.includes('motion')) return 'motion';
          if (id.includes('i18next') || id.includes('react-i18next')) return 'i18n';
          if (id.includes('react-hot-toast') || id.includes('react-helmet-async')) return 'ui';
        },
      },
    },
  },
});
