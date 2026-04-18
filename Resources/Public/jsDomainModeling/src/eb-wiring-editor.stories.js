import './eb-wiring-editor.js';

export default {
    title: 'Canvas/WiringEditor',
    component: 'eb-wiring-editor',
    tags: ['autodocs'],
    parameters: {
        layout: 'fullscreen',
        docs: {
            description: {
                component:
                    'Top-level wiring editor component. Orchestrates loading, saving, and resetting extension data via the SMD JSON-RPC endpoint. Renders the toolbar, a collapsible left panel with extension properties, and the central `eb-layer` canvas. In Storybook: TYPO3 backend APIs (Notification, Modal, Severity) are mocked. The SMD endpoint is not available, so load/save operations will fail gracefully.',
            },
        },
    },
    decorators: [
        // eb-wiring-editor is a flex column that needs a defined height on its
        // parent to expand correctly. This decorator stretches the story root to
        // the full viewport so the inner canvas fills the available space.
        (story) => {
            const wrapper = document.createElement('div');
            wrapper.style.cssText = 'position:fixed;inset:0;display:flex;flex-direction:column;';
            wrapper.appendChild(story());
            return wrapper;
        },
    ],
};

export const Standalone = {
    name: 'Editor (no backend)',
    render: () => {
        const el = document.createElement('eb-wiring-editor');
        // smd-url intentionally omitted — no backend available in Storybook.
        // The editor renders its full UI shell; load/save will fail gracefully.
        el.style.cssText = 'flex:1;min-height:0;width:100%;';
        return el;
    },
};
