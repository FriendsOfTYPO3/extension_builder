import { test, expect } from '@playwright/test';
import * as fs from 'fs';
import * as path from 'path';
import { BackendPage } from '../pages/BackendPage';
import { ExtensionBuilderPage } from '../pages/ExtensionBuilderPage';

const TEST_EXT_KEY = 'playwright_load_test';
const AUTH_FILE = path.resolve(__dirname, '../.auth/user.json');
// Extensions are written to the Composer path-repository root
const PACKAGES_DIR = path.resolve(__dirname, '../../../../packages');

test.describe('Load Extension via Open Dialog', () => {
  test.beforeAll(async ({ browser }) => {
    // Create a test extension so the load dialog has something to list
    const context = await browser.newContext({ storageState: AUTH_FILE });
    const page = await context.newPage();
    const backend = new BackendPage(page);
    await backend.navigateToModule('Extension Builder');
    const extBuilder = new ExtensionBuilderPage(backend.getContentFrame(), page);
    await extBuilder.waitForLoaded();
    await extBuilder.goToDomainModeller();
    await extBuilder.openNewExtension();
    await extBuilder.fillExtensionProperties('Playwright Load Test', TEST_EXT_KEY, 'TestVendor');
    await extBuilder.generateExtension();
    await expect(extBuilder.getSuccessMessage()).toBeVisible({ timeout: 15000 });
    await context.close();
  });

  test.afterAll(() => {
    fs.rmSync(path.join(PACKAGES_DIR, TEST_EXT_KEY), { recursive: true, force: true });
  });

  test.beforeEach(async ({ page }) => {
    const backend = new BackendPage(page);
    await backend.navigateToModule('Extension Builder');
    const extBuilder = new ExtensionBuilderPage(backend.getContentFrame());
    await extBuilder.waitForLoaded();
    await extBuilder.goToDomainModeller();
  });

  test('load dialog appears when clicking Open', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    await frame.locator('#WiringEditor-loadButton-button').click();
    const dialog = frame.locator('dialog');
    await expect(dialog).toBeVisible();
    await expect(dialog.locator('h3')).toHaveText('Open Extension');
  });

  test('load dialog lists the test extension', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    await frame.locator('#WiringEditor-loadButton-button').click();
    const dialog = frame.locator('dialog');
    await expect(dialog).toBeVisible();
    await expect(dialog.locator('select option', { hasText: TEST_EXT_KEY })).toBeVisible();
  });

  test('selecting an extension loads it into the editor', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    await frame.locator('#WiringEditor-loadButton-button').click();
    const dialog = frame.locator('dialog');
    await expect(dialog).toBeVisible();
    await dialog.locator('select').selectOption(TEST_EXT_KEY);
    await dialog.locator('button[type="submit"]').click();

    // Wait for the editor to load the extension
    await frame.locator('eb-wiring-editor').waitFor({ state: 'visible' });
    const extensionName = await frame.locator('eb-wiring-editor').evaluate(
      async (el: any) => {
        // load() is async — wait for the loading state to clear
        if (el._loading) {
          await new Promise<void>(resolve => {
            const check = setInterval(() => {
              if (!el._loading) { clearInterval(check); resolve(); }
            }, 100);
          });
        }
        return el.extensionName;
      }
    );
    expect(extensionName).toBe(TEST_EXT_KEY);
  });
});
