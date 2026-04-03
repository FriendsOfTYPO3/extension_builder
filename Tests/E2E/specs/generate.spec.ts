import { test, expect } from '@playwright/test';
import * as fs from 'fs';
import * as path from 'path';
import { BackendPage } from '../pages/BackendPage';
import { ExtensionBuilderPage } from '../pages/ExtensionBuilderPage';

// Path to the Composer path-repository root where Extension Builder writes new extensions
const PACKAGES_DIR = path.resolve(__dirname, '../../../../packages');

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

  test('"New extension" opens the form', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    await frame.locator('#WiringEditor-newButton-button').click();
    // Fields live inside eb-wiring-editor's shadow DOM — verify via evaluate()
    const hasNameField = await frame.locator('eb-wiring-editor').evaluate(
      (el: Element) => !!el.shadowRoot?.querySelector('[name="name"]')
    );
    expect(hasNameField).toBe(true);
  });

  test('extension name and vendor can be filled', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    const extBuilder = new ExtensionBuilderPage(frame);
    await extBuilder.openNewExtension();
    await extBuilder.fillExtensionProperties('My Test Extension', 'my_test_ext', 'Vendor');
    const values = await frame.locator('eb-wiring-editor').evaluate((el: any) => ({
      name: el.shadowRoot?.querySelector('[name="name"]')?.getValue?.() ?? '',
      extensionKey: el.shadowRoot?.querySelector('[name="extensionKey"]')?.getValue?.() ?? '',
    }));
    expect(values.name).toBe('My Test Extension');
    expect(values.extensionKey).toBe('my_test_ext');
  });

  test('"Save and generate" triggers generation', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    const extBuilder = new ExtensionBuilderPage(frame, page);
    await extBuilder.openNewExtension();
    await extBuilder.fillExtensionProperties('Playwright Test Ext', 'playwright_test', 'TestVendor');
    await extBuilder.generateExtension();
    await expect(extBuilder.getSuccessMessage()).toBeVisible({ timeout: 15000 });
  });

  test('success message appears after generation', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    const extBuilder = new ExtensionBuilderPage(frame, page);
    await extBuilder.openNewExtension();
    await extBuilder.fillExtensionProperties('Playwright Success Ext', 'playwright_success', 'TestVendor');
    await extBuilder.generateExtension();
    const msg = extBuilder.getSuccessMessage();
    await expect(msg).toBeVisible({ timeout: 15000 });
    await expect(msg).toContainText(/(success|generated|created|saved)/i);
  });

  test.afterAll(() => {
    for (const extKey of ['playwright_test', 'playwright_success', 'my_test_ext']) {
      fs.rmSync(path.join(PACKAGES_DIR, extKey), { recursive: true, force: true });
    }
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
