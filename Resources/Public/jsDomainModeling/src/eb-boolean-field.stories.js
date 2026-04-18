import './eb-boolean-field.js';

export default {
    title: 'Form Fields/BooleanField',
    component: 'eb-boolean-field',
    tags: ['autodocs'],
    argTypes: {
        label: { control: 'text' },
        value: { control: 'boolean' },
        description: { control: 'text' },
    },
};

const render = (args) => {
    const el = document.createElement('eb-boolean-field');
    if (args.label) {
        el.setAttribute('label', args.label);
    }
    if (args.description) {
        el.setAttribute('description', args.description);
    }
    el.value = Boolean(args.value);
    return el;
};

export const Default = {
    args: { label: 'Is Required', value: false },
    render,
};

export const Checked = {
    args: { label: 'Nullable', value: true },
    render,
};

export const WithDescription = {
    args: {
        label: 'Exclude from AJAX save',
        value: false,
        description: 'When enabled, this field is excluded from AJAX save operations',
    },
    render,
};
