import { test, expect } from '@playwright/test';
import * as fs from 'fs';
import * as path from 'path';
import { execFileSync } from 'child_process';
import { BackendPage } from '../pages/BackendPage';
import { ExtensionBuilderPage } from '../pages/ExtensionBuilderPage';

const TEST_EXT_KEY = 'playwright_wires_test';
const AUTH_FILE = path.resolve(__dirname, '../.auth/user.json');
// packages/ directory sits 4 levels above specs/
const PACKAGES_DIR = path.resolve(__dirname, '../../../../');

/** Minimal ExtensionBuilder.json with 2 modules connected by a wire. */
const FIXTURE: object = {
  modules: [
    {
      config: { position: [20, 20] },
      value: {
        name: 'Foo',
        objectsettings: {
          uid: '', type: 'Entity', aggregateRoot: true,
          addDeletedField: false, addHiddenField: false,
          addStarttimeEndtimeFields: false, categorizable: false,
        },
        actionGroup: { _default1_list: true },
        propertyGroup: { properties: [] },
        relationGroup: {
          relations: [
            {
              uid: '', relationName: 'bars', relationType: 'zeroToMany',
              renderType: 'selectMultipleSideBySide',
              relationDescription: '', propertyIsExcludeField: true,
              lazyLoading: false, foreignRelationClass: '',
            },
          ],
        },
      },
    },
    {
      config: { position: [420, 20] },
      value: {
        name: 'Bar',
        objectsettings: {
          uid: '', type: 'Entity', aggregateRoot: false,
          addDeletedField: false, addHiddenField: false,
          addStarttimeEndtimeFields: false, categorizable: false,
        },
        actionGroup: {},
        propertyGroup: { properties: [] },
        relationGroup: { relations: [] },
      },
    },
  ],
  wires: [
    {
      src: { moduleId: 0, terminal: 'REL_0', uid: '' },
      tgt: { moduleId: 1, terminal: 'SOURCES', uid: '' },
    },
  ],
  properties: {
    name: 'Playwright Wires Test',
    vendorName: 'TestVendor',
    extensionKey: TEST_EXT_KEY,
    originalExtensionKey: TEST_EXT_KEY,
    originalVendorName: 'TestVendor',
    description: '',
    emConf: {
      category: 'plugin', version: '1.0.0', state: 'alpha',
      disableVersioning: false, disableLocalization: false,
      generateDocumentationTemplate: false, generateEmptyGitRepository: false,
      generateEditorConfig: false, sourceLanguage: 'en',
      targetVersion: '12.4.0-12.4.99', dependsOn: 'typo3 => 12.4.0-12.4.99\n',
    },
  },
};

/** Performs the load-dialog UI interactions to open the given extension. */
async function openExtensionViaUI(page: any, frame: any, extKey: string): Promise<void> {
  await frame.locator('#WiringEditor-loadButton-button').click();
  const modal = page.locator('.t3js-modal');
  await expect(modal).toBeVisible();
  await modal.locator('select').selectOption(extKey);
  await modal.locator('.t3js-modal-footer .btn-primary').click();
}

