import { test, expect } from '@playwright/test';
import { BackendPage } from '../pages/BackendPage';
import { ExtensionBuilderPage } from '../pages/ExtensionBuilderPage';

test.describe('Extension Builder Module', () => {
  test('Extension Builder module appears in navigation', async ({ page }) => {
    await page.goto('/typo3/main');
    await page.waitForLoadState('networkidle');
    await expect(page.locator('a[href*="extensionbuilder"]').first()).toBeVisible({ timeout: 10000 });
  });

  test('module loads via direct URL', async ({ page }) => {
    const backend = new BackendPage(page);
    await backend.navigateToModule('Extension Builder');
    await expect(page.locator('#typo3-contentIframe')).toBeVisible();
  });

  test('Extension Builder heading is shown in frame', async ({ page }) => {
    const backend = new BackendPage(page);
    await backend.navigateToModule('Extension Builder');
    const extBuilder = new ExtensionBuilderPage(backend.getContentFrame());
    await extBuilder.waitForLoaded();
    await expect(extBuilder.getModuleTitle()).toContainText(/extension builder/i);
  });

  test('domain modeller loads after intro', async ({ page }) => {
    const backend = new BackendPage(page);
    await backend.navigateToModule('Extension Builder');
    const extBuilder = new ExtensionBuilderPage(backend.getContentFrame());
    await extBuilder.waitForLoaded();
    await extBuilder.goToDomainModeller();
    await expect(backend.getContentFrame().locator('body')).toBeVisible();
  });
});
