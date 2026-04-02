import { test, expect } from '@playwright/test';

test.describe('Extension Builder Intro Page', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('/typo3/module/tools/extensionbuilder');
        await page.waitForLoadState('networkidle');
        // Navigate via the main menu to force action=index in the URL.
        // This bypasses the firstTime redirect that would otherwise send
        // returning users directly to domain modelling.
        const frame = page.frameLocator('#typo3-contentIframe');
        await frame.getByRole('link', { name: 'Introduction' }).click();
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
