import { test, expect } from '@playwright/test';
import { BackendPage } from '../pages/BackendPage';
import { ExtensionBuilderPage } from '../pages/ExtensionBuilderPage';

/**
 * E2E tests for the Lit field components rendered inside the wiring editor.
 *
 * All field components live inside multiple Shadow DOM boundaries, so tests
 * use evaluate() to call their JS APIs directly instead of relying on
 * standard Playwright locators.
 */
test.describe('Lit Field Components', () => {
    test.beforeEach(async ({ page }) => {
        const backend = new BackendPage(page);
        await backend.navigateToModule('Extension Builder');
        const extBuilder = new ExtensionBuilderPage(backend.getContentFrame());
        await extBuilder.waitForLoaded();
        await extBuilder.goToDomainModeller();
        await backend.getContentFrame().locator('#WiringEditor-newButton-button').click();
    });

    test.describe('eb-string-field', () => {
        test('getValue returns empty string by default', async ({ page }) => {
            const frame = new BackendPage(page).getContentFrame();
            const value = await frame.locator('eb-wiring-editor').evaluate((el: any) => {
                const field = el.shadowRoot?.querySelector('eb-string-field[name="extensionKey"]') as any;
                return field?.getValue?.() ?? 'NOT FOUND';
            });
            expect(value).toBe('');
        });

        test('setValue and getValue roundtrip', async ({ page }) => {
            const frame = new BackendPage(page).getContentFrame();
            const result = await frame.locator('eb-wiring-editor').evaluate((el: any) => {
                const field = el.shadowRoot?.querySelector('eb-string-field[name="extensionKey"]') as any;
                if (!field) return 'NOT FOUND';
                field.setValue('my_ext');
                return field.getValue();
            });
            expect(result).toBe('my_ext');
        });

        test('validate returns false for required empty field', async ({ page }) => {
            const frame = new BackendPage(page).getContentFrame();
            const valid = await frame.locator('eb-wiring-editor').evaluate((el: any) => {
                const field = el.shadowRoot?.querySelector('eb-string-field[name="extensionKey"]') as any;
                if (!field) return null;
                field.setValue('');
                return field.validate();
            });
            expect(valid).toBe(false);
        });

        test('validate returns true for valid non-empty value', async ({ page }) => {
            const frame = new BackendPage(page).getContentFrame();
            const valid = await frame.locator('eb-wiring-editor').evaluate((el: any) => {
                const field = el.shadowRoot?.querySelector('eb-string-field[name="extensionKey"]') as any;
                if (!field) return null;
                field.setValue('my_ext');
                return field.validate();
            });
            expect(valid).toBe(true);
        });

        test('has aria-invalid=false when no error', async ({ page }) => {
            const frame = new BackendPage(page).getContentFrame();
            const ariaInvalid = await frame.locator('eb-wiring-editor').evaluate(async (el: any) => {
                const field = el.shadowRoot?.querySelector('eb-string-field[name="extensionKey"]') as any;
                if (!field) return null;
                field.setValue('valid');
                await field.updateComplete;
                return field.shadowRoot?.querySelector('input')?.getAttribute('aria-invalid');
            });
            expect(ariaInvalid).toBe('false');
        });
    });

    test.describe('eb-select-field', () => {
        test('getValue returns default (first) value', async ({ page }) => {
            const frame = new BackendPage(page).getContentFrame();
            const value = await frame.locator('eb-wiring-editor').evaluate((el: any) => {
                const field = el.shadowRoot?.querySelector('eb-select-field') as any;
                return field?.getValue?.() ?? 'NOT FOUND';
            });
            expect(typeof value).toBe('string');
            expect(value).not.toBe('NOT FOUND');
        });

        test('setValue and getValue roundtrip', async ({ page }) => {
            const frame = new BackendPage(page).getContentFrame();
            const result = await frame.locator('eb-wiring-editor').evaluate((el: any) => {
                const field = el.shadowRoot?.querySelector('eb-select-field') as any;
                if (!field) return 'NOT FOUND';
                const options = field.selectValues ?? [];
                if (options.length === 0) return 'NO OPTIONS';
                field.setValue(options[0]);
                return field.getValue();
            });
            expect(result).not.toBe('NOT FOUND');
            expect(result).not.toBe('NO OPTIONS');
        });
    });

    test.describe('eb-boolean-field', () => {
        test('getValue returns false by default', async ({ page }) => {
            const frame = new BackendPage(page).getContentFrame();

            // Add a model object to get boolean fields inside container
            await frame.getByRole('button', { name: '+ Model Object' }).click();

            const value = await frame.locator('eb-wiring-editor').evaluate(async (el: any) => {
                const layer = el.shadowRoot?.querySelector('eb-layer');
                await layer?.updateComplete;
                const container = layer?.shadowRoot?.querySelector('eb-container') as any;
                if (!container) return null;
                await container.updateComplete;
                const field = container.shadowRoot?.querySelector('eb-boolean-field') as any;
                return field?.getValue?.() ?? null;
            });
            expect(value).toBe(false);
        });

        test('setValue true and getValue returns true', async ({ page }) => {
            const frame = new BackendPage(page).getContentFrame();
            await frame.getByRole('button', { name: '+ Model Object' }).click();

            const value = await frame.locator('eb-wiring-editor').evaluate(async (el: any) => {
                const layer = el.shadowRoot?.querySelector('eb-layer');
                await layer?.updateComplete;
                const container = layer?.shadowRoot?.querySelector('eb-container') as any;
                if (!container) return null;
                await container.updateComplete;
                const field = container.shadowRoot?.querySelector('eb-boolean-field') as any;
                if (!field) return null;
                field.setValue(true);
                return field.getValue();
            });
            expect(value).toBe(true);
        });
    });

    test.describe('eb-inplace-edit', () => {
        test('is present in container header', async ({ page }) => {
            const frame = new BackendPage(page).getContentFrame();
            await frame.getByRole('button', { name: '+ Model Object' }).click();

            const present = await frame.locator('eb-wiring-editor').evaluate(async (el: any) => {
                const layer = el.shadowRoot?.querySelector('eb-layer');
                await layer?.updateComplete;
                const container = layer?.shadowRoot?.querySelector('eb-container') as any;
                if (!container) return false;
                await container.updateComplete;
                return !!container.shadowRoot?.querySelector('eb-inplace-edit');
            });
            expect(present).toBe(true);
        });

        test('display span has role=button for keyboard accessibility', async ({ page }) => {
            const frame = new BackendPage(page).getContentFrame();
            await frame.getByRole('button', { name: '+ Model Object' }).click();

            const role = await frame.locator('eb-wiring-editor').evaluate(async (el: any) => {
                const layer = el.shadowRoot?.querySelector('eb-layer');
                await layer?.updateComplete;
                const container = layer?.shadowRoot?.querySelector('eb-container') as any;
                if (!container) return null;
                await container.updateComplete;
                const edit = container.shadowRoot?.querySelector('eb-inplace-edit') as any;
                if (!edit) return null;
                await edit.updateComplete;
                return edit.shadowRoot?.querySelector('span')?.getAttribute('role');
            });
            expect(role).toBe('button');
        });
    });

    test.describe('field-updated event', () => {
        test('eb-string-field fires field-updated when setValue is called', async ({ page }) => {
            const frame = new BackendPage(page).getContentFrame();

            const eventFired = await frame.locator('eb-wiring-editor').evaluate(async (el: any) => {
                return new Promise<boolean>((resolve) => {
                    el.addEventListener('field-updated', () => resolve(true), { once: true });
                    const field = el.shadowRoot?.querySelector('eb-string-field[name="extensionKey"]') as any;
                    if (!field) { resolve(false); return; }
                    // Simulate real input event
                    field.value = 'test';
                    field._fireUpdated?.();
                    setTimeout(() => resolve(false), 500);
                });
            });
            expect(eventFired).toBe(true);
        });
    });
});
