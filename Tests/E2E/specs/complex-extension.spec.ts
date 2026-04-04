import { test, expect } from '@playwright/test';
import * as fs from 'fs';
import * as path from 'path';
import { BackendPage } from '../pages/BackendPage';
import { ExtensionBuilderPage } from '../pages/ExtensionBuilderPage';

const EXT_KEY = 'eb_complex_test';
// Tests/E2E/specs/ -> up 4 levels -> packages/
const PACKAGES_DIR = path.resolve(__dirname, '../../../../');
const EXT_BASE = path.join(PACKAGES_DIR, EXT_KEY);

const MODELS = [
  // 0: Article
  { name: 'Article', properties: [{ propertyName: 'title', propertyType: 'String' }, { propertyName: 'bodytext', propertyType: 'Text' }, { propertyName: 'publishDate', propertyType: 'DateTime' }, { propertyName: 'image', propertyType: 'Image' }, { propertyName: 'slug', propertyType: 'String' }, { propertyName: 'featured', propertyType: 'Boolean' }] },
  // 1: Author
  { name: 'Author', properties: [{ propertyName: 'firstName', propertyType: 'String' }, { propertyName: 'lastName', propertyType: 'String' }, { propertyName: 'email', propertyType: 'Email' }, { propertyName: 'bio', propertyType: 'Text' }, { propertyName: 'avatar', propertyType: 'Image' }, { propertyName: 'active', propertyType: 'Boolean' }] },
  // 2: Category
  { name: 'Category', properties: [{ propertyName: 'title', propertyType: 'String' }, { propertyName: 'description', propertyType: 'Text' }, { propertyName: 'color', propertyType: 'ColorPicker' }, { propertyName: 'icon', propertyType: 'Image' }, { propertyName: 'slug', propertyType: 'String' }, { propertyName: 'sortOrder', propertyType: 'Integer' }] },
  // 3: Tag
  { name: 'Tag', properties: [{ propertyName: 'label', propertyType: 'String' }, { propertyName: 'slug', propertyType: 'String' }, { propertyName: 'description', propertyType: 'Text' }, { propertyName: 'color', propertyType: 'ColorPicker' }, { propertyName: 'featured', propertyType: 'Boolean' }, { propertyName: 'count', propertyType: 'Integer' }] },
  // 4: Comment
  { name: 'Comment', properties: [{ propertyName: 'body', propertyType: 'Text' }, { propertyName: 'authorName', propertyType: 'String' }, { propertyName: 'authorEmail', propertyType: 'Email' }, { propertyName: 'approved', propertyType: 'Boolean' }, { propertyName: 'createdAt', propertyType: 'DateTime' }, { propertyName: 'score', propertyType: 'Integer' }] },
  // 5: Media
  { name: 'Media', properties: [{ propertyName: 'title', propertyType: 'String' }, { propertyName: 'file', propertyType: 'File' }, { propertyName: 'caption', propertyType: 'Text' }, { propertyName: 'altText', propertyType: 'String' }, { propertyName: 'copyright', propertyType: 'String' }, { propertyName: 'size', propertyType: 'Integer' }] },
  // 6: Video
  { name: 'Video', properties: [{ propertyName: 'title', propertyType: 'String' }, { propertyName: 'url', propertyType: 'String' }, { propertyName: 'duration', propertyType: 'Integer' }, { propertyName: 'thumbnail', propertyType: 'Image' }, { propertyName: 'description', propertyType: 'Text' }, { propertyName: 'views', propertyType: 'Integer' }] },
  // 7: Gallery
  { name: 'Gallery', properties: [{ propertyName: 'title', propertyType: 'String' }, { propertyName: 'description', propertyType: 'Text' }, { propertyName: 'coverImage', propertyType: 'Image' }, { propertyName: 'featured', propertyType: 'Boolean' }, { propertyName: 'sortOrder', propertyType: 'Integer' }, { propertyName: 'public', propertyType: 'Boolean' }] },
  // 8: Event
  { name: 'Event', properties: [{ propertyName: 'title', propertyType: 'String' }, { propertyName: 'startDate', propertyType: 'DateTime' }, { propertyName: 'endDate', propertyType: 'DateTime' }, { propertyName: 'location', propertyType: 'String' }, { propertyName: 'description', propertyType: 'Text' }, { propertyName: 'maxAttendees', propertyType: 'Integer' }] },
  // 9: Location
  { name: 'Location', properties: [{ propertyName: 'name', propertyType: 'String' }, { propertyName: 'street', propertyType: 'String' }, { propertyName: 'city', propertyType: 'String' }, { propertyName: 'country', propertyType: 'String' }, { propertyName: 'zip', propertyType: 'String' }, { propertyName: 'coordinates', propertyType: 'String' }] },
  // 10: Product
  { name: 'Product', properties: [{ propertyName: 'name', propertyType: 'String' }, { propertyName: 'sku', propertyType: 'String' }, { propertyName: 'price', propertyType: 'Float' }, { propertyName: 'stock', propertyType: 'Integer' }, { propertyName: 'description', propertyType: 'Text' }, { propertyName: 'active', propertyType: 'Boolean' }] },
  // 11: ProductCategory
  { name: 'ProductCategory', properties: [{ propertyName: 'title', propertyType: 'String' }, { propertyName: 'description', propertyType: 'Text' }, { propertyName: 'image', propertyType: 'Image' }, { propertyName: 'active', propertyType: 'Boolean' }, { propertyName: 'sortOrder', propertyType: 'Integer' }, { propertyName: 'slug', propertyType: 'String' }] },
  // 12: Review
  { name: 'Review', properties: [{ propertyName: 'rating', propertyType: 'Integer' }, { propertyName: 'comment', propertyType: 'Text' }, { propertyName: 'reviewer', propertyType: 'String' }, { propertyName: 'approved', propertyType: 'Boolean' }, { propertyName: 'createdAt', propertyType: 'DateTime' }, { propertyName: 'helpful', propertyType: 'Integer' }] },
  // 13: Newsletter
  { name: 'Newsletter', properties: [{ propertyName: 'subject', propertyType: 'String' }, { propertyName: 'body', propertyType: 'Text' }, { propertyName: 'sentDate', propertyType: 'DateTime' }, { propertyName: 'recipients', propertyType: 'Integer' }, { propertyName: 'active', propertyType: 'Boolean' }, { propertyName: 'opens', propertyType: 'Integer' }] },
  // 14: Subscriber
  { name: 'Subscriber', properties: [{ propertyName: 'email', propertyType: 'Email' }, { propertyName: 'firstName', propertyType: 'String' }, { propertyName: 'lastName', propertyType: 'String' }, { propertyName: 'confirmed', propertyType: 'Boolean' }, { propertyName: 'subscribedAt', propertyType: 'DateTime' }, { propertyName: 'source', propertyType: 'String' }] },
  // 15: Page
  { name: 'Page', properties: [{ propertyName: 'title', propertyType: 'String' }, { propertyName: 'slug', propertyType: 'String' }, { propertyName: 'content', propertyType: 'Text' }, { propertyName: 'publishDate', propertyType: 'DateTime' }, { propertyName: 'active', propertyType: 'Boolean' }, { propertyName: 'metaTitle', propertyType: 'String' }] },
  // 16: Navigation
  { name: 'Navigation', properties: [{ propertyName: 'title', propertyType: 'String' }, { propertyName: 'url', propertyType: 'String' }, { propertyName: 'sortOrder', propertyType: 'Integer' }, { propertyName: 'active', propertyType: 'Boolean' }, { propertyName: 'icon', propertyType: 'String' }, { propertyName: 'target', propertyType: 'String' }] },
  // 17: AppUser
  { name: 'AppUser', properties: [{ propertyName: 'username', propertyType: 'String' }, { propertyName: 'email', propertyType: 'Email' }, { propertyName: 'firstName', propertyType: 'String' }, { propertyName: 'lastName', propertyType: 'String' }, { propertyName: 'active', propertyType: 'Boolean' }, { propertyName: 'role', propertyType: 'String' }] },
  // 18: Setting
  { name: 'Setting', properties: [{ propertyName: 'settingKey', propertyType: 'String' }, { propertyName: 'value', propertyType: 'Text' }, { propertyName: 'description', propertyType: 'Text' }, { propertyName: 'type', propertyType: 'String' }, { propertyName: 'active', propertyType: 'Boolean' }, { propertyName: 'settingGroup', propertyType: 'String' }] },
  // 19: AuditLog
  { name: 'AuditLog', properties: [{ propertyName: 'action', propertyType: 'String' }, { propertyName: 'username', propertyType: 'String' }, { propertyName: 'timestamp', propertyType: 'DateTime' }, { propertyName: 'details', propertyType: 'Text' }, { propertyName: 'success', propertyType: 'Boolean' }, { propertyName: 'ipAddress', propertyType: 'String' }] },
  // 20: Redirect
  { name: 'Redirect', properties: [{ propertyName: 'sourceUrl', propertyType: 'String' }, { propertyName: 'targetUrl', propertyType: 'String' }, { propertyName: 'statusCode', propertyType: 'Integer' }, { propertyName: 'active', propertyType: 'Boolean' }, { propertyName: 'hits', propertyType: 'Integer' }, { propertyName: 'lastHit', propertyType: 'DateTime' }] },
  // 21: FileCollection
  { name: 'FileCollection', properties: [{ propertyName: 'title', propertyType: 'String' }, { propertyName: 'description', propertyType: 'Text' }, { propertyName: 'public', propertyType: 'Boolean' }, { propertyName: 'sortOrder', propertyType: 'Integer' }, { propertyName: 'createdAt', propertyType: 'DateTime' }, { propertyName: 'downloads', propertyType: 'Integer' }] },
];

