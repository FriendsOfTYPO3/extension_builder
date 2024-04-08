import { AuthorsListAccordion } from './AuthorsListAccordion';
import React from 'react';

import {ValidationErrorsContext} from "../../App";

export default {
    title: 'Accordions/AuthorsListAccordion',
    component: AuthorsListAccordion,
    args: {
        authors: [
            {
                id: 1,
                name: 'John Doe',
                email: ''
            },
            // Füge hier weitere Autoren hinzu, falls notwendig
        ],
        addAuthorsHandler: () => console.log("Add author handler called"), // Dummy-Handler, ersetze dies durch deine tatsächliche Funktion
    },
    parameters: {
        label: 'centered'
    },
    tags: ['autodocs'],
};

const WithMyContext = ({ contextValue, children }) => (
    <ValidationErrorsContext.Provider value={contextValue}>
        {children}
    </ValidationErrorsContext.Provider>
);

export const Primary = (args) => (
    <WithMyContext contextValue={{ setValidationErrors: () => console.log("Fehler setzen") }}>
        <AuthorsListAccordion {...args} />
    </WithMyContext>
);

