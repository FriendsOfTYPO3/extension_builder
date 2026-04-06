import * as fs from 'fs';
import * as path from 'path';
import type { Page } from '@playwright/test';

const SCREENSHOTS_DIR = path.resolve(__dirname, '../../../.playwright-screenshots');

/**
 * Creates a step-numbered screenshotter for a test suite.
 * Screenshots are saved to .playwright-screenshots/{suiteName}/001-label.png, 002-..., etc.
 * The numeric prefix guarantees files sort in execution order.
 */
export function createScreenshotter(page: Page, suiteName: string) {
  const safe = (s: string) =>
    s.replace(/[^a-z0-9-]/gi, '_').replace(/_+/g, '_').replace(/^_|_$/g, '').slice(0, 80);

  const dir = path.join(SCREENSHOTS_DIR, safe(suiteName));
  fs.mkdirSync(dir, { recursive: true });
  let step = 0;

  return async (label: string): Promise<void> => {
    step++;
    const filename = `${String(step).padStart(3, '0')}-${safe(label)}.png`;
    await page.screenshot({ path: path.join(dir, filename), fullPage: true });
  };
}
