import { ButtonComponent } from './ButtonComponent';

export default {
    title: 'Example/Button',
    component: ButtonComponent,
    parameters: {
        label: 'centered',
    },
    tags: ['autodocs'],
};

export const Primary = {
    args: {
        primary: true,
        label: 'Button',
    },
};
