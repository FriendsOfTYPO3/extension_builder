import { test, expect } from '@playwright/test';
import * as fs from 'fs';
import * as path from 'path';
import { BackendPage } from '../pages/BackendPage';
import { ExtensionBuilderPage } from '../pages/ExtensionBuilderPage';

// Tests/E2E/specs/ -> up 4 levels -> packages/
const PACKAGES_DIR = path.resolve(__dirname, '../../../../');
// Tests/E2E/specs/ -> up 5 levels -> project root
const PROJECT_ROOT = path.resolve(__dirname, '../../../../../');
const AUTH_FILE = path.resolve(__dirname, '../.auth/user.json');

// Backup base directory as configured in config/system/settings.php
const BACKUP_BASE = path.join(PROJECT_ROOT, 'var/tx_extensionbuilder/backups');

function makeContext(browser: any) {
  return browser.newContext({
    storageState: AUTH_FILE,
    baseURL: 'https://extensionbuilder.ddev.site',
    ignoreHTTPSErrors: true,
  });
}

async function openDomainModeller(page: any): Promise<{ backend: BackendPage; frame: any; extBuilder: ExtensionBuilderPage }> {
  const backend = new BackendPage(page);
  await backend.navigateToModule('Extension Builder');
  const extBuilder = new ExtensionBuilderPage(backend.getContentFrame(), page);
  await extBuilder.waitForLoaded();
  await extBuilder.goToDomainModeller();
  return { backend, frame: backend.getContentFrame(), extBuilder };
}

/** Load an existing extension in the editor by key. */
async function loadExtension(frame: any, page: any, extKey: string): Promise<void> {
  await frame.locator('#WiringEditor-loadButton-button').click();
  const modal = page.locator('.t3js-modal');
  await expect(modal).toBeVisible({ timeout: 10000 });
  await modal.locator('select').selectOption(extKey);
  await modal.locator('.t3js-modal-footer .btn-primary').click();
  await frame.locator('eb-wiring-editor').evaluate(async (el: any) => {
    if (el._loading) {
      await new Promise<void>(resolve => {
        const check = setInterval(() => { if (!el._loading) { clearInterval(check); resolve(); } }, 100);
      });
    }
  });
}

/** Create a minimal extension via the UI. */
async function generateExtension(page: any, extKey: string): Promise<void> {
  const { frame, extBuilder } = await openDomainModeller(page);
  await extBuilder.openNewExtension();
  await extBuilder.fillExtensionProperties(extKey, extKey, 'BackupTest');
  await extBuilder.generateExtension();
  await expect(extBuilder.getSuccessMessage()).toBeVisible({ timeout: 15000 });
}

