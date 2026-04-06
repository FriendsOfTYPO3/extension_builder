import { test as setup } from '@playwright/test';
import path from 'path';

const authFile = path.join(__dirname, '.auth/user.json');

setup('authenticate', async ({ page }) => {
  await page.goto('/typo3');
  await page.locator('#t3-username').fill(process.env.TYPO3_ADMIN_USER ?? 'admin');
  // Use pressSequentially so TYPO3's JS password-hash listeners fire on keyboard events
  await page.locator('#t3-password').pressSequentially(process.env.TYPO3_ADMIN_PASS ?? 'Admin2026!');
  // Give TYPO3's JS time to populate the userident hidden field before submit
  await page.waitForTimeout(300);
  await page.locator('#t3-login-submit').click();
  await page.waitForURL('**/typo3/main**', { timeout: 15000 });
  await page.context().storageState({ path: authFile });
});
