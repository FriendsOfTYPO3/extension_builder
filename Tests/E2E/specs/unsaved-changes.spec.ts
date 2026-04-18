import { test, expect } from '@playwright/test';
import * as fs from 'fs';
import * as path from 'path';
import { BackendPage } from '../pages/BackendPage';
import { ExtensionBuilderPage } from '../pages/ExtensionBuilderPage';

const EXT_A = 'playwright_unsaved_a';
const EXT_B = 'playwright_unsaved_b';
const AUTH_FILE = path.resolve(__dirname, '../.auth/user.json');
const PACKAGES_DIR = path.resolve(__dirname, '../../../../packages');

async function createExtension(browser: any, extKey: string, extName: string) {
    const context = await browser.newContext({ storageState: AUTH_FILE });
    const page = await context.newPage();
    const backend = new BackendPage(page);
    await backend.navigateToModule('Extension Builder');
    const extBuilder = new ExtensionBuilderPage(backend.getContentFrame(), page);
    await extBuilder.waitForLoaded();
    await extBuilder.goToDomainModeller();
    await extBuilder.openNewExtension();
    await extBuilder.fillExtensionProperties(extName, extKey, 'TestVendor');
    await extBuilder.generateExtension();
    await expect(extBuilder.getSuccessMessage()).toBeVisible({ timeout: 15000 });
    await context.close();
}

