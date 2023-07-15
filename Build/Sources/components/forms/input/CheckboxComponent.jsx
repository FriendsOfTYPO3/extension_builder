import React, { useState } from 'react';
import PropTypes from 'prop-types';

const CheckboxComponent = ({ checked, label, identifier, onChange }) => {
    const [isChecked, setIsChecked] = useState(checked);

    const handleChange = (event) => {
        setIsChecked(event.target.checked);
        onChange(event.target.checked);
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
    onChange: PropTypes.func,
};

CheckboxComponent.defaultProps = {
    checked: false,
    onChange: () => {},
};

export default CheckboxComponent;
