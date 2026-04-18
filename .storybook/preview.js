// Mock window.TYPO3 for Storybook — translate() reads from
// window.TYPO3.settings.extensionBuilder._LOCAL_LANG at runtime.
// In Storybook there is no TYPO3 backend, so we provide an empty object;
// translate() falls back to generating labels from camelCase keys automatically.
window.TYPO3 = window.TYPO3 ?? {};
window.TYPO3.settings = window.TYPO3.settings ?? {};
window.TYPO3.settings.extensionBuilder = window.TYPO3.settings.extensionBuilder ?? {};
window.TYPO3.settings.extensionBuilder._LOCAL_LANG = {};

// Inject TYPO3 backend CSS custom properties so components look identical to
// their appearance inside the TYPO3 backend module. These are the same values
// that the TYPO3 Bootstrap theme sets on :root in the backend.
const style = document.createElement('style');
style.textContent = `
  :root {
    /* Bootstrap base */
    --bs-body-font-size: 0.875rem;
    --bs-body-color: #333;
    --bs-body-bg: #fff;
    --bs-border-color: #c3c3c3;
    --bs-border-radius: 0.25rem;
    --bs-border-radius-sm: 0.2rem;
    --bs-secondary-color: #6c757d;
    --bs-secondary-bg: #e9ecef;
    --bs-primary: #0d6efd;
    --bs-danger: #dc3545;
    --bs-warning: #ffc107;
    --bs-dark: #212529;
    --bs-box-shadow-sm: 2px 2px 6px rgba(0,0,0,.15);

    /* TYPO3 brand */
    --eb-brand-color: #ff8700;

    /* TYPO3 module docheader */
    --module-docheader-bg: #eee;
    --module-docheader-border: #c3c3c3;
    --module-docheader-padding-x: 24px;
    --module-docheader-padding-y: 5px;

    /* Terminal colors */
    --eb-terminal-default: #4a90d9;
    --eb-terminal-default-border: #2c5f8a;
    --eb-terminal-input: #5cb85c;
    --eb-terminal-input-border: #3d7a3d;
    --eb-terminal-output: #d9534f;
    --eb-terminal-output-border: #8a2c2c;
    --eb-terminal-relation: #6ea8fe;
    --eb-terminal-relation-border: #3d7bd4;

    /* Wire color */
    --eb-wire-color: #4a90d9;
  }

  /* Padding for story canvas */
  .sb-show-main.sb-main-centered #storybook-root {
    padding: 1.5rem;
  }
`;
document.head.appendChild(style);

/** @type { import('@storybook/web-components').Preview } */
const preview = {
    parameters: {
        controls: {
            matchers: {
                color: /(background|color)$/i,
                date: /Date$/i,
            },
        },
        layout: 'centered',
    },
};

export default preview;