test.describe('Unsaved changes warning', () => {
    test.beforeAll(async ({ browser }) => {
        await createExtension(browser, EXT_A, 'Playwright Unsaved A');
        await createExtension(browser, EXT_B, 'Playwright Unsaved B');
    });

    test.afterAll(() => {
        fs.rmSync(path.join(PACKAGES_DIR, EXT_A), { recursive: true, force: true });
        fs.rmSync(path.join(PACKAGES_DIR, EXT_B), { recursive: true, force: true });
    });

    test.beforeEach(async ({ page }) => {
        const backend = new BackendPage(page);
        await backend.navigateToModule('Extension Builder');
        const extBuilder = new ExtensionBuilderPage(backend.getContentFrame());
        await extBuilder.waitForLoaded();
        await extBuilder.goToDomainModeller();
    });

    test('New button shows discard warning when changes are pending', async ({ page }) => {
        const frame = new BackendPage(page).getContentFrame();

        // Add a model object to mark the editor dirty
        const editor = frame.locator('eb-wiring-editor');
        await editor.evaluate((el: any) => el.addModelObject());

        // Click New — should show unsaved changes modal
        await frame.locator('#WiringEditor-newButton-button').click();
        const modal = page.locator('.t3js-modal');
        await expect(modal).toBeVisible();
        await expect(modal.locator('.t3js-modal-title')).toHaveText('Unsaved changes');
    });

    test('Cancel in discard warning keeps the current state', async ({ page }) => {
        const frame = new BackendPage(page).getContentFrame();

        // Add a model object to mark dirty
        const editor = frame.locator('eb-wiring-editor');
        await editor.evaluate((el: any) => el.addModelObject());

        // Click New → Cancel
        await frame.locator('#WiringEditor-newButton-button').click();
        const modal = page.locator('.t3js-modal');
        await expect(modal).toBeVisible();
        await modal.locator('.t3js-modal-footer .btn-default').click();
        await expect(modal).not.toBeVisible();

        // The model object should still be there — editor was not reset
        const containerCount = await editor.evaluate(
            (el: any) => el.shadowRoot?.querySelector('eb-layer')?.shadowRoot?.querySelectorAll('eb-container').length ?? 0
        );
        expect(containerCount).toBeGreaterThan(0);
    });

    test('Discard in warning resets the editor', async ({ page }) => {
        const frame = new BackendPage(page).getContentFrame();

        // Add a model object to mark dirty
        const editor = frame.locator('eb-wiring-editor');
        await editor.evaluate((el: any) => el.addModelObject());

        // Click New → Discard
        await frame.locator('#WiringEditor-newButton-button').click();
        const modal = page.locator('.t3js-modal');
        await expect(modal).toBeVisible();
        await modal.locator('.t3js-modal-footer .btn-warning').click();
        await expect(modal).not.toBeVisible();

        // Editor should be cleared
        const containerCount = await editor.evaluate(
            (el: any) => el.shadowRoot?.querySelector('eb-layer')?.shadowRoot?.querySelectorAll('eb-container').length ?? 0
        );
        expect(containerCount).toBe(0);
    });

    test('Open button shows discard warning when changes are pending', async ({ page }) => {
        const frame = new BackendPage(page).getContentFrame();

        // Load EXT_A
        await frame.locator('#WiringEditor-loadButton-button').click();
        let modal = page.locator('.t3js-modal');
        await expect(modal).toBeVisible();
        await modal.locator('select').selectOption(EXT_A);
        await modal.locator('.t3js-modal-footer .btn-primary').click();
        await expect(modal).not.toBeVisible();

        // Wait for load to complete
        const editor = frame.locator('eb-wiring-editor');
        await editor.evaluate(async (el: any) => {
            if (el._loading) {
                await new Promise<void>(resolve => {
                    const check = setInterval(() => {
                        if (!el._loading) { clearInterval(check); resolve(); }
                    }, 100);
                });
            }
        });

        // Modify a field to mark dirty
        await editor.evaluate((el: any) => {
            const field = el.shadowRoot?.querySelector('[name="description"]');
            field?.setValue?.('modified by test');
        });

        // Click Open — should show unsaved changes warning
        await frame.locator('#WiringEditor-loadButton-button').click();
        modal = page.locator('.t3js-modal');
        await expect(modal).toBeVisible();
        await expect(modal.locator('.t3js-modal-title')).toHaveText('Unsaved changes');
    });

    test('Discard in Open warning proceeds to load the selected extension', async ({ page }) => {
        const frame = new BackendPage(page).getContentFrame();

        // Load EXT_A first
        await frame.locator('#WiringEditor-loadButton-button').click();
        let modal = page.locator('.t3js-modal');
        await expect(modal).toBeVisible();
        await modal.locator('select').selectOption(EXT_A);
        await modal.locator('.t3js-modal-footer .btn-primary').click();
        await expect(modal).not.toBeVisible();

        const editor = frame.locator('eb-wiring-editor');
        await editor.evaluate(async (el: any) => {
            if (el._loading) {
                await new Promise<void>(resolve => {
                    const check = setInterval(() => {
                        if (!el._loading) { clearInterval(check); resolve(); }
                    }, 100);
                });
            }
        });

        // Modify a field to mark dirty
        await editor.evaluate((el: any) => {
            const field = el.shadowRoot?.querySelector('[name="description"]');
            field?.setValue?.('modified by test');
        });

        // Click Open → Discard → select EXT_B → Open
        await frame.locator('#WiringEditor-loadButton-button').click();
        modal = page.locator('.t3js-modal');
        await expect(modal).toBeVisible();
        await expect(modal.locator('.t3js-modal-title')).toHaveText('Unsaved changes');
        await modal.locator('.t3js-modal-footer .btn-warning').click();
        await expect(modal).not.toBeVisible();

        // Now the Open Extension dialog should appear
        modal = page.locator('.t3js-modal');
        await expect(modal).toBeVisible();
        await expect(modal.locator('.t3js-modal-title')).toHaveText('Open Extension');
        await modal.locator('select').selectOption(EXT_B);
        await modal.locator('.t3js-modal-footer .btn-primary').click();
        await expect(modal).not.toBeVisible();

        // EXT_B should now be loaded
        const extensionName = await editor.evaluate(async (el: any) => {
            if (el._loading) {
                await new Promise<void>(resolve => {
                    const check = setInterval(() => {
                        if (!el._loading) { clearInterval(check); resolve(); }
                    }, 100);
                });
            }
            return el.extensionName;
        });
        expect(extensionName).toBe(EXT_B);
    });

    test('No warning when no changes have been made', async ({ page }) => {
        const frame = new BackendPage(page).getContentFrame();

        // Click New immediately without making any changes — should reset directly
        await frame.locator('#WiringEditor-newButton-button').click();
        const modal = page.locator('.t3js-modal');
        await expect(modal).not.toBeVisible({ timeout: 1000 });
    });
});
