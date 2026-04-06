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

  // EBUILDER-225: Canvas can be panned by dragging the background
  test('canvas background drag pans all containers', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();

    // Add a model object so the canvas is not empty
    await frame.getByRole('button', { name: '+ Model Object' }).click();

    const editor = frame.locator('eb-wiring-editor');

    // Record the container position before panning
    const posBefore = await editor.evaluate(async (el: any) => {
      const layer = el.shadowRoot?.querySelector('eb-layer') as any;
      await layer?.updateComplete;
      const container = layer?.shadowRoot?.querySelector('eb-container') as any;
      if (!container) return null;
      const rect = container.getBoundingClientRect();
      return { x: rect.left, y: rect.top };
    });
    expect(posBefore).not.toBeNull();

    // Drag the canvas background (the layer element itself is a safe background target)
    const layerLocator = editor.locator('eb-layer');
    const layerBox = await layerLocator.boundingBox();
    expect(layerBox).not.toBeNull();

    // Start drag in the lower-right area of the canvas (unlikely to hit containers)
    const startX = layerBox!.x + layerBox!.width * 0.8;
    const startY = layerBox!.y + layerBox!.height * 0.8;
    const dragDeltaX = 100;
    const dragDeltaY = 80;

    await page.mouse.move(startX, startY);
    await page.mouse.down();
    await page.mouse.move(startX + dragDeltaX, startY + dragDeltaY, { steps: 5 });
    await page.mouse.up();

    // Verify panOffset was updated
    const panOffset = await editor.evaluate((el: any) => {
      const layer = el.shadowRoot?.querySelector('eb-layer') as any;
      return layer?._panOffset ?? null;
    });
    expect(panOffset).not.toBeNull();
    expect(Math.abs(panOffset.x) + Math.abs(panOffset.y)).toBeGreaterThan(0);
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

  // Helper to simulate a wire drag from SOURCES of srcContainer to a relation
  // terminal of tgtContainer. tgtContainer must have at least one relation added.
  async function connectViaWire(layer: any, srcContainer: any, tgtContainer: any) {
    const term0 = srcContainer.shadowRoot?.querySelector('eb-terminal[terminal-id="SOURCES"]');
    if (!term0) return false;

    // Add a relation to the target container so a droppable terminal exists
    const relListField = tgtContainer.shadowRoot?.querySelector('[name="relations"]') as any;
    if (!relListField) return false;
    relListField._addItem();
    await relListField.updateComplete;

    const relTerminal = relListField.shadowRoot?.querySelector('eb-terminal[droppable]');
    if (!relTerminal) return false;

    // Start wire drawing from source
    layer.dispatchEvent(new CustomEvent('terminal-connect', {
      detail: { terminalId: 'SOURCES', uid: '', sourceEl: term0 },
    }));
    await layer.updateComplete;

    // Drop on the relation terminal — composed: true crosses shadow DOM boundaries,
    // so the window pointerup listener in eb-layer sees the terminal in composedPath().
    relTerminal.dispatchEvent(new PointerEvent('pointerup', { bubbles: true, composed: true }));
    await layer.updateComplete;
    return true;
  }

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

        // connectViaWire is defined in outer scope — inline the logic here
        const term0 = containers[0].shadowRoot?.querySelector('eb-terminal[terminal-id="SOURCES"]');
        if (!term0) return -2;
        const relListField = containers[1].shadowRoot?.querySelector('[name="relations"]') as any;
        if (!relListField) return -3;
        relListField._addItem();
        await relListField.updateComplete;
        const relTerminal = relListField.shadowRoot?.querySelector('eb-terminal[droppable]');
        if (!relTerminal) return -4;

        layer.dispatchEvent(new CustomEvent('terminal-connect', {
          detail: { terminalId: 'SOURCES', uid: '', sourceEl: term0 },
        }));
        await layer.updateComplete;
        relTerminal.dispatchEvent(new PointerEvent('pointerup', { bubbles: true, composed: true }));
        await layer.updateComplete;

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

        const term0 = containers[0].shadowRoot?.querySelector('eb-terminal[terminal-id="SOURCES"]');
        if (!term0) return [];
        const relListField = containers[1].shadowRoot?.querySelector('[name="relations"]') as any;
        if (!relListField) return [];
        relListField._addItem();
        await relListField.updateComplete;
        const relTerminal = relListField.shadowRoot?.querySelector('eb-terminal[droppable]');
        if (!relTerminal) return [];

        layer.dispatchEvent(new CustomEvent('terminal-connect', {
          detail: { terminalId: 'SOURCES', uid: '', sourceEl: term0 },
        }));
        await layer.updateComplete;
        relTerminal.dispatchEvent(new PointerEvent('pointerup', { bubbles: true, composed: true }));
        await layer.updateComplete;

        return layer.serialize().wires;
      }
    );

    expect(serializedWires).toHaveLength(1);
    expect(serializedWires[0]).toMatchObject({
      src: { moduleId: expect.any(Number), terminal: 'REL_0' },
      tgt: { moduleId: expect.any(Number), terminal: 'SOURCES' },
    });
  });

  // EBUILDER-230: Property form shows only name and type by default
  test('new property has advanced options collapsed by default', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    await frame.getByRole('button', { name: '+ Model Object' }).click();

    const isCollapsed = await frame.locator('eb-wiring-editor').evaluate(
      async (el: any) => {
        const layer = el.shadowRoot?.querySelector('eb-layer');
        await layer?.updateComplete;
        const container = layer?.shadowRoot?.querySelector('eb-container') as any;
        if (!container) return null;
        await container.updateComplete;

        const listField = container.shadowRoot?.querySelector('[name="properties"]') as any;
        if (!listField) return null;
        listField._addItem();
        await listField.updateComplete;

        const advGroup = listField.shadowRoot?.querySelector('[name="advancedSettings"]') as any;
        if (!advGroup) return null;
        await advGroup.updateComplete;
        return advGroup.collapsed;
      }
    );
    expect(isCollapsed).toBe(true);
  });

  // EBUILDER-234: New model objects must have a UID generated upon creation
  test('new model object gets a uid assigned on creation', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    await frame.getByRole('button', { name: '+ Model Object' }).click();

    const uid = await frame.locator('eb-wiring-editor').evaluate(async (el: any) => {
      const layer = el.shadowRoot?.querySelector('eb-layer') as any;
      await layer?.updateComplete;
      return layer?.serialize()?.modules?.[0]?.value?.objectsettings?.uid;
    });
    expect(uid).toBeTruthy();
  });

  // EBUILDER-234: New relations must have a UID generated upon creation
  test('new relation gets a uid assigned on creation', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    await frame.getByRole('button', { name: '+ Model Object' }).click();

    const uid = await frame.locator('eb-wiring-editor').evaluate(async (el: any) => {
      const layer = el.shadowRoot?.querySelector('eb-layer') as any;
      await layer?.updateComplete;
      const container = layer?.shadowRoot?.querySelector('eb-container') as any;
      if (!container) return null;
      await container.updateComplete;

      const relListField = container.shadowRoot?.querySelector('[name="relations"]') as any;
      if (!relListField) return null;
      relListField._addItem();
      await relListField.updateComplete;

      return layer.serialize()?.modules?.[0]?.value?.relationGroup?.relations?.[0]?.uid;
    });
    expect(uid).toBeTruthy();
  });

  test('property advanced options toggle reveals hidden fields', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    await frame.getByRole('button', { name: '+ Model Object' }).click();

    const result = await frame.locator('eb-wiring-editor').evaluate(
      async (el: any) => {
        const layer = el.shadowRoot?.querySelector('eb-layer');
        await layer?.updateComplete;
        const container = layer?.shadowRoot?.querySelector('eb-container') as any;
        if (!container) return null;
        await container.updateComplete;

        const listField = container.shadowRoot?.querySelector('[name="properties"]') as any;
        if (!listField) return null;
        listField._addItem();
        await listField.updateComplete;

        const advGroup = listField.shadowRoot?.querySelector('[name="advancedSettings"]') as any;
        if (!advGroup) return null;
        await advGroup.updateComplete;

        // Expand the group
        advGroup.collapsed = false;
        await advGroup.updateComplete;

        const descField = listField.shadowRoot?.querySelector('[name="propertyDescription"]') as any;
        return {
          collapsed: advGroup.collapsed,
          descFieldExists: !!descField,
        };
      }
    );
    expect(result).not.toBeNull();
    expect(result.collapsed).toBe(false);
    expect(result.descFieldExists).toBe(true);
  });
});
