import { defineConfig, mergeConfig } from 'vitest/config';
import * as path from 'path';

import viteConfig from './vite.config';

export default mergeConfig(
  viteConfig,
  defineConfig({
    resolve: {
      alias: [{ find: '@tests', replacement: path.resolve(__dirname, 'tests') }],
    },
    test: {
      globals: true,
      environment: 'jsdom',
      setupFiles: './tests/setup.ts',
      include: ['tests/**/*.test.{ts,tsx}'],
    },
  }),
);
