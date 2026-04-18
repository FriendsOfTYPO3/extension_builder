import './eb-inplace-edit.js';

export default {
    title: 'Canvas/InplaceEdit',
    component: 'eb-inplace-edit',
    tags: ['autodocs'],
    argTypes: {
        value: { control: 'text' },
    },
    parameters: {
        docs: {
            description: {
                component:
                    'Click-to-edit inline text. Renders as a dashed-underline span in display mode; switches to an input on click. Confirm with Enter or blur, cancel with Escape. Fires `inplace-change` on confirmation.',
            },
        },
    },
};

const render = (args) => {
    const wrapper = document.createElement('div');
    wrapper.style.padding = '1rem';
    wrapper.style.fontWeight = 'bold';
    wrapper.style.fontSize = '1rem';

    const el = document.createElement('eb-inplace-edit');
    el.value = args.value ?? 'MyDomainObject';

    // Storybook Actions panel picks up the custom event via the DOM event listener
    el.addEventListener('inplace-change', () => {});

    wrapper.appendChild(el);
    return wrapper;
};

export const Default = {
    args: { value: 'MyDomainObject' },
    render,
};

export const EmptyValue = {
    args: { value: '' },
    render,
};

export const LongName = {
    args: { value: 'AstronomicalObservationSession' },
    render,
};
