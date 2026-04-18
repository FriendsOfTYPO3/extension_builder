import './eb-layer.js';

export default {
    title: 'Canvas/Layer',
    component: 'eb-layer',
    tags: ['autodocs'],
    parameters: {
        layout: 'fullscreen',
        docs: {
            description: {
                component:
                    'Canvas layer that hosts draggable `eb-container` cards and SVG `eb-wire` overlays. Manages pan offset and wire drawing via `terminal-connect` events. Programmatically populated via `setModules()` and `setWires()` — use `eb-wiring-editor` for the full integrated editor.',
            },
        },
    },
};

export const EmptyCanvas = {
    name: 'Empty canvas',
    render: () => {
        const wrapper = document.createElement('div');
        wrapper.style.cssText = 'position:fixed;inset:0;display:flex;flex-direction:column;border:1px solid #dee2e6;';

        const el = document.createElement('eb-layer');
        el.style.cssText = 'flex:1;min-height:0;';

        wrapper.appendChild(el);
        return wrapper;
    },
};
