import { test, expect } from '@playwright/test';
import { BackendPage } from '../pages/BackendPage';
import { ExtensionBuilderPage } from '../pages/ExtensionBuilderPage';

test.describe('Extension Generation', () => {
  test.beforeEach(async ({ page }) => {
    const backend = new BackendPage(page);
    await backend.navigateToModule('Extension Builder');
    const extBuilder = new ExtensionBuilderPage(backend.getContentFrame());
    await extBuilder.waitForLoaded();
    await extBuilder.goToDomainModeller();
  });

  test('"New extension" opens the form', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    const extBuilder = new ExtensionBuilderPage(frame);
    await extBuilder.openNewExtension();
    await expect(frame.locator('[name="name"]')).toBeVisible();
  });

  test('extension name and vendor can be filled', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    const extBuilder = new ExtensionBuilderPage(frame);
    await extBuilder.openNewExtension();
    await extBuilder.fillExtensionProperties('My Test Extension', 'my_test_ext', 'Vendor');
    await expect(frame.locator('[name="name"]')).toHaveValue('My Test Extension');
    await expect(frame.locator('[name="extensionKey"]')).toHaveValue('my_test_ext');
  });

  test('"Save and generate" triggers generation', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    const extBuilder = new ExtensionBuilderPage(frame);
    await extBuilder.openNewExtension();
    await extBuilder.fillExtensionProperties('Playwright Test Ext', 'playwright_test', 'TestVendor');
    await extBuilder.generateExtension();
    await expect(extBuilder.getSuccessMessage()).toBeVisible({ timeout: 15000 });
  });

  test('success message appears after generation', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    const extBuilder = new ExtensionBuilderPage(frame);
    await extBuilder.openNewExtension();
    await extBuilder.fillExtensionProperties('Playwright Success Ext', 'playwright_success', 'TestVendor');
    await extBuilder.generateExtension();
    const msg = extBuilder.getSuccessMessage();
    await expect(msg).toBeVisible({ timeout: 15000 });
    await expect(msg).toContainText(/(success|generated|created|saved)/i);
  });
});
