import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
    build: {
        lib: {
            entry: resolve(__dirname, 'Resources/Public/jsDomainModeling/src/main.js'),
            formats: ['es'],
            fileName: () => 'domain-modeling.js',
        },
        outDir: 'Resources/Public/JavaScript',
        emptyOutDir: true,
        rollupOptions: {
            external: [/@typo3\/.*/],
            output: {
                assetFileNames: (assetInfo) => assetInfo.name === 'style.css' ? 'domain-modeling.css' : assetInfo.name,
            },
        },
    },
});
