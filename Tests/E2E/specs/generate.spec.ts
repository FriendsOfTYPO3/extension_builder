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

  test('domain modeller renders eb-wiring-editor', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    await expect(frame.locator('eb-wiring-editor')).toBeVisible();
  });

  test('"New extension" button is present', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    await expect(frame.locator('#WiringEditor-newButton-button')).toBeVisible();
  });

  // TODO: The following tests require shadow DOM access to extension properties fields
  // inside eb-wiring-editor — tracked as a follow-up ticket for v13.
  test.skip('"New extension" opens the form', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    const extBuilder = new ExtensionBuilderPage(frame);
    await extBuilder.openNewExtension();
    await expect(frame.locator('[name="name"]')).toBeVisible();
  });

  test.skip('extension name and vendor can be filled', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    const extBuilder = new ExtensionBuilderPage(frame);
    await extBuilder.openNewExtension();
    await extBuilder.fillExtensionProperties('My Test Extension', 'my_test_ext', 'Vendor');
    await expect(frame.locator('[name="name"]')).toHaveValue('My Test Extension');
    await expect(frame.locator('[name="extensionKey"]')).toHaveValue('my_test_ext');
  });

  test.skip('"Save and generate" triggers generation', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    const extBuilder = new ExtensionBuilderPage(frame);
    await extBuilder.openNewExtension();
    await extBuilder.fillExtensionProperties('Playwright Test Ext', 'playwright_test', 'TestVendor');
    await extBuilder.generateExtension();
    await expect(extBuilder.getSuccessMessage()).toBeVisible({ timeout: 15000 });
  });

  test.skip('success message appears after generation', async ({ page }) => {
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

test.describe('Wiring Editor Toolbar', () => {
  test.beforeEach(async ({ page }) => {
    const backend = new BackendPage(page);
    await backend.navigateToModule('Extension Builder');
    const extBuilder = new ExtensionBuilderPage(backend.getContentFrame());
    await extBuilder.waitForLoaded();
    await extBuilder.goToDomainModeller();
  });

  test('save button is visible in docheader', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    await expect(frame.locator('#WiringEditor-saveButton-button')).toBeVisible();
  });

  test('load button is visible in docheader', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    await expect(frame.locator('#WiringEditor-loadButton-button')).toBeVisible();
  });

  test('advanced options toggle is visible in docheader', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    await expect(frame.locator('#toggleAdvancedOptions')).toBeVisible();
  });

  test('"+ Model Object" button is visible in editor', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    await expect(frame.getByRole('button', { name: '+ Model Object' })).toBeVisible();
  });
});
