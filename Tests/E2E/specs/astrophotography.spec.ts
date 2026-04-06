import { test, expect } from '@playwright/test';
import * as fs from 'fs';
import * as path from 'path';
import { BackendPage } from '../pages/BackendPage';
import { ExtensionBuilderPage } from '../pages/ExtensionBuilderPage';
import { createScreenshotter } from '../helpers/screenshot';

const EXT_KEY = 'eb_astrophotography';
// Tests/E2E/specs/ -> up 4 levels -> packages/
const PACKAGES_DIR = path.resolve(__dirname, '../../../../');
const EXT_BASE = path.join(PACKAGES_DIR, EXT_KEY);

// Factory for a standard zeroToMany relation entry.
const REL = (relationName: string) => ({
  uid: '',
  relationName,
  relationType: 'zeroToMany',
  renderType: 'selectMultipleSideBySide',
  relationDescription: '',
  propertyIsExcludeField: true,
  lazyLoading: false,
  foreignRelationClass: '',
});

type Relation = ReturnType<typeof REL>;
type Property = { propertyName: string; propertyType: string };
type ModelDef = { name: string; properties: Property[]; relations: Relation[] };
type WireDef = { src: { moduleId: number; terminal: string; uid: string }; tgt: { moduleId: number; terminal: string; uid: string } };

