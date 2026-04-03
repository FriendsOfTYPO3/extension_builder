import { test, expect } from '@playwright/test';
import * as fs from 'fs';
import * as path from 'path';
import { spawnSync } from 'child_process';
import { BackendPage } from '../pages/BackendPage';
import { ExtensionBuilderPage } from '../pages/ExtensionBuilderPage';

// Project root on the host — maps to /var/www/html/ inside the ddev container
const PROJECT_ROOT = path.resolve(__dirname, '../../../../../');

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

    // Poll until ext_emconf.php appears on the host filesystem (ddev volume mount sync)
    const emconfPath = path.join(EXT_BASE, 'ext_emconf.php');
    const deadline = Date.now() + 15000;
    while (!fs.existsSync(emconfPath) && Date.now() < deadline) {
      await page.waitForTimeout(500);
    }

    await context.close();
  });

  test('reference extension directory exists after generation', () => {
    expect(fs.existsSync(EXT_BASE)).toBe(true);
  });


  test.describe('File structure', () => {
    const file = (relative: string) => path.join(EXT_BASE, relative);

    test('composer.json exists', () => {
      expect(fs.existsSync(file('composer.json'))).toBe(true);
    });
    test('ext_emconf.php exists', () => {
      expect(fs.existsSync(file('ext_emconf.php'))).toBe(true);
    });
    test('ext_localconf.php exists', () => {
      expect(fs.existsSync(file('ext_localconf.php'))).toBe(true);
    });
    test('Article model class exists', () => {
      expect(fs.existsSync(file('Classes/Domain/Model/Article.php'))).toBe(true);
    });
    test('ArticleRepository class exists', () => {
      expect(fs.existsSync(file('Classes/Domain/Repository/ArticleRepository.php'))).toBe(true);
    });
    test('ArticleController class exists', () => {
      expect(fs.existsSync(file('Classes/Controller/ArticleController.php'))).toBe(true);
    });
    test('TCA definition for Article table exists', () => {
      expect(fs.existsSync(file('Configuration/TCA/tx_ebtestgenerated_domain_model_article.php'))).toBe(true);
    });
    test('TypoScript setup exists', () => {
      expect(fs.existsSync(file('Configuration/TypoScript/setup.typoscript'))).toBe(true);
    });
    test('TypoScript constants exists', () => {
      expect(fs.existsSync(file('Configuration/TypoScript/constants.typoscript'))).toBe(true);
    });
    test('Article List template exists', () => {
      expect(fs.existsSync(file('Resources/Private/Templates/Article/List.html'))).toBe(true);
    });
    test('Article Show template exists', () => {
      expect(fs.existsSync(file('Resources/Private/Templates/Article/Show.html'))).toBe(true);
    });
    test('locallang.xlf exists', () => {
      expect(fs.existsSync(file('Resources/Private/Language/locallang.xlf'))).toBe(true);
    });
  });

  /**
   * EBUILDER-96: Assert generated TCA uses v13-compatible field types.
   */
  test.describe('TCA compliance', () => {
    const tcaDir = path.join(EXT_BASE, 'Configuration/TCA');

    function readTcaFiles(): string {
      const files = fs.readdirSync(tcaDir).filter(f => f.endsWith('.php'));
      return files.map(f => fs.readFileSync(path.join(tcaDir, f), 'utf8')).join('\n');
    }

    test('image property uses type=file (not deprecated getFileFieldTCAConfig)', () => {
      const content = readTcaFiles();
      expect(content).toContain("'type' => 'file'");
      expect(content).not.toMatch(/getFileFieldTCAConfig\(/);
    });

    test('date property uses type=datetime (not deprecated eval=date)', () => {
      const content = readTcaFiles();
      expect(content).toContain("'type' => 'datetime'");
      expect(content).not.toMatch(/'eval'\s*=>\s*'[^']*\bdate\b/);
    });

    test('boolean property uses type=check (not deprecated type=input with eval)', () => {
      const content = readTcaFiles();
      expect(content).toContain("'type' => 'check'");
    });

    test('no deprecated eval=datetime in TCA', () => {
      const content = readTcaFiles();
      expect(content).not.toMatch(/'eval'\s*=>\s*'[^']*\bdatetime\b/);
    });
  });

  /**
   * EBUILDER-97: Assert generated plugin registration uses CType, not list_type.
   */
  test.describe('Plugin registration', () => {
    test('ext_localconf.php contains configurePlugin() call', () => {
      const content = fs.readFileSync(path.join(EXT_BASE, 'ext_localconf.php'), 'utf8');
      expect(content).toContain('ExtensionUtility::configurePlugin(');
    });

    test('no deprecated list_type in any generated file', () => {
      const extLocalconf = fs.readFileSync(path.join(EXT_BASE, 'ext_localconf.php'), 'utf8');
      expect(extLocalconf).not.toContain('list_type');

      const typoScriptSetup = fs.readFileSync(
        path.join(EXT_BASE, 'Configuration/TypoScript/setup.typoscript'),
        'utf8'
      );
      expect(typoScriptSetup).not.toContain('list_type');
      expect(typoScriptSetup).not.toContain('tt_content.list.20.');
    });
  });

  /**
   * EBUILDER-98: Assert generated controller uses current FlashMessage API.
   */
  test.describe('FlashMessage API', () => {
    test('controller uses $this->addFlashMessage() (not deprecated flashMessageContainer)', () => {
      const content = fs.readFileSync(
        path.join(EXT_BASE, 'Classes/Controller/ArticleController.php'),
        'utf8'
      );
      expect(content).toContain('$this->addFlashMessage(');
      expect(content).not.toContain('$this->flashMessageContainer');
      expect(content).not.toContain('new \\TYPO3\\CMS\\Core\\Messaging\\FlashMessage(');
    });
  });

  /**
   * EBUILDER-99: Assert no deprecated Extbase TypoScript persistence mapping.
   */
  test.describe('TypoScript persistence mapping', () => {
    test('no deprecated config.tx_extbase.persistence.classes in generated TypoScript', () => {
      const tsDir = path.join(EXT_BASE, 'Configuration/TypoScript');
      const files = fs.readdirSync(tsDir).filter(f => f.endsWith('.typoscript'));
      const content = files.map(f => fs.readFileSync(path.join(tsDir, f), 'utf8')).join('\n');
      expect(content).not.toContain('config.tx_extbase.persistence.classes');
      expect(content).not.toMatch(/plugin\.tx_\w+\.persistence\.classes/);
    });
  });

  /**
   * EBUILDER-95: PHP syntax check on every generated .php file.
   * Runs `ddev exec php -l <path>` for each file and asserts exit code 0.
   */
  test.describe('PHP syntax', () => {
    function collectPhpFiles(dir: string): string[] {
      const entries = fs.readdirSync(dir, { withFileTypes: true });
      const files: string[] = [];
      for (const entry of entries) {
        const fullPath = path.join(dir, entry.name);
        if (entry.isDirectory()) {
          files.push(...collectPhpFiles(fullPath));
        } else if (entry.name.endsWith('.php')) {
          files.push(fullPath);
        }
      }
      return files;
    }

    test('all generated PHP files have valid syntax', () => {
      const phpFiles = collectPhpFiles(EXT_BASE);
      expect(phpFiles.length).toBeGreaterThan(0);

      const errors: string[] = [];
      for (const hostPath of phpFiles) {
        const containerPath = '/var/www/html/' + path.relative(PROJECT_ROOT, hostPath);
        const result = spawnSync('ddev', ['exec', 'php', '-l', containerPath], {
          cwd: PROJECT_ROOT,
          encoding: 'utf8',
        });
        if (result.status !== 0) {
          errors.push(`${path.relative(EXT_BASE, hostPath)}: ${result.stderr?.trim() ?? result.stdout?.trim()}`);
        }
      }

      if (errors.length > 0) {
        throw new Error(`PHP syntax errors in generated files:\n${errors.join('\n')}`);
      }
    });
  });

  test.afterAll(() => {
    fs.rmSync(EXT_BASE, { recursive: true, force: true });
  });
});
