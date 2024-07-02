import { render, fireEvent } from '@testing-library/react';
import React from 'react';
import CheckboxComponent from './CheckboxComponent';

test('CheckboxComponent change value on click', () => {
    const mockOnChange = jest.fn();
    const { getByLabelText } = render(
        <CheckboxComponent
            checked={false}
            label='Test Checkbox'
            identifier='test-checkbox'
            onChange={mockOnChange}
        />
    );

    const checkbox = getByLabelText('Test Checkbox');
    fireEvent.click(checkbox);

    // Überprüfen Sie, ob das Kontrollkästchen beim Klicken auf gewechselt hat.
    expect(checkbox.checked).toBe(true);

    // Überprüfen Sie, ob die onChange-Callback-Funktion ausgeführt wurde.
    expect(mockOnChange).toHaveBeenCalled();
});
