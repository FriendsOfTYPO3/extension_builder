import { test, expect } from '@playwright/test';

test.describe('TYPO3 Backend Login', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('login page is reachable', async ({ page }) => {
    await page.goto('/typo3');
    await expect(page).toHaveTitle(/TYPO3/);
    await expect(page.getByLabel('Username')).toBeVisible();
    await expect(page.getByRole('textbox', { name: 'Password' })).toBeVisible();
  });

  test('valid credentials redirect to backend', async ({ page }) => {
    const user = process.env.TYPO3_ADMIN_USER ?? 'admin';
    const pass = process.env.TYPO3_ADMIN_PASS ?? 'admin';
    await page.goto('/typo3');
    await page.getByLabel('Username').fill(user);
    await page.getByRole('textbox', { name: 'Password' }).fill(pass);
    await page.getByRole('button', { name: 'Login' }).click();
    await expect(page).toHaveURL(/typo3\/main/);
  });

  test('invalid credentials show error', async ({ page }) => {
    await page.goto('/typo3');
    await page.getByLabel('Username').fill('invalid_user');
    await page.getByRole('textbox', { name: 'Password' }).fill('wrong_pass');
    await page.getByRole('button', { name: 'Login' }).click();
    await expect(page.locator('.alert-danger, .typo3-message-error')).toBeVisible();
  });
});
