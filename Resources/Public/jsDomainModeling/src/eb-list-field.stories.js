import './eb-list-field.js';
import './eb-group.js';
import './eb-string-field.js';
import './eb-boolean-field.js';
import './eb-select-field.js';
import './eb-hidden-field.js';

// Minimal element type definition matching the property list format used in
// the wiring editor. Each item gets a name field and a type selector.
const propertyElementType = JSON.stringify({
    inputParams: {
        fields: [
            {
                inputParams: { name: 'uid', className: 'hiddenField' },
            },
            {
                type: 'string',
                inputParams: {
                    name: 'propertyName',
                    label: 'Property Name',
                    required: true,
                    'lc-first': true,
                    'force-alpha-numeric-underscore': true,
                },
            },
            {
                type: 'select',
                inputParams: {
                    name: 'propertyType',
                    label: 'Type',
                    selectValues: ['string', 'integer', 'boolean', 'float', 'date', 'richText'],
                },
            },
            {
                type: 'boolean',
                inputParams: {
                    name: 'required',
                    label: 'Required',
                },
            },
        ],
    },
});

export default {
    title: 'Form Fields/ListField',
    component: 'eb-list-field',
    tags: ['autodocs'],
    parameters: {
        docs: {
            description: {
                component:
                    'Sortable list of repeatable field groups. Each item can be collapsed, reordered, and deleted. The `element-type` attribute accepts a JSON definition that drives the field set rendered per item.',
            },
        },
    },
};

export const Empty = {
    name: 'Empty list',
    render: () => {
        const el = document.createElement('eb-list-field');
        el.setAttribute('name', 'properties');
        el.setAttribute('add-label', '+ Add Property');
        el.setAttribute('element-type', propertyElementType);

        const wrapper = document.createElement('div');
        wrapper.style.width = '380px';
        wrapper.appendChild(el);
        return wrapper;
    },
};

export const WithItems = {
    name: 'Pre-populated list',
    render: () => {
        const el = document.createElement('eb-list-field');
        el.setAttribute('name', 'properties');
        el.setAttribute('add-label', '+ Add Property');
        el.setAttribute('element-type', propertyElementType);

        const wrapper = document.createElement('div');
        wrapper.style.width = '380px';
        wrapper.appendChild(el);

        // setValue after render cycle
        requestAnimationFrame(() => {
            el.setValue([
                { uid: '1', propertyName: 'title', propertyType: 'string', required: true },
                { uid: '2', propertyName: 'description', propertyType: 'richText', required: false },
            ]);
        });

        return wrapper;
    },
};
