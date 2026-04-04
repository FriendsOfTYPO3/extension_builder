import { test, expect } from '@playwright/test';
import * as fs from 'fs';
import * as path from 'path';
import { spawnSync } from 'child_process';
import { BackendPage } from '../pages/BackendPage';
import { ExtensionBuilderPage } from '../pages/ExtensionBuilderPage';

// Tests/E2E/specs/ -> up 4 levels -> packages/
const PACKAGES_DIR = path.resolve(__dirname, '../../../../');
// Tests/E2E/specs/ -> up 5 levels -> project root
const PROJECT_ROOT = path.resolve(__dirname, '../../../../../');
const AUTH_FILE = path.resolve(__dirname, '../.auth/user.json');
const SETTINGS_FILE = path.resolve(__dirname, '../../../../../config/system/settings.php');

const SPLIT_TOKEN = '## EXTENSION BUILDER DEFAULTS END TOKEN - Everything BEFORE this line is overwritten with the defaults of the extension builder';

/** Wait for a file to appear on the host filesystem after a UI action. */
async function waitForFile(filePath: string, timeoutMs = 15000): Promise<void> {
  const deadline = Date.now() + timeoutMs;
  while (!fs.existsSync(filePath) && Date.now() < deadline) {
    await new Promise(r => setTimeout(r, 500));
  }
}

/** Open a new browser context with stored auth. */
function makeContext(browser: any) {
  return browser.newContext({
    storageState: AUTH_FILE,
    baseURL: 'https://extensionbuilder.ddev.site',
    ignoreHTTPSErrors: true,
  });
}

/** Navigate to Extension Builder domain modeller. */
async function openDomainModeller(page: any) {
  const backend = new BackendPage(page);
  await backend.navigateToModule('Extension Builder');
  const extBuilder = new ExtensionBuilderPage(backend.getContentFrame(), page);
  await extBuilder.waitForLoaded();
  await extBuilder.goToDomainModeller();
  return { backend, extBuilder };
}

/** Generate a minimal extension with a single domain model via the UI. */
async function generateMinimalExtension(
  page: any,
  extKey: string,
  modelName: string,
  properties: Array<{ propertyName: string; propertyType: string }> = []
): Promise<void> {
  const { backend, extBuilder } = await openDomainModeller(page);
  await extBuilder.openNewExtension();
  await extBuilder.fillExtensionProperties(extKey, extKey, 'RoundtripTest');

  const frame = backend.getContentFrame();
  await frame.locator('eb-wiring-editor').evaluate(
    async (el: any, args: { extKey: string; modelName: string; properties: Array<{ propertyName: string; propertyType: string }> }) => {
      el.extensionName = args.extKey;
      const layer = el.shadowRoot?.querySelector('eb-layer');
      if (layer) {
        layer.addContainers([{
          config: { position: [20, 20] },
          value: {
            name: args.modelName,
            objectsettings: {
              description: '',
              type: 'Entity',
              aggregateRoot: true,
              addDeletedField: false,
              addHiddenField: false,
              addStarttimeEndtimeFields: false,
              categorizable: false,
            },
            actionGroup: { _default1_list: true },
            propertyGroup: { properties: args.properties },
            relationGroup: { relations: [] },
          },
        }]);
        const containers = Array.from(layer.shadowRoot?.querySelectorAll('eb-container') ?? []) as any[];
        for (const c of containers) { await c.updateComplete; }
      }
    },
    { extKey, modelName, properties }
  );

  await extBuilder.generateExtension();
  await expect(extBuilder.getSuccessMessage()).toBeVisible({ timeout: 15000 });
}

/** Load an existing extension in the editor by key and re-save it. */
async function reloadAndSaveExtension(page: any, extKey: string): Promise<void> {
  const { backend, extBuilder } = await openDomainModeller(page);
  const frame = backend.getContentFrame();

  await frame.locator('#WiringEditor-loadButton-button').click();
  const modal = page.locator('.t3js-modal');
  await expect(modal).toBeVisible({ timeout: 10000 });
  await modal.locator('select').selectOption(extKey);
  await modal.locator('.btn-primary').click();

  await frame.locator('eb-wiring-editor').evaluate(async (el: any) => {
    if (el._loading) {
      await new Promise<void>(resolve => {
        const check = setInterval(() => {
          if (!el._loading) { clearInterval(check); resolve(); }
        }, 100);
      });
    }
  });

  await extBuilder.generateExtension();
  await expect(extBuilder.getSuccessMessage()).toBeVisible({ timeout: 15000 });
}

