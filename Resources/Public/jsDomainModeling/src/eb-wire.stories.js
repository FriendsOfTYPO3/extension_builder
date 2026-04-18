import './eb-wire.js';

export default {
    title: 'Canvas/Wire',
    component: 'eb-wire',
    tags: ['autodocs'],
    parameters: {
        docs: {
            description: {
                component:
                    'SVG cubic Bézier curve connecting two terminals on the canvas. Must be rendered inside an `<svg>` element. Coordinates are in `eb-layer` canvas space.',
            },
        },
    },
};

function makeSvg(width, height, ...wires) {
    const wrapper = document.createElement('div');
    wrapper.style.width = `${width}px`;
    wrapper.style.height = `${height}px`;
    wrapper.style.border = '1px solid #dee2e6';
    wrapper.style.borderRadius = '4px';
    wrapper.style.background = '#f8f9fa';
    wrapper.style.position = 'relative';

    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('width', width);
    svg.setAttribute('height', height);
    svg.style.position = 'absolute';
    svg.style.top = '0';
    svg.style.left = '0';

    wires.forEach((wire) => svg.appendChild(wire));
    wrapper.appendChild(svg);
    return wrapper;
}

export const StraightDown = {
    name: 'Vertical wire',
    render: () => {
        const wire = document.createElement('eb-wire');
        wire.setAttribute('x1', '150');
        wire.setAttribute('y1', '20');
        wire.setAttribute('x2', '150');
        wire.setAttribute('y2', '180');
        return makeSvg(300, 200, wire);
    },
};

export const DiagonalWire = {
    name: 'Diagonal wire',
    render: () => {
        const wire = document.createElement('eb-wire');
        wire.setAttribute('x1', '50');
        wire.setAttribute('y1', '30');
        wire.setAttribute('x2', '250');
        wire.setAttribute('y2', '170');
        return makeSvg(300, 200, wire);
    },
};

export const MultipleWires = {
    name: 'Multiple wires',
    render: () => {
        const wire1 = document.createElement('eb-wire');
        wire1.setAttribute('x1', '80');
        wire1.setAttribute('y1', '20');
        wire1.setAttribute('x2', '220');
        wire1.setAttribute('y2', '180');

        const wire2 = document.createElement('eb-wire');
        wire2.setAttribute('x1', '220');
        wire2.setAttribute('y1', '20');
        wire2.setAttribute('x2', '80');
        wire2.setAttribute('y2', '180');
        wire2.style.setProperty('--eb-wire-color', '#ff8700');

        return makeSvg(300, 200, wire1, wire2);
    },
};
