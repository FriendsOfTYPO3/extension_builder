import React from 'react';
import { render, fireEvent } from '@testing-library/react';
import '@testing-library/jest-dom/extend-expect';
import SelectComponent from './SelectComponent';

test('SelectComponent correctly handles changes', () => {
    const handleChange = jest.fn();
    const options = ['Option 1', 'Option 2', 'Option 3'];
    const { getByLabelText } = render(
        <SelectComponent
            label="Test Select Component"
            options={options}
            onChange={handleChange}
        />
    );

    const select = getByLabelText('Test Select Component');

    expect(select.value).toBe('');

    fireEvent.change(select, { target: { value: 'Option 2' } });

    expect(handleChange).toBeCalledWith('Option 2');
    expect(select.value).toBe('Option 2');
});
