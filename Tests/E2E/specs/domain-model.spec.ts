import { test, expect } from '@playwright/test';
import { BackendPage } from '../pages/BackendPage';
import { ExtensionBuilderPage } from '../pages/ExtensionBuilderPage';

test.describe('Domain Model Canvas', () => {
  test.beforeEach(async ({ page }) => {
    const backend = new BackendPage(page);
    await backend.navigateToModule('Extension Builder');
    const extBuilder = new ExtensionBuilderPage(backend.getContentFrame());
    await extBuilder.waitForLoaded();
    await extBuilder.goToDomainModeller();
    // Start with a clean slate
    await backend.getContentFrame().locator('#WiringEditor-newButton-button').click();
  });

  // EBUILDER-38: Add model object to canvas
  test('clicking "+ Model Object" adds an eb-container to the canvas', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    const editor = frame.locator('eb-wiring-editor');

    const containersBefore = await editor.evaluate(
      (el: Element) => el.shadowRoot?.querySelector('eb-layer')?.shadowRoot?.querySelectorAll('eb-container').length ?? 0
    );
    expect(containersBefore).toBe(0);

    await frame.getByRole('button', { name: '+ Model Object' }).click();

    const containersAfter = await editor.evaluate(
      async (el: Element) => {
        const layer = el.shadowRoot?.querySelector('eb-layer') as any;
        await layer?.updateComplete;
        return layer?.shadowRoot?.querySelectorAll('eb-container').length ?? 0;
      }
    );
    expect(containersAfter).toBe(1);
  });

  test('added model object container is visible in canvas', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    await frame.getByRole('button', { name: '+ Model Object' }).click();

    const isVisible = await frame.locator('eb-wiring-editor').evaluate(
      async (el: Element) => {
        const layer = el.shadowRoot?.querySelector('eb-layer') as any;
        await layer?.updateComplete;
        const container = layer?.shadowRoot?.querySelector('eb-container') as any;
        if (!container) return false;
        await container.updateComplete;
        const rect = container.getBoundingClientRect();
        return rect.width > 0 && rect.height > 0;
      }
    );
    expect(isVisible).toBe(true);
  });
});
