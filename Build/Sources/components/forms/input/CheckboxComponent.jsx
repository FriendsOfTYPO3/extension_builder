import React, { useState } from 'react';
import PropTypes from 'prop-types';

const CheckboxComponent = ({checked = false, label, identifier, onChange = () => {}}) => {
    const [isChecked, setIsChecked] = useState(checked);

    const handleChange = (event) => {
        const { checked } = event.target;
        setIsChecked(prevState => prevState !== checked ? checked : prevState);
        onChange(checked);
    };

    return (
        <div className="form-check form-switch mb-2">
            <label
                className="form-check-label"
                htmlFor={identifier}
            >
                {label}
            </label>
            <input
                className="form-check-input"
                type="checkbox"
                role="switch"
                id={identifier}
                checked={isChecked}
                onChange={handleChange}
            />
        </div>
    );
};

CheckboxComponent.propTypes = {
    checked: PropTypes.bool,
    label: PropTypes.string.isRequired,
    identifier: PropTypes.string.isRequired,
    onChange: PropTypes.func,
};

export default CheckboxComponent;
