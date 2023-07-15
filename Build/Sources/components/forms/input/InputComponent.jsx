import React, { useState, useEffect } from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';

const InputComponent = ({ label, identifier, initialValue, onChange, validation }) => {
    const [value, setValue] = useState(initialValue || "");
    const [isValid, setIsValid] = useState(null);

    const handleChange = (event) => {
        setValue(event.target.value);
        onChange(event.target.value);
    };

    const validate = (value) => {
        if (validation) {
            if (validation.isRequired && value.trim() === '') {
                return false;
            }
            if (validation.minLength && value.length < validation.minLength) {
                return false;
            }
            // Hier können Sie weitere Validierungsregeln hinzufügen
        }
        return true;
    };

    useEffect(() => {
        if(validation){
            setIsValid(validate(value));
        }
    }, [value, validation]);

    return (
        <div className="mb-2">
            <label
                htmlFor={identifier}
            >
                {label}
            </label>
            <input
                type="text"
                className={classNames("form-control form-control-sm", {
                    'is-valid': isValid === true,
                    'is-invalid': isValid === false,
                })}
                value={value}
                placeholder={label}
                aria-label={label}
                aria-describedby={identifier}
                onChange={handleChange}
            />
        </div>
    );
};

InputComponent.propTypes = {
    label: PropTypes.string,
    initialValue: PropTypes.string,
    onChange: PropTypes.func,
    identifier: PropTypes.string,
    validation: PropTypes.shape({
        isRequired: PropTypes.bool,
        minLength: PropTypes.number,
    }),
};

InputComponent.defaultProps = {
    label: '',
    initialValue: '',
    onChange: () => {},
    identifier: '',
    validation: null,
};

export default InputComponent;
