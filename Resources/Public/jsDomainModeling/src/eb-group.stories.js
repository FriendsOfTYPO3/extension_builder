import './eb-group.js';
import './eb-string-field.js';
import './eb-boolean-field.js';
import './eb-select-field.js';

export default {
    title: 'Layout/Group',
    component: 'eb-group',
    tags: ['autodocs'],
    argTypes: {
        legend: { control: 'text' },
        collapsible: { control: 'boolean' },
        collapsed: { control: 'boolean' },
    },
};

export const Default = {
    render: () => {
        const group = document.createElement('eb-group');
        group.setAttribute('legend', 'Object Settings');

        const nameField = document.createElement('eb-string-field');
        nameField.setAttribute('name', 'className');
        nameField.setAttribute('label', 'Class Name');
        nameField.setAttribute('uc-first', '');
        nameField.setAttribute('required', '');
        nameField.value = 'MyModel';

        const typeSelect = document.createElement('eb-select-field');
        typeSelect.setAttribute('name', 'type');
        typeSelect.setAttribute('label', 'Object Type');
        typeSelect.selectValues = ['Entity', 'ValueObject'];
        typeSelect.value = 'Entity';

        const aggregateRoot = document.createElement('eb-boolean-field');
        aggregateRoot.setAttribute('name', 'aggregateRoot');
        aggregateRoot.setAttribute('label', 'Aggregate Root');
        aggregateRoot.value = true;

        group.appendChild(nameField);
        group.appendChild(typeSelect);
        group.appendChild(aggregateRoot);

        const wrapper = document.createElement('div');
        wrapper.style.width = '320px';
        wrapper.appendChild(group);
        return wrapper;
    },
};

export const Collapsible = {
    render: () => {
        const group = document.createElement('eb-group');
        group.setAttribute('legend', 'Advanced Settings');
        group.setAttribute('collapsible', '');

        const field1 = document.createElement('eb-boolean-field');
        field1.setAttribute('name', 'addDeletedField');
        field1.setAttribute('label', 'Add "deleted" field');
        field1.value = false;

        const field2 = document.createElement('eb-boolean-field');
        field2.setAttribute('name', 'addHiddenField');
        field2.setAttribute('label', 'Add "hidden" field');
        field2.value = true;

        group.appendChild(field1);
        group.appendChild(field2);

        const wrapper = document.createElement('div');
        wrapper.style.width = '320px';
        wrapper.appendChild(group);
        return wrapper;
    },
};

export const CollapsedByDefault = {
    render: () => {
        const group = document.createElement('eb-group');
        group.setAttribute('legend', 'Collapsed Section');
        group.setAttribute('collapsible', '');
        group.setAttribute('collapsed', '');

        const field = document.createElement('eb-string-field');
        field.setAttribute('name', 'description');
        field.setAttribute('label', 'Description');
        field.value = 'Hidden until expanded';

        group.appendChild(field);

        const wrapper = document.createElement('div');
        wrapper.style.width = '320px';
        wrapper.appendChild(group);
        return wrapper;
    },
};
