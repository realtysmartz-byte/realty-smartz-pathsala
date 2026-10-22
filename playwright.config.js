const { defineConfig } = require('@playwright/test');

module.exports = defineConfig({
  testDir: './tests',
  testMatch: '**/*.spec.js',
  timeout: 60000,
  workers: 1,
  use: {
    baseURL: 'http://127.0.0.1:8000',
    channel: 'chrome',
    screenshot: 'only-on-failure',
    viewport: { width: 1440, height: 900 },
    ignoreHTTPSErrors: true,
  },
  webServer: {
    command: 'C:\\xampp\\php\\php.exe -S 127.0.0.1:8000 -t public public/index.php',
    url: 'http://127.0.0.1:8000',
    reuseExistingServer: true,
    timeout: 15000,
  },
});