// ---------------------------------------------------------------------------
// EBUILDER-120: Backup-Restore UI
// ---------------------------------------------------------------------------
test.describe('Backup-Restore UI (EBUILDER-120)', () => {
  test.setTimeout(120_000);

  /**
   * The "Restore backup" button must be visible in the docheader toolbar.
   */
  test('backup button is visible in docheader', async ({ page }) => {
    const { frame } = await openDomainModeller(page);
    await expect(frame.locator('#WiringEditor-backupsButton-button')).toBeVisible();
  });

  /**
   * Clicking the backup button with no extension loaded should show
   * an info notification, not silently do nothing.
   */
  test('clicking backup button with no extension loaded shows notification', async ({ page }) => {
    const { frame } = await openDomainModeller(page);
    // Do NOT load any extension — click immediately
    await frame.locator('#WiringEditor-backupsButton-button').click();
    // TYPO3 v13 renders notifications in the outer page
    await expect(page.locator('#alert-container .alert-info')).toBeVisible({ timeout: 5000 });
  });

  /**
   * When an extension is loaded but no backups exist, the button click should
   * show an info notification ("No backups found…").
   */
  test('backup button shows "no backups" notification when extension has no backups', async ({ browser }) => {
    const extKey = 'eb_bkp_empty';
    fs.rmSync(path.join(PACKAGES_DIR, extKey), { recursive: true, force: true });
    fs.rmSync(path.join(BACKUP_BASE, extKey), { recursive: true, force: true });

    const context = await makeContext(browser);
    const page = await context.newPage();
    try {
      await generateExtension(page, extKey);

      // Navigate back and load the extension
      const { frame } = await openDomainModeller(page);
      await loadExtension(frame, page, extKey);

      // Remove any backups that the second save may have created
      fs.rmSync(path.join(BACKUP_BASE, extKey), { recursive: true, force: true });

      await frame.locator('#WiringEditor-backupsButton-button').click();
      await expect(page.locator('#alert-container .alert-info')).toBeVisible({ timeout: 5000 });
    } finally {
      await context.close();
      fs.rmSync(path.join(PACKAGES_DIR, extKey), { recursive: true, force: true });
      fs.rmSync(path.join(BACKUP_BASE, extKey), { recursive: true, force: true });
    }
  });

  /**
   * When backups exist for the loaded extension, clicking the backup button
   * should open a dialog listing the available backup entries.
   */
  test('backup dialog lists available backups', async ({ browser }) => {
    const extKey = 'eb_bkp_list';
    const backupEntry = '2026-04-01-1000000';
    const backupPath = path.join(BACKUP_BASE, extKey, backupEntry);

    fs.rmSync(path.join(PACKAGES_DIR, extKey), { recursive: true, force: true });
    fs.rmSync(path.join(BACKUP_BASE, extKey), { recursive: true, force: true });

    const context = await makeContext(browser);
    const page = await context.newPage();
    try {
      await generateExtension(page, extKey);

      // Create a fake backup directory directly on the host filesystem
      fs.mkdirSync(backupPath, { recursive: true });
      fs.writeFileSync(path.join(backupPath, 'ext_emconf.php'), '<?php // backup');

      const { frame } = await openDomainModeller(page);
      await loadExtension(frame, page, extKey);
      await frame.locator('#WiringEditor-backupsButton-button').click();

      // Backup selection modal should appear
      const modal = page.locator('.t3js-modal');
      await expect(modal).toBeVisible({ timeout: 10000 });
      await expect(modal.locator('.t3js-modal-title')).toContainText('Restore backup');
      await expect(modal.locator('select option')).toHaveCount(1);
      await expect(modal.locator('select option').first()).toHaveAttribute('value', backupEntry);
    } finally {
      await context.close();
      fs.rmSync(path.join(PACKAGES_DIR, extKey), { recursive: true, force: true });
      fs.rmSync(path.join(BACKUP_BASE, extKey), { recursive: true, force: true });
    }
  });

  /**
   * Full restore flow: select a backup, confirm, verify that the extension files
   * are overwritten with the backup content.
   */
  test('restore replaces extension files with backup content', async ({ browser }) => {
    const extKey = 'eb_bkp_restore';
    const backupEntry = '2026-04-01-2000000';
    const backupPath = path.join(BACKUP_BASE, extKey, backupEntry);
    const extDir = path.join(PACKAGES_DIR, extKey);
    const markerContent = '<?php // FROM_BACKUP_MARKER';

    fs.rmSync(extDir, { recursive: true, force: true });
    fs.rmSync(path.join(BACKUP_BASE, extKey), { recursive: true, force: true });

    const context = await makeContext(browser);
    const page = await context.newPage();
    try {
      await generateExtension(page, extKey);

      // Create a backup with a distinctive marker in ext_emconf.php
      fs.mkdirSync(backupPath, { recursive: true });
      fs.writeFileSync(path.join(backupPath, 'ext_emconf.php'), markerContent);

      const { frame } = await openDomainModeller(page);
      await loadExtension(frame, page, extKey);

      // Click "Restore backup"
      await frame.locator('#WiringEditor-backupsButton-button').click();

      // Backup modal: select entry and click Restore
      const backupModal = page.locator('.t3js-modal');
      await expect(backupModal).toBeVisible({ timeout: 10000 });
      await backupModal.locator('select').selectOption(backupEntry);
      await backupModal.locator('.t3js-modal-footer .btn-danger').click();

      // TYPO3 stacks a second confirmation modal on top; target the last btn-danger
      const confirmBtn = page.locator('.modal-footer button.btn-danger').last();
      await expect(confirmBtn).toBeVisible({ timeout: 10000 });
      await confirmBtn.click();

      // Success notification
      await expect(page.locator('#alert-container .alert-success')).toBeVisible({ timeout: 15000 });

      // The extension directory should now contain the backup content
      await page.waitForTimeout(1500); // brief FS sync
      const restored = fs.readFileSync(path.join(extDir, 'ext_emconf.php'), 'utf8');
      expect(restored).toContain('FROM_BACKUP_MARKER');
    } finally {
      await context.close();
      fs.rmSync(extDir, { recursive: true, force: true });
      fs.rmSync(path.join(BACKUP_BASE, extKey), { recursive: true, force: true });
    }
  });
});
