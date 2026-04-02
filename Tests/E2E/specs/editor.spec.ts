import { test, expect } from '@playwright/test';
import { BackendPage } from '../pages/BackendPage';
import { ExtensionBuilderPage } from '../pages/ExtensionBuilderPage';

test.describe('Wiring Editor Interactions', () => {
    test.beforeEach(async ({ page }) => {
        const backend = new BackendPage(page);
        await backend.navigateToModule('Extension Builder');
        const extBuilder = new ExtensionBuilderPage(backend.getContentFrame());
        await extBuilder.waitForLoaded();
        await extBuilder.goToDomainModeller();
    });

    test('eb-layer canvas is present in editor shadow DOM', async ({ page }) => {
        const frame = new BackendPage(page).getContentFrame();
        const hasLayer = await frame.locator('eb-wiring-editor').evaluate(
            (el: Element) => !!el.shadowRoot?.querySelector('eb-layer')
        );
        expect(hasLayer).toBe(true);
    });

    test('left panel collapses and expands on toggle', async ({ page }) => {
        const frame = new BackendPage(page).getContentFrame();
        const editor = frame.locator('eb-wiring-editor');

        const initialCollapsed = await editor.evaluate(
            (el: Element) => el.shadowRoot?.querySelector('.left-panel')?.classList.contains('collapsed') ?? true
        );
        expect(initialCollapsed).toBe(false);

        await editor.evaluate(
            (el: Element) =>
                (el.shadowRoot?.querySelector('.left-panel-header button') as HTMLButtonElement)?.click()
        );

        const afterCollapsed = await editor.evaluate(
            (el: Element) => el.shadowRoot?.querySelector('.left-panel')?.classList.contains('collapsed') ?? false
        );
        expect(afterCollapsed).toBe(true);
    });

    test('"New" button resets extension name on the editor', async ({ page }) => {
        const frame = new BackendPage(page).getContentFrame();
        const editor = frame.locator('eb-wiring-editor');

        // Set a name to simulate an extension being loaded
        await editor.evaluate((el: any) => { el.extensionName = 'test_ext'; });
        await frame.locator('#WiringEditor-newButton-button').click();

        const name = await editor.evaluate((el: any) => el.extensionName);
        expect(name).toBe('');
    });
});
