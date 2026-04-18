import './eb-textarea-field.js';

export default {
    title: 'Form Fields/TextareaField',
    component: 'eb-textarea-field',
    tags: ['autodocs'],
    argTypes: {
        label: { control: 'text' },
        value: { control: 'text' },
        rows: { control: 'number' },
        description: { control: 'text' },
    },
};

const render = (args) => {
    const el = document.createElement('eb-textarea-field');
    if (args.label) {
        el.setAttribute('label', args.label);
    }
    if (args.description) {
        el.setAttribute('description', args.description);
    }
    if (args.rows) {
        el.setAttribute('rows', args.rows);
    }
    el.value = args.value ?? '';
    return el;
};

export const Default = {
    args: { label: 'Description', value: '', rows: 4 },
    render,
};

export const WithContent = {
    args: {
        label: 'Extension Description',
        value: 'This extension provides a custom domain model for managing astrophotography data including telescopes, cameras, and observation sessions.',
        rows: 4,
    },
    render,
};

export const WithDescription = {
    args: {
        label: 'Notes',
        description: 'Additional notes visible in the backend list view',
        value: '',
        rows: 3,
    },
    render,
};
