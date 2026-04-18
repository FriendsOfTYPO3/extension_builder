import './eb-string-field.js';

export default {
    title: 'Form Fields/StringField',
    component: 'eb-string-field',
    tags: ['autodocs'],
    argTypes: {
        value: { control: 'text' },
        label: { control: 'text' },
        required: { control: 'boolean' },
        placeholder: { control: 'text' },
        description: { control: 'text' },
        'force-lower-case': { control: 'boolean' },
        'uc-first': { control: 'boolean' },
        'force-alpha-numeric-underscore': { control: 'boolean' },
        'min-length': { control: 'number' },
        'max-length': { control: 'number' },
    },
};

const render = (args) => {
    const el = document.createElement('eb-string-field');
    Object.entries(args).forEach(([k, v]) => {
        if (v === true) {
            el.setAttribute(k, '');
        } else if (v !== false && v !== undefined && v !== '') {
            el.setAttribute(k, v);
        }
    });
    return el;
};

export const Default = {
    args: { label: 'Extension Name', value: 'my_extension', placeholder: 'my_extension' },
    render,
};

export const Required = {
    args: { label: 'Extension Key', required: true, 'force-lower-case': true, 'min-length': 3, 'max-length': 30 },
    render,
};

export const WithDescription = {
    args: {
        label: 'Vendor Name',
        description: 'Your PHP namespace vendor prefix',
        value: 'MyVendor',
        'uc-first': true,
    },
    render,
};

export const ForceAlphaNumericUnderscore = {
    args: {
        label: 'Table Name',
        'force-alpha-numeric-underscore': true,
        'force-lower-case': true,
        value: 'tx_myext_domain_model_item',
    },
    render,
};

export const ValidationError = {
    args: { label: 'Class Name', required: true, 'uc-first': true, value: '', 'min-length': 3 },
    render,
};
