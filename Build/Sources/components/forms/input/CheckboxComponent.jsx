import React, { useState, useEffect } from 'react';
import PropTypes from 'prop-types';

const CheckboxComponent = ({checked, label, identifier, onChange = () => {}}) => {
    const [isChecked, setIsChecked] = useState(checked || false);

    const handleChange = (event) => {
        const { checked } = event.target;
        setIsChecked(prevState => prevState !== checked ? checked : prevState);
        onChange(checked);
    };

    useEffect(() => {
        setIsChecked(checked);
    }, [checked]);

    return (
        <div className="d-flex justify-content-between form-check ps-0 form-switch mb-2">
            <label
                className="fs-3 form-check-label"
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
