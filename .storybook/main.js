import path from 'path';

/** @type { import('@storybook/web-components-vite').StorybookConfig } */
const config = {
    stories: ['../Resources/Public/jsDomainModeling/src/**/*.stories.js'],
    framework: {
        name: '@storybook/web-components-vite',
        options: {},
    },
    async viteFinal(viteConfig) {
        const mocksDir = path.resolve(process.cwd(), '.storybook/mocks');

        viteConfig.resolve = viteConfig.resolve ?? {};
        // Use array form for Vite aliases — more reliable than object form
        // for scoped package paths like @typo3/backend/notification.js
        const existing = Array.isArray(viteConfig.resolve.alias) ? viteConfig.resolve.alias : [];
        viteConfig.resolve.alias = [
            ...existing,
            { find: '@typo3/backend/notification.js', replacement: `${mocksDir}/typo3-notification.js` },
            { find: '@typo3/backend/modal.js', replacement: `${mocksDir}/typo3-modal.js` },
            { find: '@typo3/backend/severity.js', replacement: `${mocksDir}/typo3-severity.js` },
        ];

        return viteConfig;
    },
};

export default config;
