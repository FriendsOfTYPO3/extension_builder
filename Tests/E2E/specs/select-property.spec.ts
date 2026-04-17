import { test, expect } from '@playwright/test';
import { BackendPage } from '../pages/BackendPage';
import { ExtensionBuilderPage } from '../pages/ExtensionBuilderPage';

test.describe('SelectProperty select items', () => {
  test.beforeEach(async ({ page }) => {
    const backend = new BackendPage(page);
    await backend.navigateToModule('Extension Builder');
    const extBuilder = new ExtensionBuilderPage(backend.getContentFrame());
    await extBuilder.waitForLoaded();
    await extBuilder.goToDomainModeller();
    await backend.getContentFrame().locator('#WiringEditor-newButton-button').click();
  });

  // Verifies that the selectItems list field is rendered inside the advanced settings
  // group of a property item.
  test('selectItems list field is present in property advanced settings', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    await frame.getByRole('button', { name: '+ Model Object' }).click();

    const result = await frame.locator('eb-wiring-editor').evaluate(
      async (el: any) => {
        const layer = el.shadowRoot?.querySelector('eb-layer');
        await layer?.updateComplete;
        const container = layer?.shadowRoot?.querySelector('eb-container') as any;
        if (!container) return null;
        await container.updateComplete;

        // Reach the properties list field (eb-list-field[name="properties"]) via the
        // container's shadow root — eb-group[name="propertyGroup"] renders it as a
        // light-DOM slotted child, but container.shadowRoot.querySelector traverses
        // into slotted light DOM here.
        const listField = container.shadowRoot?.querySelector('[name="properties"]') as any;
        if (!listField) return null;

        listField._addItem();
        await listField.updateComplete;

        // advancedSettings eb-group is rendered as a light-DOM child of the property
        // eb-group, which is itself a light-DOM child inside an .item-content div in
        // the list field's shadow root.
        const advGroup = listField.shadowRoot?.querySelector('[name="advancedSettings"]') as any;
        if (!advGroup) return null;
        await advGroup.updateComplete;

        // The selectItems list field is a light-DOM child of the advancedSettings
        // eb-group (slotted into its card-body). querySelector walks light DOM.
        const selectItemsList =
          advGroup.querySelector('[name="selectItems"]') ??
          advGroup.shadowRoot?.querySelector('[name="selectItems"]');

        return selectItemsList !== null;
      }
    );

    expect(result).toBe(true);
  });

  // Verifies that items can be added to the selectItems list and that getValue()
  // returns the expected label/value pairs.
  test('selectItems can have items added and getValue returns them', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    await frame.getByRole('button', { name: '+ Model Object' }).click();

    const value = await frame.locator('eb-wiring-editor').evaluate(
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

        // Expand the advanced settings group so its content is reachable.
        advGroup.collapsed = false;
        advGroup.requestUpdate();
        await advGroup.updateComplete;

        const selectItemsList =
          (advGroup.querySelector('[name="selectItems"]') ??
          advGroup.shadowRoot?.querySelector('[name="selectItems"]')) as any;
        if (!selectItemsList) return null;

        // Add one item to the selectItems list.
        selectItemsList._addItem();
        await selectItemsList.updateComplete;

        // The item's content lives inside .item-content > eb-group[name="selectItem"].
        // The eb-group renders label/value string fields as light-DOM slotted children.
        const itemContent = selectItemsList.shadowRoot?.querySelector('.item-content');
        if (!itemContent) return null;

        const labelField = itemContent.querySelector('[name="label"]') as any;
        const valueField = itemContent.querySelector('[name="value"]') as any;
        if (!labelField || !valueField) return null;

        labelField.setValue('Active');
        valueField.setValue('active');

        return selectItemsList.getValue();
      }
    );

    expect(value).not.toBeNull();
    expect(value).toEqual(expect.arrayContaining([
      expect.objectContaining({ label: 'Active', value: 'active' }),
    ]));
  });

  // Verifies that when selectItems are configured for a Select-type property, the
  // serialized layer data (wiring JSON) contains them under the property entry.
  test('wiring JSON contains selectItems when items are configured', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    await frame.getByRole('button', { name: '+ Model Object' }).click();

    const moduleValue = await frame.locator('eb-wiring-editor').evaluate(
      async (el: any) => {
        const layer = el.shadowRoot?.querySelector('eb-layer') as any;
        await layer?.updateComplete;
        const container = layer?.shadowRoot?.querySelector('eb-container') as any;
        if (!container) return null;
        await container.updateComplete;

        // Add a property
        const listField = container.shadowRoot?.querySelector('[name="properties"]') as any;
        if (!listField) return null;

        listField._addItem();
        await listField.updateComplete;

        // Set propertyName to "status"
        const propertyNameField = listField.shadowRoot
          ?.querySelector('.item-content')
          ?.querySelector('[name="propertyName"]') as any;
        if (propertyNameField) {
          propertyNameField.setValue('status');
        }

        // Set propertyType to "Select"
        const propertyTypeField = listField.shadowRoot
          ?.querySelector('.item-content')
          ?.querySelector('[name="propertyType"]') as any;
        if (propertyTypeField) {
          propertyTypeField.setValue('Select');
        }

        // Expand advanced settings
        const advGroup = listField.shadowRoot?.querySelector('[name="advancedSettings"]') as any;
        if (!advGroup) return null;
        await advGroup.updateComplete;

        advGroup.collapsed = false;
        advGroup.requestUpdate();
        await advGroup.updateComplete;

        // Locate and populate the selectItems list
        const selectItemsList =
          (advGroup.querySelector('[name="selectItems"]') ??
          advGroup.shadowRoot?.querySelector('[name="selectItems"]')) as any;
        if (!selectItemsList) return null;

        selectItemsList._addItem();
        await selectItemsList.updateComplete;

        const itemContent = selectItemsList.shadowRoot?.querySelector('.item-content');
        if (!itemContent) return null;

        const labelField = itemContent.querySelector('[name="label"]') as any;
        const valueField = itemContent.querySelector('[name="value"]') as any;
        if (!labelField || !valueField) return null;

        labelField.setValue('Active');
        valueField.setValue('active');

        // Collect the full wiring data and return the first module's value
        const { modules } = layer.serialize();
        return modules?.[0]?.value ?? null;
      }
    );

    expect(moduleValue).not.toBeNull();

    // The properties list is serialized under propertyGroup.properties
    const properties: any[] = moduleValue?.propertyGroup?.properties ?? [];
    expect(properties).toHaveLength(1);
    expect(properties[0]).toMatchObject({
      propertyName: 'status',
      propertyType: 'Select',
      selectItems: [{ label: 'Active', value: 'active' }],
    });
  });
});
