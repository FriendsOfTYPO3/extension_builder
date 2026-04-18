import './eb-hidden-field.js';
import './eb-string-field.js';

export default {
    title: 'Form Fields/HiddenField',
    component: 'eb-hidden-field',
    tags: ['autodocs'],
    parameters: {
        docs: {
            description: {
                component:
                    'Hidden field that stores a value without rendering any visible UI. Used for internal state (e.g. uid) that participates in form serialisation. Shown here inside a group alongside a visible field.',
            },
        },
    },
};

export const WithVisibleSibling = {
    name: 'Hidden field inside a form group',
    render: () => {
        const wrapper = document.createElement('div');
        wrapper.style.width = '320px';

        const label = document.createElement('p');
        label.style.fontSize = '0.75rem';
        label.style.color = '#6c757d';
        label.textContent =
            'The uid field below is hidden (display: none). It stores the value "42" without rendering.';

        const hiddenField = document.createElement('eb-hidden-field');
        hiddenField.setAttribute('name', 'uid');
        hiddenField.value = '42';

        const visibleField = document.createElement('eb-string-field');
        visibleField.setAttribute('name', 'propertyName');
        visibleField.setAttribute('label', 'Property Name');
        visibleField.value = 'title';

        wrapper.appendChild(label);
        wrapper.appendChild(hiddenField);
        wrapper.appendChild(visibleField);
        return wrapper;
    },
};
