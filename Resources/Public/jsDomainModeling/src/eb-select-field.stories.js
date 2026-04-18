import './eb-select-field.js';

export default {
    title: 'Form Fields/SelectField',
    component: 'eb-select-field',
    tags: ['autodocs'],
    argTypes: {
        label: { control: 'text' },
        value: { control: 'text' },
        required: { control: 'boolean' },
        description: { control: 'text' },
    },
};

const propertyTypes = ['string', 'integer', 'boolean', 'float', 'date', 'dateTime', 'richText', 'image', 'file'];

const render = (args) => {
    const el = document.createElement('eb-select-field');
    if (args.label) {
        el.setAttribute('label', args.label);
    }
    if (args.description) {
        el.setAttribute('description', args.description);
    }
    if (args.required) {
        el.setAttribute('required', '');
    }
    el.selectValues = propertyTypes;
    el.value = args.value ?? propertyTypes[0];
    return el;
};

export const Default = {
    args: { label: 'Property Type', value: 'string' },
    render,
};

export const WithDescription = {
    args: { label: 'Relation Type', description: 'The type of relation between domain objects', value: 'integer' },
    render,
};

export const WithAllowedValues = {
    name: 'With Filtered Options',
    render: () => {
        const el = document.createElement('eb-select-field');
        el.setAttribute('label', 'Filtered Type (only scalar)');
        el.selectValues = propertyTypes;
        el.allowedValues = ['string', 'integer', 'boolean', 'float'];
        el.value = 'string';
        return el;
    },
};