const MODELS: ModelDef[] = [
  // 0: AstroImage — the central entity
  { name: 'AstroImage', relations: [
    REL('celestialObjects'), REL('imagingSessions'), REL('processingRecipes'), REL('awards'),
  ], properties: [
    { propertyName: 'title',           propertyType: 'String' },
    { propertyName: 'slug',            propertyType: 'Slug' },
    { propertyName: 'description',     propertyType: 'RichText' },
    { propertyName: 'image',           propertyType: 'Image' },
    { propertyName: 'captureDateTime', propertyType: 'DateTime' },
    { propertyName: 'publishDate',     propertyType: 'Date' },
    { propertyName: 'featured',        propertyType: 'Boolean' },
  ]},
  // 1: CelestialObject
  { name: 'CelestialObject', relations: [], properties: [
    { propertyName: 'name',               propertyType: 'String' },
    { propertyName: 'catalogId',          propertyType: 'String' },
    { propertyName: 'objectType',         propertyType: 'Select' },
    { propertyName: 'constellation',      propertyType: 'String' },
    { propertyName: 'rightAscension',     propertyType: 'String' },
    { propertyName: 'declination',        propertyType: 'String' },
    { propertyName: 'magnitude',          propertyType: 'Float' },
    { propertyName: 'distanceLightyears', propertyType: 'Float' },
    { propertyName: 'description',        propertyType: 'RichText' },
    { propertyName: 'previewImage',       propertyType: 'Image' },
    { propertyName: 'active',             propertyType: 'Boolean' },
  ]},
  // 2: Telescope
  { name: 'Telescope', relations: [], properties: [
    { propertyName: 'name',         propertyType: 'String' },
    { propertyName: 'brand',        propertyType: 'String' },
    { propertyName: 'telescopeType',propertyType: 'Select' },
    { propertyName: 'focalLength',  propertyType: 'Integer' },
    { propertyName: 'aperture',     propertyType: 'Integer' },
    { propertyName: 'focalRatio',   propertyType: 'Float' },
    { propertyName: 'purchaseDate', propertyType: 'NativeDate' },
    { propertyName: 'active',       propertyType: 'Boolean' },
    { propertyName: 'notes',        propertyType: 'Text' },
    { propertyName: 'image',        propertyType: 'Image' },
  ]},
  // 3: Camera
  { name: 'Camera', relations: [], properties: [
    { propertyName: 'name',         propertyType: 'String' },
    { propertyName: 'brand',        propertyType: 'String' },
    { propertyName: 'sensorType',   propertyType: 'Select' },
    { propertyName: 'sensorWidth',  propertyType: 'Float' },
    { propertyName: 'sensorHeight', propertyType: 'Float' },
    { propertyName: 'pixelSize',    propertyType: 'Float' },
    { propertyName: 'megapixels',   propertyType: 'Float' },
    { propertyName: 'cooled',       propertyType: 'Boolean' },
    { propertyName: 'purchaseDate', propertyType: 'NativeDate' },
    { propertyName: 'active',       propertyType: 'Boolean' },
  ]},
  // 4: AstroFilter
  { name: 'AstroFilter', relations: [], properties: [
    { propertyName: 'name',             propertyType: 'String' },
    { propertyName: 'filterType',       propertyType: 'Select' },
    { propertyName: 'centralWavelength',propertyType: 'Integer' },
    { propertyName: 'bandwidth',        propertyType: 'Float' },
    { propertyName: 'color',            propertyType: 'ColorPicker' },
    { propertyName: 'manufacturer',     propertyType: 'String' },
    { propertyName: 'diameter',         propertyType: 'Float' },
    { propertyName: 'active',           propertyType: 'Boolean' },
  ]},
  // 5: ObservingSite
  { name: 'ObservingSite', relations: [], properties: [
    { propertyName: 'name',         propertyType: 'String' },
    { propertyName: 'description',  propertyType: 'Text' },
    { propertyName: 'latitude',     propertyType: 'Float' },
    { propertyName: 'longitude',    propertyType: 'Float' },
    { propertyName: 'altitude',     propertyType: 'Integer' },
    { propertyName: 'bortleClass',  propertyType: 'Integer' },
    { propertyName: 'website',      propertyType: 'InputLink' },
    { propertyName: 'contactEmail', propertyType: 'Email' },
    { propertyName: 'active',       propertyType: 'Boolean' },
    { propertyName: 'image',        propertyType: 'Image' },
  ]},
  // 6: ImagingSession — hub for equipment wires
  { name: 'ImagingSession', relations: [
    REL('observingSites'), REL('telescopes'), REL('cameras'), REL('astroFilters'),
  ], properties: [
    { propertyName: 'sessionDate',      propertyType: 'NativeDate' },
    { propertyName: 'startTime',        propertyType: 'NativeTime' },
    { propertyName: 'endTime',          propertyType: 'Time' },
    { propertyName: 'frameExposure',    propertyType: 'TimeSec' },
    { propertyName: 'temperature',      propertyType: 'Float' },
    { propertyName: 'humidity',         propertyType: 'Integer' },
    { propertyName: 'seeingConditions', propertyType: 'Integer' },
    { propertyName: 'transparency',     propertyType: 'Integer' },
    { propertyName: 'moonPhase',        propertyType: 'Integer' },
    { propertyName: 'totalFrames',      propertyType: 'Integer' },
    { propertyName: 'usableFrames',     propertyType: 'Integer' },
    { propertyName: 'notes',            propertyType: 'Text' },
  ]},
  // 7: ProcessingRecipe
  { name: 'ProcessingRecipe', relations: [
    REL('cameras'),
  ], properties: [
    { propertyName: 'title',                propertyType: 'String' },
    { propertyName: 'software',             propertyType: 'String' },
    { propertyName: 'description',          propertyType: 'RichText' },
    { propertyName: 'stackingMethod',       propertyType: 'Select' },
    { propertyName: 'totalIntegrationTime', propertyType: 'Float' },
    { propertyName: 'processingDate',       propertyType: 'NativeDateTime' },
    { propertyName: 'recipeFile',           propertyType: 'File' },
    { propertyName: 'active',               propertyType: 'Boolean' },
    { propertyName: 'notes',                propertyType: 'Text' },
  ]},
  // 8: Award
  { name: 'Award', relations: [], properties: [
    { propertyName: 'title',           propertyType: 'String' },
    { propertyName: 'organization',    propertyType: 'String' },
    { propertyName: 'awardDate',       propertyType: 'NativeDate' },
    { propertyName: 'description',     propertyType: 'Text' },
    { propertyName: 'certificateFile', propertyType: 'File' },
    { propertyName: 'sourceUrl',       propertyType: 'InputLink' },
    { propertyName: 'active',          propertyType: 'Boolean' },
  ]},
];

