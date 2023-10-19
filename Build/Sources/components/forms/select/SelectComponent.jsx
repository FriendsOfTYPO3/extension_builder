import React, { useState, useEffect } from 'react';
import PropTypes from 'prop-types';

const SelectComponent = ({ label, options, defaultValue, showEmptyValue = true, identifier, initialValue = "", onChange }) => {
    const [value, setValue] = useState(initialValue);

    const handleChange = (event) => {
        setValue(event.target.value);
        onChange(event.target.value);
    };

    useEffect(() => {
        setValue(initialValue);
    }, [initialValue]);

    return (
        <div className="mb-2">
            <label
                htmlFor={identifier}
                className="form-label"
            >
                {label}
            </label>
            <select
                className="form-select"
                aria-label={label}
                onChange={handleChange}
                value={value}  // Setzen Sie den aktuellen Wert hier
            >
                {showEmptyValue && <option value="">Bitte wählen</option>}
                {
                    options.map((option, index) => {
                        return (
                            <option key={index} value={option}>{option}</option>
                        )
                    })
                }
            </select>
        </div>
    );
};

SelectComponent.propTypes = {
    label: PropTypes.string,
    options: PropTypes.arrayOf(PropTypes.string),
    defaultValue: PropTypes.string,
    identifier: PropTypes.string,
    onChange: PropTypes.func,
};

SelectComponent.defaultProps = {
    label: '',
    options: [],
    onChange: () => {},
};

export default SelectComponent;
