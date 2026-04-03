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

  // EBUILDER-39: Add domain property to a model object
  test('adding a property to a model object increases the property list', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    await frame.getByRole('button', { name: '+ Model Object' }).click();

    const itemCount = await frame.locator('eb-wiring-editor').evaluate(
      async (el: any) => {
        const layer = el.shadowRoot?.querySelector('eb-layer');
        await layer?.updateComplete;
        const container = layer?.shadowRoot?.querySelector('eb-container');
        if (!container) return -1;
        await container.updateComplete;

        // Reach the properties list field inside the container's shadow DOM.
        // eb-list-field[name="properties"] is a light-DOM child of eb-group,
        // which is itself in eb-container's shadow root — querySelectorAll traverses this.
        const listField = container.shadowRoot?.querySelector('[name="properties"]') as any;
        if (!listField) return -2;

        listField._addItem();
        await listField.updateComplete;
        return listField.getValue().length;
      }
    );
    expect(itemCount).toBe(1);
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

  // EBUILDER-40: Connect two model objects via wire
  test('two model objects can be connected with a wire', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    const addBtn = frame.getByRole('button', { name: '+ Model Object' });
    await addBtn.click();
    await addBtn.click();

    const wireCount = await frame.locator('eb-wiring-editor').evaluate(
      async (el: any) => {
        const layer = el.shadowRoot?.querySelector('eb-layer');
        await layer?.updateComplete;
        const containers = Array.from(layer?.shadowRoot?.querySelectorAll('eb-container') ?? []) as any[];
        if (containers.length < 2) return -1;

        await containers[0].updateComplete;
        await containers[1].updateComplete;

        const term0 = containers[0].shadowRoot?.querySelector('eb-terminal');
        const term1 = containers[1].shadowRoot?.querySelector('eb-terminal');
        if (!term0 || !term1) return -2;

        // Dispatch terminal-connect events directly on eb-layer (which listens on itself).
        // This replicates clicking both terminals without requiring drag through nested shadow DOMs.
        layer.dispatchEvent(new CustomEvent('terminal-connect', {
          detail: { terminalId: 'SOURCES', uid: '', sourceEl: term0 },
        }));
        layer.dispatchEvent(new CustomEvent('terminal-connect', {
          detail: { terminalId: 'SOURCES', uid: '', sourceEl: term1 },
        }));

        return layer._wires.length;
      }
    );
    expect(wireCount).toBe(1);
  });

  test('connected wire appears in serialized layer data', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    const addBtn = frame.getByRole('button', { name: '+ Model Object' });
    await addBtn.click();
    await addBtn.click();

    const serializedWires = await frame.locator('eb-wiring-editor').evaluate(
      async (el: any) => {
        const layer = el.shadowRoot?.querySelector('eb-layer');
        await layer?.updateComplete;
        const containers = Array.from(layer?.shadowRoot?.querySelectorAll('eb-container') ?? []) as any[];
        if (containers.length < 2) return [];

        await containers[0].updateComplete;
        await containers[1].updateComplete;

        const term0 = containers[0].shadowRoot?.querySelector('eb-terminal');
        const term1 = containers[1].shadowRoot?.querySelector('eb-terminal');
        if (!term0 || !term1) return [];

        layer.dispatchEvent(new CustomEvent('terminal-connect', {
          detail: { terminalId: 'SOURCES', uid: '', sourceEl: term0 },
        }));
        layer.dispatchEvent(new CustomEvent('terminal-connect', {
          detail: { terminalId: 'SOURCES', uid: '', sourceEl: term1 },
        }));

        return layer.serialize().wires;
      }
    );

    expect(serializedWires).toHaveLength(1);
    expect(serializedWires[0]).toMatchObject({
      src: { moduleId: expect.any(Number) },
      tgt: { moduleId: expect.any(Number) },
    });
  });
});
