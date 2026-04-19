import js from '@eslint/js';
import globals from 'globals';

export default [
    js.configs.recommended,
    {
        languageOptions: {
            ecmaVersion: 2022,
            sourceType: 'module',
            globals: {
                ...globals.browser,
                YAHOO: 'readonly',
                TYPO3: 'readonly',
                extbaseModeling_wiringEditorLanguage: 'readonly',
            },
        },
        rules: {
            'no-unused-vars': 'warn',
            'no-console': 'warn',
            eqeqeq: ['error', 'always'],
            curly: ['error', 'all'],
        },
    },
    {
        ignores: [
            '.Build/',
            'node_modules/',
            'Resources/Public/jsDomainModeling/wireit/',
        ],
    },
];
