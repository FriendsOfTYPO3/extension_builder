import './eb-container.js';

// Minimal moduleData matching the shape eb-container reads from moduleData.value
function makeModuleData(name, overrides = {}) {
    return {
        value: {
            name,
            objectsettings: {
                uid: String(globalThis.crypto.getRandomValues(new Uint32Array(1))[0] % 1000),
                type: 'Entity',
                aggregateRoot: false,
                addDeletedField: true,
                addHiddenField: true,
                addStarttimeEndtimeField: false,
                addCategorization: false,
                addRecordType: false,
            },
            propertyGroup: { properties: [] },
            actionGroup: { customActions: [], actions: [] },
            ...overrides,
        },
    };
}

export default {
    title: 'Canvas/Container',
    component: 'eb-container',
    tags: ['autodocs'],
    parameters: {
        layout: 'padded',
        docs: {
            description: {
                component:
                    'Draggable domain model object card in the wiring canvas. Renders a colour-coded header with an `eb-terminal` output port and an inline-editable name, plus an auto-generated form body driven by the `modelObjectModule` config. Fires `container-moved`, `container-removed`, and `container-resized` events.',
            },
        },
    },
};

export const Default = {
    name: 'Domain model card',
    render: () => {
        const wrapper = document.createElement('div');
        wrapper.style.position = 'relative';
        wrapper.style.width = '360px';
        wrapper.style.height = '600px';

        const el = document.createElement('eb-container');
        el.setAttribute('module-id', '1');
        el.setAttribute('pos-x', '20');
        el.setAttribute('pos-y', '20');
        el.moduleData = makeModuleData('BlogPost');

        wrapper.appendChild(el);
        return wrapper;
    },
};

export const ValueObject = {
    name: 'Value Object card',
    render: () => {
        const wrapper = document.createElement('div');
        wrapper.style.position = 'relative';
        wrapper.style.width = '360px';
        wrapper.style.height = '600px';

        const el = document.createElement('eb-container');
        el.setAttribute('module-id', '2');
        el.setAttribute('pos-x', '20');
        el.setAttribute('pos-y', '20');
        el.moduleData = makeModuleData('Tag', {
            objectsettings: {
                uid: '99',
                type: 'ValueObject',
                aggregateRoot: false,
                addDeletedField: false,
                addHiddenField: false,
                addStarttimeEndtimeField: false,
                addCategorization: false,
                addRecordType: false,
            },
        });

        wrapper.appendChild(el);
        return wrapper;
    },
};
