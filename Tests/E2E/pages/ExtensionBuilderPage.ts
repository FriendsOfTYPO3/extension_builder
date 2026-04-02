import { FrameLocator, Locator } from '@playwright/test';

export class ExtensionBuilderPage {
  constructor(private readonly frame: FrameLocator) {}

  async goToDomainModeller(): Promise<void> {
    const btn = this.frame.getByRole('link', { name: /go to (the )?domain modell/i });
    if (await btn.isVisible()) {
      await btn.click();
      // Link navigates the parent page; wait for the new page's iframe to load YUI
      await this.frame.locator('#WiringEditor-saveButton-button').waitFor({ state: 'visible', timeout: 15000 });
    }
  }

  async waitForLoaded(): Promise<void> {
    await this.frame.locator('body').waitFor({ state: 'visible' });
  }

  getModuleTitle(): Locator {
    return this.frame.locator('h1').first();
  }

  async openNewExtension(): Promise<void> {
    await this.frame.locator('#WiringEditor-newButton-button').click();
  }

  async fillExtensionProperties(name: string, key: string, vendor: string): Promise<void> {
    await this.frame.locator('[name="name"]').fill(name);
    await this.frame.locator('[name="extensionKey"]').fill(key);
    await this.frame.locator('[name="vendorName"]').fill(vendor);
  }

  async generateExtension(): Promise<void> {
    await this.frame.locator('#WiringEditor-saveButton-button').click();
  }

  getSuccessMessage(): Locator {
    // YUI message box shown after save
    return this.frame.locator('#wireEditorMessageBox, .typo3-message-ok, .alert-success').first();
  }
}