// Wire definitions. src.terminal 'REL_N' = Nth entry in that module's relations array.
// tgt.terminal 'SOURCES' = the receiving end of every container.
const WIRES: WireDef[] = [
  // AstroImage (0) → CelestialObject (1)
  { src: { moduleId: 0, terminal: 'REL_0', uid: '' }, tgt: { moduleId: 1, terminal: 'SOURCES', uid: '' } },
  // AstroImage (0) → ImagingSession (6)
  { src: { moduleId: 0, terminal: 'REL_1', uid: '' }, tgt: { moduleId: 6, terminal: 'SOURCES', uid: '' } },
  // AstroImage (0) → ProcessingRecipe (7)
  { src: { moduleId: 0, terminal: 'REL_2', uid: '' }, tgt: { moduleId: 7, terminal: 'SOURCES', uid: '' } },
  // AstroImage (0) → Award (8)
  { src: { moduleId: 0, terminal: 'REL_3', uid: '' }, tgt: { moduleId: 8, terminal: 'SOURCES', uid: '' } },
  // ImagingSession (6) → ObservingSite (5)
  { src: { moduleId: 6, terminal: 'REL_0', uid: '' }, tgt: { moduleId: 5, terminal: 'SOURCES', uid: '' } },
  // ImagingSession (6) → Telescope (2)
  { src: { moduleId: 6, terminal: 'REL_1', uid: '' }, tgt: { moduleId: 2, terminal: 'SOURCES', uid: '' } },
  // ImagingSession (6) → Camera (3)
  { src: { moduleId: 6, terminal: 'REL_2', uid: '' }, tgt: { moduleId: 3, terminal: 'SOURCES', uid: '' } },
  // ImagingSession (6) → AstroFilter (4)
  { src: { moduleId: 6, terminal: 'REL_3', uid: '' }, tgt: { moduleId: 4, terminal: 'SOURCES', uid: '' } },
  // ProcessingRecipe (7) → Camera (3)
  { src: { moduleId: 7, terminal: 'REL_0', uid: '' }, tgt: { moduleId: 3, terminal: 'SOURCES', uid: '' } },
];

