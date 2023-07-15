import React, { useState } from 'react';
import PropTypes from 'prop-types';

const SelectComponent = ({ label, options, defaultValue, identifier, onChange }) => {
    const [value, setValue] = useState('');

    const handleChange = (event) => {
        setValue(event.target.value);
        onChange(event.target.value);
    };

    return (
        <div className="mb-2">
            <label
                htmlFor={identifier}>
                {label}
            </label>
            <select
                className="form-select" aria-label={label}
                onChange={handleChange}
            >
                <option>{defaultValue}</option>
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
    options: PropTypes.arrayOf(
        PropTypes.shape({
            label: PropTypes.string.isRequired,
            value: PropTypes.string.isRequired,
        })
    ),
    onChange: PropTypes.func,
};

SelectComponent.defaultProps = {
    label: '',
    options: [],
    onChange: () => {},
};

export default SelectComponent;