/** Read settings.php and toggle the enableRoundtrip value. */
function setRoundtripEnabled(enabled: boolean): void {
  const content = fs.readFileSync(SETTINGS_FILE, 'utf8');
  const updated = enabled
    ? content.replace(/'enableRoundtrip' => '0'/, "'enableRoundtrip' => '1'")
    : content.replace(/'enableRoundtrip' => '1'/, "'enableRoundtrip' => '0'");
  fs.writeFileSync(SETTINGS_FILE, updated);
  // Flush TYPO3 cache so the new setting takes effect immediately
  spawnSync('ddev', ['exec', 'vendor/bin/typo3', 'cache:flush'], {
    cwd: PROJECT_ROOT,
    encoding: 'utf8',
    stdio: 'pipe',
  });
}

// ---------------------------------------------------------------------------
// EBUILDER-116: Split token preservation
// EBUILDER-117: Domain object rename
// EBUILDER-118: Roundtrip disabled — custom code NOT preserved
// ---------------------------------------------------------------------------
test.describe('Roundtrip Mode: basic scenarios', () => {
  test.setTimeout(120_000);

  /**
   * EBUILDER-116: When a model file contains custom code below the split token,
   * re-generating the extension must preserve that custom code.
   */
  test('split token: custom code below split token survives regeneration', async ({ browser }) => {
    const EXT_KEY = 'eb_rt_split';
    const MODEL_NAME = 'SplitModel';
    const MODEL_FILE = path.join(PACKAGES_DIR, EXT_KEY, 'Classes/Domain/Model', `${MODEL_NAME}.php`);

    // Clean up from any previous run
    fs.rmSync(path.join(PACKAGES_DIR, EXT_KEY), { recursive: true, force: true });

    const context = await makeContext(browser);
    const page = await context.newPage();

    try {
      // Step 1: Generate the initial extension
      await generateMinimalExtension(page, EXT_KEY, MODEL_NAME, [
        { propertyName: 'title', propertyType: 'String' },
      ]);
      await waitForFile(MODEL_FILE);
      expect(fs.existsSync(MODEL_FILE)).toBe(true);

      // Step 2: Inject split token + custom code
      const originalContent = fs.readFileSync(MODEL_FILE, 'utf8');
      const customCode = '\n// MY CUSTOM METHOD\npublic function customBehavior(): void { /* custom */ }\n';
      fs.writeFileSync(MODEL_FILE, originalContent + SPLIT_TOKEN + customCode);

      // Step 3: Re-generate via UI (load and save again)
      await reloadAndSaveExtension(page, EXT_KEY);
      // Wait briefly for filesystem sync
      await page.waitForTimeout(2000);

      // Step 4: Assert custom code below split token is preserved
      const regenerated = fs.readFileSync(MODEL_FILE, 'utf8');
      expect(regenerated).toContain(SPLIT_TOKEN);
      expect(regenerated).toContain('MY CUSTOM METHOD');
    } finally {
      await context.close();
      fs.rmSync(path.join(PACKAGES_DIR, EXT_KEY), { recursive: true, force: true });
    }
  });

  /**
   * EBUILDER-117: Renaming a domain object via the UI renames all generated files.
   */
  test('domain object rename: Foo renamed to Bar produces Bar class files', async ({ browser }) => {
    const EXT_KEY = 'eb_rt_rename';
    const MODEL_DIR = path.join(PACKAGES_DIR, EXT_KEY, 'Classes/Domain/Model');

    // Clean up from any previous run
    fs.rmSync(path.join(PACKAGES_DIR, EXT_KEY), { recursive: true, force: true });

    const context = await makeContext(browser);
    const page = await context.newPage();

    try {
      // Step 1: Generate extension with Foo model
      await generateMinimalExtension(page, EXT_KEY, 'Foo', [
        { propertyName: 'title', propertyType: 'String' },
      ]);
      await waitForFile(path.join(MODEL_DIR, 'Foo.php'));
      expect(fs.existsSync(path.join(MODEL_DIR, 'Foo.php'))).toBe(true);

      // Step 2: Load the extension and rename Foo → Bar, then save
      const { backend, extBuilder } = await openDomainModeller(page);
      const frame = backend.getContentFrame();

      await frame.locator('#WiringEditor-loadButton-button').click();
      const modal = page.locator('.t3js-modal');
      await expect(modal).toBeVisible({ timeout: 10000 });
      await modal.locator('select').selectOption(EXT_KEY);
      await modal.locator('.btn-primary').click();

      // Wait for load to complete
      await frame.locator('eb-wiring-editor').evaluate(async (el: any) => {
        if (el._loading) {
          await new Promise<void>(resolve => {
            const check = setInterval(() => {
              if (!el._loading) { clearInterval(check); resolve(); }
            }, 100);
          });
        }
      });

      // Rename the domain model from Foo to Bar via the shadow DOM
      await frame.locator('eb-wiring-editor').evaluate(async (el: any) => {
        const layer = el.shadowRoot?.querySelector('eb-layer');
        if (!layer) { return; }
        const containers = Array.from(layer.shadowRoot?.querySelectorAll('eb-container') ?? []) as any[];
        for (const container of containers) {
          if (container._name === 'Foo') {
            container._name = 'Bar';
            await container.updateComplete;
          }
        }
      });

      // Click save — with roundtrip + rename, a preview modal appears first
      await extBuilder.generateExtension();
      const previewModal = page.locator('.t3js-modal');
      await expect(previewModal).toBeVisible({ timeout: 10000 });
      await previewModal.getByRole('button', { name: 'Generate' }).click();
      await expect(extBuilder.getSuccessMessage()).toBeVisible({ timeout: 15000 });
      await page.waitForTimeout(2000);

      // Step 3: Assert Bar files exist
      expect(fs.existsSync(path.join(MODEL_DIR, 'Bar.php'))).toBe(true);
      expect(fs.existsSync(path.join(PACKAGES_DIR, EXT_KEY, 'Classes/Controller/BarController.php'))).toBe(true);
      expect(fs.existsSync(path.join(PACKAGES_DIR, EXT_KEY, 'Classes/Domain/Repository/BarRepository.php'))).toBe(true);
    } finally {
      await context.close();
      fs.rmSync(path.join(PACKAGES_DIR, EXT_KEY), { recursive: true, force: true });
    }
  });

  /**
   * EBUILDER-118: When roundtrip is disabled, custom code below the split token
   * must NOT be preserved during regeneration.
   */
  test('roundtrip disabled: custom code below split token is NOT preserved', async ({ browser }) => {
    const EXT_KEY = 'eb_rt_disabled';
    const MODEL_NAME = 'NoRoundtrip';
    const MODEL_FILE = path.join(PACKAGES_DIR, EXT_KEY, 'Classes/Domain/Model', `${MODEL_NAME}.php`);

    // Clean up from any previous run
    fs.rmSync(path.join(PACKAGES_DIR, EXT_KEY), { recursive: true, force: true });

    const context = await makeContext(browser);
    const page = await context.newPage();

    try {
      // Step 1: Generate the initial extension (roundtrip is ON)
      await generateMinimalExtension(page, EXT_KEY, MODEL_NAME, [
        { propertyName: 'title', propertyType: 'String' },
      ]);
      await waitForFile(MODEL_FILE);

      // Step 2: Inject split token + custom code
      const originalContent = fs.readFileSync(MODEL_FILE, 'utf8');
      const customCode = '\n// SHOULD NOT SURVIVE\npublic function shouldBeGone(): void {}\n';
      fs.writeFileSync(MODEL_FILE, originalContent + SPLIT_TOKEN + customCode);

      // Step 3: Disable roundtrip
      setRoundtripEnabled(false);

      try {
        // Step 4: Re-generate via UI
        // With roundtrip disabled + existing extension, the backend returns a
        // confirm response (Error 500 = EXTENSION_DIR_EXISTS), so after clicking
        // Save we must also click the "Save anyway" button in the modal.
        const { backend, extBuilder } = await openDomainModeller(page);
        const frame = backend.getContentFrame();

        await frame.locator('#WiringEditor-loadButton-button').click();
        const loadModal = page.locator('.t3js-modal');
        await expect(loadModal).toBeVisible({ timeout: 10000 });
        await loadModal.locator('select').selectOption(EXT_KEY);
        await loadModal.locator('.btn-primary').click();

        await frame.locator('eb-wiring-editor').evaluate(async (el: any) => {
          if (el._loading) {
            await new Promise<void>(resolve => {
              const check = setInterval(() => {
                if (!el._loading) { clearInterval(check); resolve(); }
              }, 100);
            });
          }
        });

        await extBuilder.generateExtension();
        // The confirmation modal ("Save anyway") appears because roundtrip is off
        const saveModal = page.locator('.t3js-modal');
        await expect(saveModal).toBeVisible({ timeout: 10000 });
        await saveModal.getByRole('button', { name: 'Save anyway' }).click();
        await expect(extBuilder.getSuccessMessage()).toBeVisible({ timeout: 15000 });
        await page.waitForTimeout(2000);

        // Step 5: Custom code must NOT be preserved
        const regenerated = fs.readFileSync(MODEL_FILE, 'utf8');
        expect(regenerated).not.toContain('SHOULD NOT SURVIVE');
      } finally {
        // Always restore roundtrip setting
        setRoundtripEnabled(true);
      }
    } finally {
      await context.close();
      fs.rmSync(path.join(PACKAGES_DIR, EXT_KEY), { recursive: true, force: true });
    }
  });
});

