import { defineConfig, devices } from '@playwright/test';
import { existsSync, readFileSync } from 'fs';
import { resolve } from 'path';

// Load .env file if present (Playwright does not load it automatically)
const envFile = resolve(__dirname, '.env');
if (existsSync(envFile)) {
  for (const line of readFileSync(envFile, 'utf-8').split('\n')) {
    const match = line.match(/^([^#=]+)=(.*)$/);
    if (match) process.env[match[1].trim()] ??= match[2].trim();
  }
}

export default defineConfig({
  testDir: './Tests/E2E',
  fullyParallel: false,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  use: {
    baseURL: 'https://extensionbuilder.ddev.site',
    ignoreHTTPSErrors: true,
    screenshot: 'only-on-failure',
    trace: 'on-first-retry',
  },
  projects: [
    {
      name: 'setup',
      testMatch: /.*\.setup\.ts/,
    },
    {
      name: 'chromium',
      use: {
        ...devices['Desktop Chrome'],
        storageState: 'Tests/E2E/.auth/user.json',
      },
      dependencies: ['setup'],
    },
  ],
});
