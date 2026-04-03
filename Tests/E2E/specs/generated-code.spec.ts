import { test, expect } from '@playwright/test';
import * as fs from 'fs';
import * as path from 'path';
import { BackendPage } from '../pages/BackendPage';
import { ExtensionBuilderPage } from '../pages/ExtensionBuilderPage';

// Path to the Composer path-repository root where Extension Builder writes new extensions
const PACKAGES_DIR = path.resolve(__dirname, '../../../../');
const EXT_KEY = 'eb_test_generated';
const EXT_BASE = path.join(PACKAGES_DIR, EXT_KEY);

test.describe('Generated Code Quality', () => {
  /**
   * EBUILDER-93: Generate a reference extension once before all assertions.
   * Creates eb_test_generated with an Article model (6 property types) and a
   * frontend plugin with CRUD actions so every subsequent test has real files
   * to inspect.
   */
  test.beforeAll(async ({ browser }) => {
    const context = await browser.newContext({
      storageState: path.resolve(__dirname, '../.auth/user.json'),
      baseURL: 'https://extensionbuilder.ddev.site',
      ignoreHTTPSErrors: true,
    });
    const page = await context.newPage();

    const backend = new BackendPage(page);
    await backend.navigateToModule('Extension Builder');
    const extBuilder = new ExtensionBuilderPage(backend.getContentFrame(), page);
    await extBuilder.waitForLoaded();
    await extBuilder.goToDomainModeller();

    await extBuilder.openNewExtension();
    await extBuilder.fillExtensionProperties('EB Test Generated', EXT_KEY, 'TestVendor');

    const frame = backend.getContentFrame();
    await frame.locator('eb-wiring-editor').evaluate(async (el: any, extKey: string) => {
      // Override the extension name so saveWiring uses the right key
      el.extensionName = extKey;

      // Add Article domain model with 6 properties and CRUD actions
      const layer = el.shadowRoot?.querySelector('eb-layer');
      if (layer) {
        layer.addContainers([{
          config: { position: [20, 20] },
          value: {
            name: 'Article',
            objectsettings: {
              description: '',
              type: 'Entity',
              aggregateRoot: true,
              addDeletedField: true,
              addHiddenField: true,
              addStarttimeEndtimeFields: true,
              categorizable: false,
            },
            actionGroup: {
              _default1_list: true,
              _default2_show: true,
              _default3_new_create: true,
              _default4_edit_update: true,
              _default5_delete: true,
            },
            propertyGroup: {
              properties: [
                { propertyName: 'title', propertyType: 'String' },
                { propertyName: 'bodytext', propertyType: 'Text' },
                { propertyName: 'publishDate', propertyType: 'Date' },
                { propertyName: 'image', propertyType: 'Image' },
                { propertyName: 'email', propertyType: 'Email' },
                { propertyName: 'active', propertyType: 'Boolean' },
              ],
            },
            relationGroup: { relations: [] },
          },
        }]);

        await layer.updateComplete;

        // Wait for each container's reactive update so _populateFromValue() completes
        const containers = Array.from(
          layer.shadowRoot?.querySelectorAll('eb-container') ?? []
        ) as any[];
        for (const container of containers) {
          await container.updateComplete;
        }
      }

      // Register a frontend plugin so ext_localconf.php gets a configurePlugin() call
      const pluginsField = el.shadowRoot?.querySelector('[name="plugins"]');
      if (pluginsField) {
        pluginsField.setValue([{
          name: 'Article',
          description: '',
          key: 'article',
          actions: {
            controllerActionCombinations: 'Article => list,show,new,create,edit,update,delete',
          },
        }]);
      }
    }, EXT_KEY);

    await extBuilder.generateExtension();
    await expect(extBuilder.getSuccessMessage()).toBeVisible({ timeout: 15000 });

    // Allow ddev volume mount to sync the generated files to the host filesystem
    await page.waitForTimeout(3000);

    await context.close();
  });

  test('reference extension directory exists after generation', () => {
    expect(fs.existsSync(EXT_BASE)).toBe(true);
  });

  test.afterAll(() => {
    fs.rmSync(EXT_BASE, { recursive: true, force: true });
  });
});
