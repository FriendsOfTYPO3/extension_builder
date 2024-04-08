import React from 'react';

export const ButtonComponent = ({label}) => {
    return (
        <button
            className="btn btn-primary"
        >
            {label}
        </button>
    );
}
