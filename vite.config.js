import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
    build: {
        lib: {
            entry: resolve(import.meta.dirname, 'Resources/Public/jsDomainModeling/src/main.js'),
            formats: ['es'],
            fileName: () => 'extension-builder.js',
        },
        outDir: 'Resources/Public/JavaScript',
        emptyOutDir: true,
        rollupOptions: {
            external: [/@typo3\/.*/],
            output: {
                assetFileNames: (assetInfo) => assetInfo.name?.endsWith('.css') ? 'extension-builder.css' : assetInfo.name ?? '[name][extname]',
            },
        },
    },
});
