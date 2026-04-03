import { FrameLocator, Locator, Page } from '@playwright/test';

export class ExtensionBuilderPage {
  constructor(
    private readonly frame: FrameLocator,
    private readonly page?: Page
  ) {}

  async goToDomainModeller(): Promise<void> {
    const btn = this.frame.getByRole('link', { name: /go to (the )?domain modell/i });
    if (await btn.isVisible()) {
      await btn.click();
      // Link navigates the parent page; wait for the new page's iframe to load
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
    // Fields live inside eb-wiring-editor's shadow DOM — standard locators cannot pierce it.
    // Use evaluate() to reach shadow root fields and call their setValue() API.
    await this.frame.locator('eb-wiring-editor').evaluate(
      (el: any, args: { name: string; key: string; vendor: string }) => {
        el.extensionName = args.key;
        el.shadowRoot?.querySelectorAll('[name]').forEach((field: any) => {
          if (field.name === 'name') field.setValue?.(args.name);
          if (field.name === 'extensionKey') field.setValue?.(args.key);
          if (field.name === 'vendorName') field.setValue?.(args.vendor);
        });
      },
      { name, key, vendor }
    );
  }

  async generateExtension(): Promise<void> {
    await this.frame.locator('#WiringEditor-saveButton-button').click();
  }

  getSuccessMessage(): Locator {
    // TYPO3 v13 notification.js uses top.TYPO3.Notification when available,
    // rendering the #alert-container in the outer backend page, not the iframe.
    if (this.page) {
      return this.page.locator('#alert-container .alert-success').first();
    }
    return this.frame.locator('#alert-container .alert-success').first();
  }
}
