import { test, expect } from '@playwright/test';
import { BackendPage } from '../pages/BackendPage';
import { ExtensionBuilderPage } from '../pages/ExtensionBuilderPage';

test.describe('Advanced Mode Toggle', () => {
  test.beforeEach(async ({ page }) => {
    const backend = new BackendPage(page);
    await backend.navigateToModule('Extension Builder');
    const extBuilder = new ExtensionBuilderPage(backend.getContentFrame());
    await extBuilder.waitForLoaded();
    await extBuilder.goToDomainModeller();
    await backend.getContentFrame().locator('#WiringEditor-newButton-button').click();
  });

  test('model object settings fields with advancedMode are hidden by default', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    await frame.getByRole('button', { name: '+ Model Object' }).click();

    const result = await frame.locator('eb-wiring-editor').evaluate(async (el: any) => {
      const layer = el.shadowRoot?.querySelector('eb-layer');
      await layer?.updateComplete;
      const container = layer?.shadowRoot?.querySelector('eb-container') as any;
      if (!container) return null;
      await container.updateComplete;

      const settingsGroup = container.shadowRoot?.querySelector('[name="objectsettings"]') as any;
      if (!settingsGroup) return null;
      await settingsGroup.updateComplete;

      // Fields marked advancedMode: true in modelObject config
      const advancedFields = ['type', 'sorting', 'addDeletedField', 'addHiddenField',
        'addStarttimeEndtimeFields', 'categorizable', 'mapToTable', 'parentClass'];
      const visibility: Record<string, string> = {};
      for (const name of advancedFields) {
        const field = settingsGroup.querySelector(`[name="${name}"]`);
        visibility[name] = field ? getComputedStyle(field).display : 'NOT_FOUND';
      }
      return visibility;
    });

    expect(result).not.toBeNull();
    for (const [name, display] of Object.entries(result!)) {
      expect(display, `field "${name}" should be hidden`).toBe('none');
    }
  });

  test('toggling advanced mode reveals hidden model object fields', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    await frame.getByRole('button', { name: '+ Model Object' }).click();

    const result = await frame.locator('eb-wiring-editor').evaluate(async (el: any) => {
      const layer = el.shadowRoot?.querySelector('eb-layer');
      await layer?.updateComplete;
      const container = layer?.shadowRoot?.querySelector('eb-container') as any;
      if (!container) return null;
      await container.updateComplete;

      const settingsGroup = container.shadowRoot?.querySelector('[name="objectsettings"]') as any;
      if (!settingsGroup) return null;
      await settingsGroup.updateComplete;

      // Toggle advanced mode ON
      el._toggleAdvancedMode();
      await el.updateComplete;
      await new Promise(r => requestAnimationFrame(r));

      const advancedFields = ['type', 'sorting', 'addDeletedField', 'addHiddenField',
        'addStarttimeEndtimeFields', 'categorizable', 'mapToTable', 'parentClass'];
      const visibility: Record<string, string> = {};
      for (const name of advancedFields) {
        const field = settingsGroup.querySelector(`[name="${name}"]`);
        visibility[name] = field ? getComputedStyle(field).display : 'NOT_FOUND';
      }
      return visibility;
    });

    expect(result).not.toBeNull();
    for (const [name, display] of Object.entries(result!)) {
      expect(display, `field "${name}" should be visible`).toBe('block');
    }
  });

  test('toggling advanced mode off hides fields again', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    await frame.getByRole('button', { name: '+ Model Object' }).click();

    const result = await frame.locator('eb-wiring-editor').evaluate(async (el: any) => {
      const layer = el.shadowRoot?.querySelector('eb-layer');
      await layer?.updateComplete;
      const container = layer?.shadowRoot?.querySelector('eb-container') as any;
      if (!container) return null;
      await container.updateComplete;

      const settingsGroup = container.shadowRoot?.querySelector('[name="objectsettings"]') as any;
      if (!settingsGroup) return null;

      // Toggle ON then OFF
      el._toggleAdvancedMode();
      await el.updateComplete;
      await new Promise(r => requestAnimationFrame(r));

      el._toggleAdvancedMode();
      await el.updateComplete;
      await new Promise(r => requestAnimationFrame(r));

      const typeField = settingsGroup.querySelector('[name="type"]');
      return typeField ? getComputedStyle(typeField).display : 'NOT_FOUND';
    });

    expect(result).toBe('none');
  });

  test('advanced mode works for relation fields inside list items', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    await frame.getByRole('button', { name: '+ Model Object' }).click();

    const result = await frame.locator('eb-wiring-editor').evaluate(async (el: any) => {
      const layer = el.shadowRoot?.querySelector('eb-layer');
      await layer?.updateComplete;
      const container = layer?.shadowRoot?.querySelector('eb-container') as any;
      if (!container) return null;
      await container.updateComplete;

      // Add a relation item
      const relList = container.shadowRoot?.querySelector('[name="relationGroup"] [name="relations"]') as any;
      if (!relList) return { error: 'no relation list field' };
      relList._addItem();
      await relList.updateComplete;

      // The lazyLoading field has advancedMode: true and is now a direct child
      const lazyField = relList.shadowRoot?.querySelector('[name="lazyLoading"]');
      if (!lazyField) return { error: 'no lazyLoading field' };

      const beforeToggle = getComputedStyle(lazyField).display;

      // Toggle advanced mode ON
      el._toggleAdvancedMode();
      await el.updateComplete;
      await new Promise(r => requestAnimationFrame(r));

      const afterToggle = getComputedStyle(lazyField).display;
      return { beforeToggle, afterToggle };
    });

    expect(result).not.toHaveProperty('error');
    expect(result.beforeToggle).toBe('none');
    expect(result.afterToggle).toBe('block');
  });

  test('docheader toggle button triggers advanced mode', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    await frame.getByRole('button', { name: '+ Model Object' }).click();

    // Click the docheader button
    await frame.locator('#toggleAdvancedOptions').click();

    const result = await frame.locator('eb-wiring-editor').evaluate(async (el: any) => {
      const layer = el.shadowRoot?.querySelector('eb-layer');
      await layer?.updateComplete;
      const container = layer?.shadowRoot?.querySelector('eb-container') as any;
      if (!container) return null;
      await container.updateComplete;

      const settingsGroup = container.shadowRoot?.querySelector('[name="objectsettings"]') as any;
      if (!settingsGroup) return null;

      const typeField = settingsGroup.querySelector('[name="type"]');
      return typeField ? getComputedStyle(typeField).display : 'NOT_FOUND';
    });

    expect(result).toBe('block');
  });
});
