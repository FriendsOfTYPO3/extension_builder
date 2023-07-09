import {render, fireEvent} from '@testing-library/react';
import {SingleAuthorComponent} from './SingleAuthorComponent';
import React from 'react';
import '@testing-library/jest-dom';


test('it should render properly and buttons should work as expected', () => {
    const mockAuthor1 = {id: 1, name: 'Author 1', role: '', email: '', company: ''};
    const mockAuthor2 = {id: 2, name: 'Author 2', role: '', email: '', company: ''};
    const mockAuthors = [mockAuthor1, mockAuthor2];  // Autorenliste mit zwei Autoren
    const mockUpdateHandler = jest.fn();
    const mockRemoveHandler = jest.fn();
    const mockMoveAuthor = jest.fn();
    const mockIndex1 = 0;  // Index des ersten Autors in der Autorenliste

    const {getByRole, getByText} = render(
        <SingleAuthorComponent
            author={mockAuthor1}
            authors={mockAuthors}  // Autorenliste als Prop hinzufügen
            index={mockIndex1}
            updateAuthorHandler={mockUpdateHandler}
            removeAuthorHandler={mockRemoveHandler}
            moveAuthor={mockMoveAuthor}
        />
    );

    // Prüfe, ob die Button-Schaltflächen ordnungsgemäß gerendert werden und die Klick-Events auslösen
    const trashButton = getByRole('button', {name: /Trash/i});
    fireEvent.click(trashButton);
    expect(mockRemoveHandler).toHaveBeenCalledWith(mockAuthor1.id);

    const arrowDownButton = getByRole('button', {name: /Arrow Down/i});
    fireEvent.click(arrowDownButton);
    expect(mockMoveAuthor).toHaveBeenCalledWith(mockIndex1, 1);

    // Prüfen, ob die Rollenoptionen korrekt gerendert werden
    const roleOptions = ['Developer', 'Project Manager', 'Designer', 'Tester', 'Documentation Writer', 'Reviewer', 'Support', 'Translator', 'Security'];
    roleOptions.forEach((role) => {
        expect(getByText(role)).toBeInTheDocument();
    });
});