// ---------------------------------------------------------------------------
// EBUILDER-129: Complex stress test — 10 models, 5 with custom code injected
// ---------------------------------------------------------------------------
test.describe('Roundtrip Mode: complex stress test', () => {
  test.setTimeout(300_000);

  /**
   * EBUILDER-129: With 10 domain models generated and custom code injected
   * into 5 of them, re-saving must preserve the custom code in the 5 injected
   * models and leave the 5 control models unaffected.
   */
  test('10 models: custom code survives in injected models, absent in control models', async ({ browser }) => {
    const EXT_KEY = 'eb_rt_stress';
    const EXT_BASE = path.join(PACKAGES_DIR, EXT_KEY);

    const INJECTED_MODELS = ['Alpha', 'Beta', 'Gamma', 'Delta', 'Epsilon'];
    const CONTROL_MODELS = ['Zeta', 'Eta', 'Theta', 'Iota', 'Kappa'];
    const ALL_MODELS = [...INJECTED_MODELS, ...CONTROL_MODELS];

    // Clean up from any previous run
    fs.rmSync(EXT_BASE, { recursive: true, force: true });

    const context = await makeContext(browser);
    const page = await context.newPage();

    try {
      // Step 1: Generate extension with 10 models
      const { backend, extBuilder } = await openDomainModeller(page);
      await extBuilder.openNewExtension();
      await extBuilder.fillExtensionProperties(EXT_KEY, EXT_KEY, 'StressTest');

      const frame = backend.getContentFrame();
      await frame.locator('eb-wiring-editor').evaluate(
        async (el: any, args: { extKey: string; models: string[] }) => {
          el.extensionName = args.extKey;
          const layer = el.shadowRoot?.querySelector('eb-layer');
          if (!layer) { return; }

          const COLS = 5;
          const COL_SPACING = 350;
          const ROW_SPACING = 250;

          layer.addContainers(args.models.map((name, i) => ({
            config: { position: [(i % COLS) * COL_SPACING + 20, Math.floor(i / COLS) * ROW_SPACING + 20] },
            value: {
              name,
              objectsettings: {
                description: '',
                type: 'Entity',
                aggregateRoot: true,
                addDeletedField: false,
                addHiddenField: false,
                addStarttimeEndtimeFields: false,
                categorizable: false,
              },
              actionGroup: { _default1_list: true },
              propertyGroup: {
                properties: [
                  { propertyName: 'title', propertyType: 'String' },
                  { propertyName: 'description', propertyType: 'Text' },
                  { propertyName: 'active', propertyType: 'Boolean' },
                ],
              },
              relationGroup: { relations: [] },
            },
          })));

          await layer.updateComplete;
          const containers = Array.from(layer.shadowRoot?.querySelectorAll('eb-container') ?? []) as any[];
          for (const c of containers) { await c.updateComplete; }
        },
        { extKey: EXT_KEY, models: ALL_MODELS }
      );

      await extBuilder.generateExtension();
      await expect(extBuilder.getSuccessMessage()).toBeVisible({ timeout: 30000 });

      // Wait for all model files to appear
      for (const modelName of ALL_MODELS) {
        await waitForFile(path.join(EXT_BASE, 'Classes/Domain/Model', `${modelName}.php`));
      }

      // Step 2: Inject custom code into the 5 injected models
      for (const modelName of INJECTED_MODELS) {
        const modelFile = path.join(EXT_BASE, 'Classes/Domain/Model', `${modelName}.php`);
        const content = fs.readFileSync(modelFile, 'utf8');
        const customCode = `\n// CUSTOM_${modelName.toUpperCase()}\npublic function custom${modelName}(): void {}\n`;
        fs.writeFileSync(modelFile, content + SPLIT_TOKEN + customCode);
      }

      // Step 3: Re-generate via UI (load and save)
      await reloadAndSaveExtension(page, EXT_KEY);
      await page.waitForTimeout(3000);

      // Step 4: Assert custom code preserved in injected models
      for (const modelName of INJECTED_MODELS) {
        const modelFile = path.join(EXT_BASE, 'Classes/Domain/Model', `${modelName}.php`);
        const content = fs.readFileSync(modelFile, 'utf8');
        expect(content).toContain(`CUSTOM_${modelName.toUpperCase()}`);
        expect(content).toContain(SPLIT_TOKEN);
      }

      // Step 5: Assert custom code absent in control models
      for (const modelName of CONTROL_MODELS) {
        const modelFile = path.join(EXT_BASE, 'Classes/Domain/Model', `${modelName}.php`);
        const content = fs.readFileSync(modelFile, 'utf8');
        expect(content).not.toContain(`CUSTOM_${modelName.toUpperCase()}`);
      }
    } finally {
      await context.close();
      fs.rmSync(EXT_BASE, { recursive: true, force: true });
    }
  });
});
