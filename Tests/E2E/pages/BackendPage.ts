import { Page, FrameLocator } from '@playwright/test';

// Module routes in TYPO3 v12: /typo3/module/{parent}/{name}
// Content is rendered inside #typo3-contentIframe (renamed from #typo3-content-frame in v11)
const MODULE_URLS: Record<string, string> = {
  'Extension Builder': '/typo3/module/tools/extensionbuilder',
};

export class BackendPage {
  constructor(readonly page: Page) {}

  async navigateToModule(moduleName: string): Promise<void> {
    const url = MODULE_URLS[moduleName];
    if (!url) throw new Error(`Unknown module: ${moduleName}`);
    await this.page.goto(url);
    await this.page.waitForLoadState('networkidle');
  }

  getContentFrame(): FrameLocator {
    return this.page.frameLocator('#typo3-contentIframe');
  }
}
