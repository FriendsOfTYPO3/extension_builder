import { test as setup } from '@playwright/test';
import path from 'path';

const authFile = path.join(__dirname, '.auth/user.json');

setup('authenticate', async ({ page }) => {
  await page.goto('/typo3');
  await page.getByLabel('Username').fill(process.env.TYPO3_ADMIN_USER ?? 'admin');
  await page.getByRole('textbox', { name: 'Password' }).fill(process.env.TYPO3_ADMIN_PASS ?? 'Admin2026!');
  await page.getByRole('button', { name: 'Login' }).click();
  await page.waitForURL('**/typo3/main**');
  await page.context().storageState({ path: authFile });
});
