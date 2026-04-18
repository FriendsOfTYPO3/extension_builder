// Mock window.TYPO3 for Storybook — translate() reads from
// window.TYPO3.settings.extensionBuilder._LOCAL_LANG at runtime.
// In Storybook there is no TYPO3 backend, so we provide an empty object;
// translate() falls back to generating labels from camelCase keys automatically.
window.TYPO3 = window.TYPO3 ?? {};
window.TYPO3.settings = window.TYPO3.settings ?? {};
window.TYPO3.settings.extensionBuilder = window.TYPO3.settings.extensionBuilder ?? {};
window.TYPO3.settings.extensionBuilder._LOCAL_LANG = {};

/** @type { import('@storybook/web-components').Preview } */
const preview = {
    parameters: {
        controls: {
            matchers: {
                color: /(background|color)$/i,
                date: /Date$/i,
            },
        },
    },
};

export default preview;