test.describe('Astrophotography Demo Extension', () => {
  test.setTimeout(300_000);

  test.beforeAll(async ({ browser }) => {
    test.setTimeout(300_000);

    if (fs.existsSync(EXT_BASE)) {
      fs.rmSync(EXT_BASE, { recursive: true });
    }

    const context = await browser.newContext({
      storageState: path.resolve(__dirname, '../.auth/user.json'),
      baseURL: 'https://extensionbuilder.ddev.site',
      ignoreHTTPSErrors: true,
    });
    const page = await context.newPage();
    const backend = new BackendPage(page);
    const ss = createScreenshotter(page, 'astrophotography-setup');

    await backend.navigateToModule('Extension Builder');
    const extBuilder = new ExtensionBuilderPage(backend.getContentFrame(), page);
    await extBuilder.waitForLoaded();
    await extBuilder.goToDomainModeller();
    await ss('domain-modeller-loaded');
    await extBuilder.openNewExtension();
    await extBuilder.fillExtensionProperties('EB Astrophotography', EXT_KEY, 'AcmeCorp');
    await ss('properties-filled');

    const frame = backend.getContentFrame();
    const COLS = 3;
    const COL_SPACING = 350;
    const ROW_SPACING = 250;

    await frame.locator('eb-wiring-editor').evaluate(
      async (el: any, { extKey, models, wires, cols, colSpacing, rowSpacing }: {
        extKey: string;
        models: Array<{ name: string; properties: Array<{ propertyName: string; propertyType: string }>; relations: Array<Record<string, unknown>> }>;
        wires: Array<{ src: { moduleId: number; terminal: string; uid: string }; tgt: { moduleId: number; terminal: string; uid: string } }>;
        cols: number;
        colSpacing: number;
        rowSpacing: number;
      }) => {
        el.extensionName = extKey;

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
            actionGroup: { _default1_list: true, _default2_show: true },
            propertyGroup: { properties: model.properties },
            relationGroup: { relations: model.relations },
          },
        }));

        layer.addContainers(containerConfigs);
        await layer.updateComplete;

        const containers = Array.from(
          layer.shadowRoot?.querySelectorAll('eb-container') ?? []
        ) as any[];
        for (const container of containers) {
          await container.updateComplete;
        }

        // Add visual wires and wait for them to be rendered.
        layer.addWires(wires, containerConfigs);
        await new Promise<void>((resolve) => {
          const deadline = Date.now() + 5_000;
          const check = setInterval(() => {
            if ((layer._wires?.length ?? 0) >= wires.length || Date.now() > deadline) {
              clearInterval(check);
              resolve();
            }
          }, 100);
        });

        // Plugins
        const pluginsField = el.shadowRoot?.querySelector('[name="plugins"]');
        if (pluginsField) {
          pluginsField.setValue([
            { name: 'Image Gallery', key: 'imageGallery', description: '', actions: { controllerActionCombinations: 'AstroImage => list,show' } },
            { name: 'Sky Atlas',     key: 'skyAtlas',     description: '', actions: { controllerActionCombinations: 'CelestialObject => list,show' } },
          ]);
        }

        // Backend module
        const modulesField = el.shadowRoot?.querySelector('[name="backendModules"]');
        if (modulesField) {
          modulesField.setValue([
            { name: 'Astro Manager', key: 'astroManager', description: '', mainModule: 'web', tabLabel: 'Astro Manager', actions: { controllerActionCombinations: 'AstroImage => list,show' } },
          ]);
        }
      },
      { extKey: EXT_KEY, models: MODELS, wires: WIRES, cols: COLS, colSpacing: COL_SPACING, rowSpacing: ROW_SPACING }
    );

    await ss('models-wires-configured');
    await extBuilder.generateExtension();

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

    const emconfPath = path.join(EXT_BASE, 'ext_emconf.php');
    const deadline = Date.now() + 30_000;
    while (!fs.existsSync(emconfPath) && Date.now() < deadline) {
      await page.waitForTimeout(500);
    }

    await ss('generation-complete');
    await context.close();
  });

  // --- Phase 1: File structure ---

  test('generated extension directory exists', () => {
    expect(fs.existsSync(EXT_BASE)).toBe(true);
  });

  test('9 model PHP files were generated', () => {
    const modelDir = path.join(EXT_BASE, 'Classes/Domain/Model');
    const files = fs.readdirSync(modelDir).filter(f => f.endsWith('.php'));
    expect(files).toHaveLength(9);
  });

  test('9 TCA configuration files were generated', () => {
    const tcaDir = path.join(EXT_BASE, 'Configuration/TCA');
    const files = fs.readdirSync(tcaDir).filter(f => f.endsWith('.php'));
    expect(files).toHaveLength(9);
  });

  test('AstroImage.php contains all 7 declared properties', () => {
    const content = fs.readFileSync(path.join(EXT_BASE, 'Classes/Domain/Model/AstroImage.php'), 'utf8');
    for (const prop of ['title', 'slug', 'description', 'image', 'captureDateTime', 'publishDate', 'featured']) {
      expect(content).toContain(prop);
    }
  });

  test('ImagingSession.php contains all 12 declared properties', () => {
    const content = fs.readFileSync(path.join(EXT_BASE, 'Classes/Domain/Model/ImagingSession.php'), 'utf8');
    for (const prop of ['sessionDate', 'startTime', 'endTime', 'frameExposure', 'temperature', 'humidity', 'seeingConditions', 'transparency', 'moonPhase', 'totalFrames', 'usableFrames', 'notes']) {
      expect(content).toContain(prop);
    }
  });

  // --- Phase 2: Load-back ---

  test('load: all 9 containers appear on canvas', async ({ page }) => {
    const ss = createScreenshotter(page, 'astrophotography-load');
    const consoleErrors: string[] = [];
    page.on('console', msg => {
      if (msg.type() === 'error') consoleErrors.push(msg.text());
    });

    const backend = new BackendPage(page);
    await backend.navigateToModule('Extension Builder');
    const extBuilder = new ExtensionBuilderPage(backend.getContentFrame(), page);
    await extBuilder.waitForLoaded();
    await extBuilder.goToDomainModeller();
    await ss('domain-modeller-loaded');

    const frame = backend.getContentFrame();
    await frame.locator('#WiringEditor-loadButton-button').click();
    const modal = page.locator('.t3js-modal');
    await expect(modal).toBeVisible();
    await modal.locator('select').selectOption(EXT_KEY);
    await modal.locator('.btn-primary').click();

    const result = await frame.locator('eb-wiring-editor').evaluate(async (el: any) => {
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
      while (containers.length < 9 && Date.now() < deadline) {
        await new Promise(r => setTimeout(r, 200));
        await layer?.updateComplete;
        containers = layer?.shadowRoot?.querySelectorAll('eb-container') ?? ([] as any);
      }
      return { extensionName: el.extensionName, containerCount: containers.length };
    });

    await ss('9-containers-loaded');
    expect(result.extensionName).toBe(EXT_KEY);
    expect(result.containerCount).toBe(9);
    expect(consoleErrors).toHaveLength(0);
  });

  // --- Phase 3: Edit and re-save ---

  test('edit: adding a property to AstroImage does not corrupt other model files', async ({ page }) => {
    const ss = createScreenshotter(page, 'astrophotography-edit');
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

    // Wait for load
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
      while (containers.length < 9 && Date.now() < deadline) {
        await new Promise(r => setTimeout(r, 200));
        await layer?.updateComplete;
        containers = layer?.shadowRoot?.querySelectorAll('eb-container') ?? ([] as any);
      }
    });

    // Add stackCount:Integer to AstroImage
    await frame.locator('eb-wiring-editor').evaluate(async (el: any) => {
      const layer = el.shadowRoot?.querySelector('eb-layer');
      await layer?.updateComplete;
      const containers = Array.from(layer?.shadowRoot?.querySelectorAll('eb-container') ?? []) as any[];
      let astroImageContainer: any = null;
      for (const container of containers) {
        await container.updateComplete;
        const nameField = container.shadowRoot?.querySelector('[name="name"]');
        if (nameField?.getValue?.() === 'AstroImage') {
          astroImageContainer = container;
          break;
        }
      }
      if (!astroImageContainer) throw new Error('AstroImage container not found');
      const listField = astroImageContainer.shadowRoot?.querySelector('[name="properties"]') as any;
      if (!listField) throw new Error('Properties list field not found in AstroImage');
      const existing = listField.getValue() ?? [];
      listField.setValue([...existing, { propertyName: 'stackCount', propertyType: 'Integer' }]);
      await listField.updateComplete;
    });

    await ss('stackcount-property-added');
    await extBuilder.generateExtension();

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

    const astroImagePath = path.join(EXT_BASE, 'Classes/Domain/Model/AstroImage.php');
    const deadline = Date.now() + 15_000;
    while (Date.now() < deadline) {
      if (fs.existsSync(astroImagePath) && fs.readFileSync(astroImagePath, 'utf8').includes('stackCount')) break;
      await page.waitForTimeout(500);
    }

    await ss('regeneration-complete');
    expect(fs.readFileSync(astroImagePath, 'utf8')).toContain('stackCount');

    const modelDir = path.join(EXT_BASE, 'Classes/Domain/Model');
    const files = fs.readdirSync(modelDir).filter(f => f.endsWith('.php'));
    expect(files).toHaveLength(9);

    expect(consoleErrors).toHaveLength(0);
  });
});