// Wire pairs: [srcIdx, tgtIdx] — indices into MODELS array
const WIRE_PAIRS: [number, number][] = [
  [0, 1],   // Article -> Author
  [0, 2],   // Article -> Category
  [0, 3],   // Article -> Tag
  [0, 4],   // Article -> Comment
  [0, 5],   // Article -> Media
  [7, 5],   // Gallery -> Media
  [7, 6],   // Gallery -> Video
  [8, 9],   // Event -> Location
  [10, 11], // Product -> ProductCategory
  [10, 12], // Product -> Review
  [13, 14], // Newsletter -> Subscriber
  [15, 16], // Page -> Navigation
];

test.describe('Complex Extension Stress Test', () => {
  test.setTimeout(300_000);

  test.beforeAll(async ({ browser }) => {
    test.setTimeout(300_000);

    // Clean up from any previous failed run
    fs.rmSync(EXT_BASE, { recursive: true, force: true });

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
    await extBuilder.fillExtensionProperties('EB Complex Test', EXT_KEY, 'AcmeCorp');

    const frame = backend.getContentFrame();

    // Build container config array for addContainers — 5-column grid layout
    const COLS = 5;
    const COL_SPACING = 350;
    const ROW_SPACING = 250;

    await frame.locator('eb-wiring-editor').evaluate(
      async (el: any, { extKey, models, cols, colSpacing, rowSpacing }: {
        extKey: string;
        models: Array<{ name: string; properties: Array<{ propertyName: string; propertyType: string }> }>;
        cols: number;
        colSpacing: number;
        rowSpacing: number;
      }) => {
        el.extensionName = extKey;

        // Set authors (persons field)
        const personsField = el.shadowRoot?.querySelector('[name="persons"]');
        if (personsField) {
          personsField.setValue([
            { name: 'John Doe', role: 'Developer', email: 'john@acme.com', company: 'Acme Corp' },
            { name: 'Jane Smith', role: 'Developer', email: 'jane@acme.com', company: 'Acme Corp' },
          ]);
        }

        // Build and add all 22 containers
        const layer = el.shadowRoot?.querySelector('eb-layer');
        if (!layer) throw new Error('eb-layer not found');

        const containerConfigs = models.map((model, i) => ({
          config: {
            position: [(i % cols) * colSpacing + 20, Math.floor(i / cols) * rowSpacing + 20],
          },
          value: {
            name: model.name,
            objectsettings: {
              type: 'Entity',
              aggregateRoot: true,
              addDeletedField: true,
              addHiddenField: true,
              addStarttimeEndtimeFields: true,
            },
            actionGroup: {
              _default1_list: true,
              _default2_show: true,
            },
            propertyGroup: { properties: model.properties },
            relationGroup: { relations: [] },
          },
        }));

        layer.addContainers(containerConfigs);

        await layer.updateComplete;

        // Wait for each container to finish its reactive update
        const containers = Array.from(
          layer.shadowRoot?.querySelectorAll('eb-container') ?? []
        ) as any[];
        for (const container of containers) {
          await container.updateComplete;
        }

        // Set plugins (3)
        const pluginsField = el.shadowRoot?.querySelector('[name="plugins"]');
        if (pluginsField) {
          pluginsField.setValue([
            { name: 'Article List', key: 'articleList', description: '', actions: { controllerActionCombinations: 'Article => list,show' } },
            { name: 'Event Calendar', key: 'eventCalendar', description: '', actions: { controllerActionCombinations: 'Event => list,show' } },
            { name: 'Product Catalog', key: 'productCatalog', description: '', actions: { controllerActionCombinations: 'Product => list,show' } },
          ]);
        }

        // Set backend modules (2)
        const modulesField = el.shadowRoot?.querySelector('[name="backendModules"]');
        if (modulesField) {
          modulesField.setValue([
            { name: 'Article Manager', key: 'articleManager', description: '', mainModule: 'web', tabLabel: 'Article Manager', actions: { controllerActionCombinations: 'Article => list,show' } },
            { name: 'Event Manager', key: 'eventManager', description: '', mainModule: 'web', tabLabel: 'Event Manager', actions: { controllerActionCombinations: 'Event => list,show' } },
          ]);
        }
      },
      { extKey: EXT_KEY, models: MODELS, cols: COLS, colSpacing: COL_SPACING, rowSpacing: ROW_SPACING }
    );

    await extBuilder.generateExtension();

    // Poll for either the success notification or a "Save anyway" confirmation dialog.
    // Both are transient — success auto-dismisses in ~5s, so we must not wait for dialog
    // while success already appeared (race condition).
    const saveAnywayBtn = page.locator('button', { hasText: 'Save anyway' });
    const successMsg = extBuilder.getSuccessMessage();
    let dialogClicked = false;
    const pollDeadline = Date.now() + 40_000;
    while (Date.now() < pollDeadline) {
      if (await successMsg.isVisible()) break;
      if (!dialogClicked && await saveAnywayBtn.isVisible()) {
        await saveAnywayBtn.click();
        dialogClicked = true;
      }
      await page.waitForTimeout(200);
    }

    // Poll until ext_emconf.php appears on the host filesystem
    const emconfPath = path.join(EXT_BASE, 'ext_emconf.php');
    const deadline = Date.now() + 30_000;
    while (!fs.existsSync(emconfPath) && Date.now() < deadline) {
      await page.waitForTimeout(500);
    }

    await context.close();
  });

  test.afterAll(() => {
    fs.rmSync(EXT_BASE, { recursive: true, force: true });
  });

  // --- Phase 1: File structure assertions (synchronous) ---

  test('generated extension directory exists', () => {
    expect(fs.existsSync(EXT_BASE)).toBe(true);
  });

  test('22 model PHP files were generated', () => {
    const modelDir = path.join(EXT_BASE, 'Classes/Domain/Model');
    const files = fs.readdirSync(modelDir).filter(f => f.endsWith('.php'));
    expect(files).toHaveLength(22);
  });

  test('22 TCA configuration files were generated', () => {
    const tcaDir = path.join(EXT_BASE, 'Configuration/TCA');
    const files = fs.readdirSync(tcaDir).filter(f => f.endsWith('.php'));
    expect(files).toHaveLength(22);
  });

  // --- Phase 2: Load-back assertions ---

  test('load: all 22 containers appear on canvas after loading the extension', async ({ page }) => {
    const consoleErrors: string[] = [];
    page.on('console', msg => {
      if (msg.type() === 'error') consoleErrors.push(msg.text());
    });

    const backend = new BackendPage(page);
    await backend.navigateToModule('Extension Builder');
    const extBuilder = new ExtensionBuilderPage(backend.getContentFrame(), page);
    await extBuilder.waitForLoaded();
    await extBuilder.goToDomainModeller();

    const frame = backend.getContentFrame();
    await frame.locator('#WiringEditor-loadButton-button').click();
    const modal = page.locator('.t3js-modal');
    await expect(modal).toBeVisible();
    await modal.locator('select').selectOption(EXT_KEY);
    await modal.locator('.btn-primary').click();

    const result = await frame.locator('eb-wiring-editor').evaluate(async (el: any) => {
      // Give the async load a moment to start, then wait for _loading to clear
      await new Promise(r => setTimeout(r, 500));
      if (el._loading) {
        await new Promise<void>(resolve => {
          const check = setInterval(() => {
            if (!el._loading) { clearInterval(check); resolve(); }
          }, 100);
        });
      }
      const layer = el.shadowRoot?.querySelector('eb-layer');
      await layer?.updateComplete;
      // Poll until containers are rendered in the layer's shadow DOM
      let containers: NodeListOf<any> = layer?.shadowRoot?.querySelectorAll('eb-container') ?? ([] as any);
      const deadline = Date.now() + 15_000;
      while (containers.length < 22 && Date.now() < deadline) {
        await new Promise(r => setTimeout(r, 200));
        await layer?.updateComplete;
        containers = layer?.shadowRoot?.querySelectorAll('eb-container') ?? ([] as any);
      }
      return {
        extensionName: el.extensionName,
        containerCount: containers.length,
      };
    });

    expect(result.extensionName).toBe(EXT_KEY);
    expect(result.containerCount).toBe(22);
    expect(consoleErrors).toHaveLength(0);
  });

  // --- Phase 3: Edit and re-save assertion ---

  test('edit: adding a property to Article does not corrupt other model files', async ({ page }) => {
    const consoleErrors: string[] = [];
    page.on('console', msg => {
      if (msg.type() === 'error') consoleErrors.push(msg.text());
    });

    const backend = new BackendPage(page);
    await backend.navigateToModule('Extension Builder');
    const extBuilder = new ExtensionBuilderPage(backend.getContentFrame(), page);
    await extBuilder.waitForLoaded();
    await extBuilder.goToDomainModeller();

    // Load the extension
    const frame = backend.getContentFrame();
    await frame.locator('#WiringEditor-loadButton-button').click();
    const modal = page.locator('.t3js-modal');
    await expect(modal).toBeVisible();
    await modal.locator('select').selectOption(EXT_KEY);
    await modal.locator('.btn-primary').click();

    // Wait for load to complete and containers to render
    await frame.locator('eb-wiring-editor').evaluate(async (el: any) => {
      await new Promise(r => setTimeout(r, 500));
      if (el._loading) {
        await new Promise<void>(resolve => {
          const check = setInterval(() => {
            if (!el._loading) { clearInterval(check); resolve(); }
          }, 100);
        });
      }
      const layer = el.shadowRoot?.querySelector('eb-layer');
      await layer?.updateComplete;
      let containers: NodeListOf<any> = layer?.shadowRoot?.querySelectorAll('eb-container') ?? ([] as any);
      const deadline = Date.now() + 15_000;
      while (containers.length < 22 && Date.now() < deadline) {
        await new Promise(r => setTimeout(r, 200));
        await layer?.updateComplete;
        containers = layer?.shadowRoot?.querySelectorAll('eb-container') ?? ([] as any);
      }
    });

    // Add viewCount:Integer to Article
    await frame.locator('eb-wiring-editor').evaluate(async (el: any) => {
      const layer = el.shadowRoot?.querySelector('eb-layer');
      await layer?.updateComplete;

      const containers = Array.from(
        layer?.shadowRoot?.querySelectorAll('eb-container') ?? []
      ) as any[];

      // Find Article container by name field value
      let articleContainer: any = null;
      for (const container of containers) {
        await container.updateComplete;
        const nameField = container.shadowRoot?.querySelector('[name="name"]');
        if (nameField?.getValue?.() === 'Article') {
          articleContainer = container;
          break;
        }
      }
      if (!articleContainer) throw new Error('Article container not found');

      const listField = articleContainer.shadowRoot?.querySelector('[name="properties"]') as any;
      if (!listField) throw new Error('Properties list field not found in Article');

      // Append new property using setValue
      const existingProps = listField.getValue() ?? [];
      listField.setValue([
        ...existingProps,
        { propertyName: 'viewCount', propertyType: 'Integer' },
      ]);
      await listField.updateComplete;
    });

    // Re-generate the extension (poll for success OR "Save anyway" dialog simultaneously)
    await extBuilder.generateExtension();
    const saveAnywayBtn2 = page.locator('button', { hasText: 'Save anyway' });
    const successMsg2 = extBuilder.getSuccessMessage();
    let dialogClicked2 = false;
    const pollDeadline2 = Date.now() + 40_000;
    while (Date.now() < pollDeadline2) {
      if (await successMsg2.isVisible()) break;
      if (!dialogClicked2 && await saveAnywayBtn2.isVisible()) {
        await saveAnywayBtn2.click();
        dialogClicked2 = true;
      }
      await page.waitForTimeout(200);
    }

    // Poll until Article.php contains viewCount
    const articlePath = path.join(EXT_BASE, 'Classes/Domain/Model/Article.php');
    const deadline = Date.now() + 15_000;
    while (Date.now() < deadline) {
      if (fs.existsSync(articlePath) && fs.readFileSync(articlePath, 'utf8').includes('viewCount')) break;
      await page.waitForTimeout(500);
    }

    // Assert Article.php contains viewCount
    expect(fs.readFileSync(articlePath, 'utf8')).toContain('viewCount');

    // Assert all 22 model files still exist
    const modelDir = path.join(EXT_BASE, 'Classes/Domain/Model');
    const files = fs.readdirSync(modelDir).filter(f => f.endsWith('.php'));
    expect(files).toHaveLength(22);

    expect(consoleErrors).toHaveLength(0);
  });
});
