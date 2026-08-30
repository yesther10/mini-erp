import { defineConfig } from '@playwright/test';

process.env.PLAYWRIGHT_BROWSERS_PATH ??= '0';

export default defineConfig({
    testDir: './tests/Playwright',
    fullyParallel: false,
    globalSetup: './tests/Playwright/support/global-setup.mjs',
    globalTeardown: './tests/Playwright/support/global-teardown.mjs',
    reporter: 'list',
    use: {
        baseURL: 'http://127.0.0.1:8000',
        trace: 'retain-on-failure',
    },
    workers: 1,
});
