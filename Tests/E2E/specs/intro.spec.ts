import { test, expect } from '@playwright/test';

test.describe('Extension Builder Intro Page', () => {
    test.beforeEach(async ({ page }) => {
        // Navigate directly to the intro page action
        await page.goto('/typo3/module/tools/extensionbuilder/BuilderModule/index');
        await page.waitForLoadState('networkidle');
    });

    test('"Requirements" section heading is shown', async ({ page }) => {
        const frame = page.frameLocator('#typo3-contentIframe');
        await expect(frame.getByRole('heading', { name: 'Requirements' })).toBeVisible();
    });

    test('"Quick Start" section heading is shown', async ({ page }) => {
        const frame = page.frameLocator('#typo3-contentIframe');
        await expect(frame.getByRole('heading', { name: 'Quick Start' })).toBeVisible();
    });

    test('"Go to the Domain Modeller" link is present', async ({ page }) => {
        const frame = page.frameLocator('#typo3-contentIframe');
        await expect(frame.getByRole('link', { name: /go to (the )?domain modell/i })).toBeVisible();
    });
});
