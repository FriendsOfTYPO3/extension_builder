import './eb-terminal.js';

export default {
    title: 'Canvas/Terminal',
    component: 'eb-terminal',
    tags: ['autodocs'],
    parameters: {
        docs: {
            description: {
                component:
                    'Visual connection point (port) on a domain model container card. Clicking fires `terminal-connect` so `eb-layer` can draw a wire. The `type` attribute controls color and meaning: `input` (green) = accepts a relation, `output` (red) = exposes a relation. Droppable terminals (blue) are rendered inline in relation list items.',
            },
        },
    },
};

export const Input = {
    name: 'Input terminal (green)',
    render: () => {
        const wrapper = document.createElement('div');
        wrapper.style.position = 'relative';
        wrapper.style.width = '80px';
        wrapper.style.height = '30px';

        const el = document.createElement('eb-terminal');
        el.setAttribute('type', 'input');
        el.setAttribute('terminal-id', 'TERM_in');
        el.setAttribute('uid', '1');
        el.style.position = 'relative';
        el.style.display = 'inline-block';

        wrapper.appendChild(el);
        return wrapper;
    },
};

export const Output = {
    name: 'Output terminal (red)',
    render: () => {
        const wrapper = document.createElement('div');
        wrapper.style.position = 'relative';
        wrapper.style.width = '80px';
        wrapper.style.height = '30px';

        const el = document.createElement('eb-terminal');
        el.setAttribute('type', 'output');
        el.setAttribute('terminal-id', 'TERM_out');
        el.setAttribute('uid', '1');
        el.style.position = 'relative';
        el.style.display = 'inline-block';

        wrapper.appendChild(el);
        return wrapper;
    },
};

export const Droppable = {
    name: 'Droppable terminal (blue, inline in list)',
    render: () => {
        const wrapper = document.createElement('div');
        wrapper.style.display = 'flex';
        wrapper.style.alignItems = 'center';
        wrapper.style.gap = '8px';
        wrapper.style.padding = '8px';

        const el = document.createElement('eb-terminal');
        el.setAttribute('droppable', '');
        el.setAttribute('terminal-id', 'REL_0');
        el.setAttribute('uid', '42');

        const label = document.createElement('span');
        label.style.fontSize = '0.75rem';
        label.textContent = 'Drop relation here';

        wrapper.appendChild(el);
        wrapper.appendChild(label);
        return wrapper;
    },
};