test.describe('Wire loading on extension open', () => {
  test.use({ storageState: AUTH_FILE });

  test.beforeAll(() => {
    // Write the test extension fixture directly to the packages directory.
    // This bypasses UI extension generation and gives us a controlled state
    // with exactly 2 modules and 1 wire to test with.
    const extDir = path.join(PACKAGES_DIR, TEST_EXT_KEY);
    fs.mkdirSync(extDir, { recursive: true });
    fs.writeFileSync(
      path.join(extDir, 'ExtensionBuilder.json'),
      JSON.stringify(FIXTURE, null, 2),
      'utf8',
    );
    // Flush Mutagen so the ddev container sees the new file before any test
    // calls listWirings. Without this, the async Mutagen sync can race the test.
    try {
      execFileSync('ddev', ['mutagen', 'sync'], { stdio: 'ignore', timeout: 15000 });
    } catch {
      // Mutagen sync unavailable or already up-to-date — continue anyway
    }
  });

  test.afterAll(() => {
    fs.rmSync(path.join(PACKAGES_DIR, TEST_EXT_KEY), { recursive: true, force: true });
  });

  test.beforeEach(async ({ page }) => {
    const backend = new BackendPage(page);
    await backend.navigateToModule('Extension Builder');
    const extBuilder = new ExtensionBuilderPage(backend.getContentFrame());
    await extBuilder.waitForLoaded();
    await extBuilder.goToDomainModeller();
  });

  // EBUILDER-233: Wires must be visible immediately after loading an extension —
  // not only after the user drags a container (which previously triggered a
  // re-calculation of wire positions).
  test('wires have non-zero positions immediately after opening a saved extension', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    await openExtensionViaUI(page, frame, TEST_EXT_KEY);

    // Wait for the editor to finish loading
    const wireData = await frame.locator('eb-wiring-editor').evaluate(async (el: any) => {
      if (el._loading) {
        await new Promise<void>((resolve) => {
          const check = setInterval(() => {
            if (!el._loading) { clearInterval(check); resolve(); }
          }, 100);
        });
      }
      const layer = el.shadowRoot?.querySelector('eb-layer') as any;
      if (!layer) return [];
      // Wait for eb-layer and all its eb-container children to finish rendering
      await layer.updateComplete;
      const containers = Array.from(layer.shadowRoot?.querySelectorAll('eb-container') ?? []) as any[];
      await Promise.all(containers.map((c: any) => c.updateComplete));

      // addWires() is async internally; poll until _wires is populated
      await new Promise<void>((resolve) => {
        if ((layer._wires?.length ?? 0) > 0) { resolve(); return; }
        const check = setInterval(() => {
          if ((layer._wires?.length ?? 0) > 0) { clearInterval(check); resolve(); }
        }, 100);
        setTimeout(() => { clearInterval(check); resolve(); }, 5000);
      });

      return layer._wires ?? [];
    });

    // The fixture has exactly one wire
    expect(wireData.length).toBeGreaterThan(0);

    // Before the fix, all wires had coordinates 0,0,0,0 because the terminal
    // elements were not yet rendered when addWires() ran. After the fix,
    // at least one coordinate must be non-zero.
    const wire = wireData[0];
    const hasNonZeroPosition = wire.x1 !== 0 || wire.y1 !== 0 || wire.x2 !== 0 || wire.y2 !== 0;
    expect(hasNonZeroPosition).toBe(true);
  });

  test('loaded extension has correct relation data on Foo', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    await openExtensionViaUI(page, frame, TEST_EXT_KEY);

    const relations = await frame.locator('eb-wiring-editor').evaluate(async (el: any) => {
      if (el._loading) {
        await new Promise<void>((resolve) => {
          const check = setInterval(() => {
            if (!el._loading) { clearInterval(check); resolve(); }
          }, 100);
          setTimeout(() => { clearInterval(check); resolve(); }, 5000);
        });
      }
      const layer = el.shadowRoot?.querySelector('eb-layer') as any;
      if (!layer) return null;
      await layer.updateComplete;
      const containers = Array.from(layer.shadowRoot?.querySelectorAll('eb-container') ?? []) as any[];
      await Promise.all(containers.map((c: any) => c.updateComplete));
      return layer.serialize()?.modules?.[0]?.value?.relationGroup?.relations ?? null;
    });

    expect(relations).toHaveLength(1);
    expect(relations[0]).toMatchObject({
      relationName: 'bars',
      relationType: 'zeroToMany',
    });
  });

  test('wire in loaded extension connects Foo and Bar (different module IDs)', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    await openExtensionViaUI(page, frame, TEST_EXT_KEY);

    const wires = await frame.locator('eb-wiring-editor').evaluate(async (el: any) => {
      if (el._loading) {
        await new Promise<void>((resolve) => {
          const check = setInterval(() => {
            if (!el._loading) { clearInterval(check); resolve(); }
          }, 100);
          setTimeout(() => { clearInterval(check); resolve(); }, 5000);
        });
      }
      const layer = el.shadowRoot?.querySelector('eb-layer') as any;
      if (!layer) return null;
      await layer.updateComplete;
      const containers = Array.from(layer.shadowRoot?.querySelectorAll('eb-container') ?? []) as any[];
      await Promise.all(containers.map((c: any) => c.updateComplete));
      return layer.serialize()?.wires ?? null;
    });

    expect(wires).toHaveLength(1);
    // When the fixture's REL_0 terminal is loaded and re-serialized, the Lit
    // component maps it to the actual DOM terminal-id 'relationWire_0'.
    expect(wires[0]).toMatchObject({
      src: { terminal: 'relationWire_0' },
      tgt: { terminal: 'SOURCES' },
    });
    expect(wires[0].src.moduleId).not.toBe(wires[0].tgt.moduleId);
  });

  // GitHub #634: Deleting a relation must not cause a save error.
  // The orphaned wire must be filtered out of serialize() so PHP never sees it.
  test('deleting a relation removes its wire and save succeeds', async ({ page }) => {
    const frame = new BackendPage(page).getContentFrame();
    await openExtensionViaUI(page, frame, TEST_EXT_KEY);

    // Wait for load, then delete the relation via the list-field delete button.
    const wireCountAfterDelete = await frame.locator('eb-wiring-editor').evaluate(async (el: any) => {
      if (el._loading) {
        await new Promise<void>((resolve) => {
          const check = setInterval(() => {
            if (!el._loading) { clearInterval(check); resolve(); }
          }, 100);
          setTimeout(() => { clearInterval(check); resolve(); }, 5000);
        });
      }
      const layer = el.shadowRoot?.querySelector('eb-layer') as any;
      if (!layer) return -1;
      await layer.updateComplete;

      // Wait for wires to be populated by addWires()
      await new Promise<void>((resolve) => {
        if ((layer._wires?.length ?? 0) > 0) { resolve(); return; }
        const check = setInterval(() => {
          if ((layer._wires?.length ?? 0) > 0) { clearInterval(check); resolve(); }
        }, 100);
        setTimeout(() => { clearInterval(check); resolve(); }, 5000);
      });

      // Find the Foo container (module-id=0) and click the relation delete button.
      const containers = Array.from(layer.shadowRoot?.querySelectorAll('eb-container') ?? []) as any[];
      let deleted = false;
      for (const c of containers) {
        await c.updateComplete;
        // Deep-search for the list-field delete button (first relation item).
        const findDeleteBtn = (root: any): HTMLElement | null => {
          if (!root?.shadowRoot) return null;
          const btn = root.shadowRoot.querySelector('.btn-delete');
          if (btn) return btn;
          for (const child of root.shadowRoot.querySelectorAll('*')) {
            const found = findDeleteBtn(child);
            if (found) return found;
          }
          return null;
        };
        const deleteBtn = findDeleteBtn(c);
        if (deleteBtn) {
          (deleteBtn as HTMLElement).click();
          deleted = true;
          break;
        }
      }
      if (!deleted) return -2;

      // Allow Lit to re-render after the deletion.
      await layer.updateComplete;
      await Promise.all((Array.from(layer.shadowRoot?.querySelectorAll('eb-container') ?? []) as any[]).map((c: any) => c.updateComplete));

      return layer.serialize()?.wires?.length ?? -3;
    });

    // After deleting the only relation, the wire must be gone from serialize().
    expect(wireCountAfterDelete).toBe(0);

    // Save and expect no error — the orphaned wire must not reach PHP.
    await frame.locator('#WiringEditor-saveButton-button').click();

    // The extension has no existing files to roundtrip against, so save should
    // succeed immediately (no "Save anyway" modal needed for a fresh extension).
    const extBuilder = new ExtensionBuilderPage(frame, page);
    await expect(extBuilder.getSuccessMessage()).toBeVisible({ timeout: 15000 });
  });
});
