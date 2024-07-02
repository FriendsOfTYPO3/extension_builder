import React from 'react';
import { render, fireEvent } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import InputComponent from './InputComponent';
import '@testing-library/jest-dom/extend-expect';

describe('InputComponent', () => {
    let onChangeMock;

    beforeEach(() => {
        onChangeMock = jest.fn();
    });

    it('renders without crashing', () => {
        render(<InputComponent onChange={onChangeMock} />);
    });

    it('should handle value change', () => {
        const { getByPlaceholderText } = render(<InputComponent label="test" initialValue="initial" onChange={onChangeMock} identifier="test-id"/>);
        const inputField = getByPlaceholderText('test');
        fireEvent.change(inputField, { target: { value: 'new value' } });
        expect(onChangeMock).toHaveBeenCalledWith('new value');
    });

    it('should display initial value', () => {
        const { getByDisplayValue } = render(<InputComponent label="test" initialValue="initial" onChange={onChangeMock} identifier="test-id"/>);
        expect(getByDisplayValue('initial')).toBeInTheDocument();
    });

    test('validate the input value', () => {
        const validation = { isRequired: true, minLength: 5 };
        const { getByRole } = render(
            <InputComponent
                label="Test Input"
                identifier="test-input"
                initialValue=""
                onChange={onChangeMock}
                validation={validation}
            />
        );

        const input = getByRole('textbox');

        // Test minLength validation
        fireEvent.change(input, { target: { value: '123' } });
        expect(input.className).toContain('is-invalid');

        fireEvent.change(input, { target: { value: '12345' } });
        expect(input.className).toContain('is-valid');

        // Test isRequired validation
        fireEvent.change(input, { target: { value: '' } });
        expect(input.className).toContain('is-invalid');
    });

    it('should call onChange when value changes', () => {
        const { getByRole } = render(
            <InputComponent
                label="Test Input"
                identifier="test-input"
                initialValue=""
                onChange={onChangeMock}
            />
        );
        const input = getByRole('textbox');
        fireEvent.change(input, { target: { value: 'new value' } });
        expect(onChangeMock).toHaveBeenCalledWith('new value');
    });

    it('should validate without isRequired', () => {
        const validation = { minLength: 5 };
        const { getByRole } = render(
            <InputComponent
                label="Test Input"
                identifier="test-input"
                initialValue=""
                onChange={onChangeMock}
                validation={validation}
            />
        );
        const input = getByRole('textbox');
        fireEvent.change(input, { target: { value: '12345' } });
        expect(input.className).toContain('is-valid');
    });

    it('should initialize with null', () => {
        const { getByRole } = render(
            <InputComponent
                label="Test Input"
                identifier="test-input"
                initialValue={null}
                onChange={onChangeMock}
            />
        );
        const input = getByRole('textbox');
        expect(input.value).toBe('');
    });

    it('should apply defaultProps', () => {
        const { getByRole } = render(<InputComponent />);
        const input = getByRole('textbox');
        fireEvent.change(input, { target: { value: 'new value' } });
        // Stellen Sie sicher, dass es keine Fehler gibt und die Komponente immer noch funktioniert
        expect(input.value).toBe('new value');
    });
});
