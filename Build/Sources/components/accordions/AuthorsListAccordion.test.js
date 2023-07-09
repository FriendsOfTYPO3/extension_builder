import { render, screen } from '@testing-library/react';
import { AuthorsListAccordion } from './AuthorsListAccordion'; // Ersetzen Sie diesen import mit dem tatsächlichen Pfad zu Ihrer Datei
import React from 'react';
describe('AuthorsListAccordion', () => {
    test('it renders without crashing', () => {
        render(
            <AuthorsListAccordion authors={[]} addAuthorsHandler={() => {}} />
        );
    });

    test('it shows "No authors yet" when there are no authors', () => {
        render(
            <AuthorsListAccordion authors={[]} addAuthorsHandler={() => {}} />
        );
        const noAuthors = screen.queryByText('No authors yet');
        expect(noAuthors).toBeFalsy();
    });

    test('it does not show "No authors yet" when there are authors', () => {
        render(
            <AuthorsListAccordion
                authors={[{
                    id: 1,
                    name: 'Test Author'
                }]}
                addAuthorsHandler={() => {}} />
        );
        const noAuthors = screen.queryByText('No authors yet');
        expect(noAuthors).toBeFalsy();
    });
});
