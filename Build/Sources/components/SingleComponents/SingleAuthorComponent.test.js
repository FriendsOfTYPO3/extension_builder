import { render, fireEvent } from '@testing-library/react';
import { SingleAuthorComponent } from './SingleAuthorComponent';
import React from 'react';
import '@testing-library/jest-dom';


describe('SingleAuthorComponent', () => {
    const mockAuthor = {
        id: '1',
        name: 'John Doe',
        role: 'Developer',
        email: 'john.doe@test.com',
        company: 'Test Company'
    };

    const updateAuthorMock = jest.fn();
    const removeAuthorMock = jest.fn();
    const moveAuthorMock = jest.fn();

    it('displays the correct initial author information', () => {
        const { getByLabelText } = render(<SingleAuthorComponent author={mockAuthor} updateAuthorHandler={updateAuthorMock} removeAuthorHandler={removeAuthorMock} moveAuthor={moveAuthorMock} index={0} authors={[mockAuthor]} />);

        expect(getByLabelText('Author')).toHaveValue(mockAuthor.name);
        // expect(getByLabelText('Role')).toHaveValue(mockAuthor.role);
        expect(getByLabelText('E-Mail')).toHaveValue(mockAuthor.email);
        expect(getByLabelText('Company')).toHaveValue(mockAuthor.company);
    });

    it('calls updateAuthorHandler when an input field is changed', () => {
        const { getByLabelText } = render(<SingleAuthorComponent author={mockAuthor} updateAuthorHandler={updateAuthorMock} removeAuthorHandler={removeAuthorMock} moveAuthor={moveAuthorMock} index={0} authors={[mockAuthor]} />);

        fireEvent.change(getByLabelText('Author'), { target: { value: 'New Name' } });
        expect(updateAuthorMock).toHaveBeenCalledWith(mockAuthor.id, 'name', 'New Name');
    });

    it('calls removeAuthorHandler when the delete button is clicked', () => {
        const { getByLabelText } = render(<SingleAuthorComponent author={mockAuthor} updateAuthorHandler={updateAuthorMock} removeAuthorHandler={removeAuthorMock} moveAuthor={moveAuthorMock} index={0} authors={[mockAuthor]} />);

        fireEvent.click(getByLabelText('Trash'));
        expect(removeAuthorMock).toHaveBeenCalledWith(mockAuthor.id);
    });
});
