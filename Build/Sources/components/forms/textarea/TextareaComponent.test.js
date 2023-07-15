import React from 'react';
import { render, fireEvent } from '@testing-library/react';
import '@testing-library/jest-dom';
import TextareaComponent from './TextareaComponent';

describe('TextareaComponent', () => {
    it('renders without crashing', () => {
        const { getByLabelText } = render(<TextareaComponent label="My Label" placeholder="My Placeholder" identifier="myIdentifier" />);
        expect(getByLabelText('My Label')).toBeInTheDocument();
    });

    it('handles onChange', () => {
        const mockOnChange = jest.fn();
        const { getByLabelText } = render(<TextareaComponent label="My Label" placeholder="My Placeholder" identifier="myIdentifier" onChange={mockOnChange} />);
        const textarea = getByLabelText('My Label');
        fireEvent.change(textarea, { target: { value: 'test' } });
        expect(mockOnChange).toHaveBeenCalledWith('test');
    });

    it('validates onChange', () => {
        const mockOnChange = jest.fn();
        const { getByLabelText } = render(
            <TextareaComponent
                label="My Label"
                placeholder="My Placeholder"
                identifier="myIdentifier"
                onChange={mockOnChange}
                validation={{ isRequired: true }}
            />
        );
        const textarea = getByLabelText('My Label');

        // Testen des leeren Textbereichs
        fireEvent.change(textarea, { target: { value: ' ' } }); // Leerzeichen
        expect(textarea).toHaveClass('is-invalid');

        // Testen des gefüllten Textbereichs
        fireEvent.change(textarea, { target: { value: 'test' } });
        expect(textarea).toHaveClass('is-valid');
    });
});
