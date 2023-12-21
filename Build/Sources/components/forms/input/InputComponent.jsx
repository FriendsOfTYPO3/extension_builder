import React, { useState, useEffect } from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import { getExtensionKeyIsValid } from "../../../helper";

const InputComponent = ({ label, identifier, initialValue, onChange, validation }) => {
    const [value, setValue] = useState(initialValue || "");
    const [isValid, setIsValid] = useState(null);

    const handleChange = (event) => {
        setValue(event.target.value);
        onChange(event.target.value);
    };

    const validate = (value) => {
        if(validation){
            return !(
                validation.isRequired && value.trim() === '' ||
                validation.minLength && value.length < validation.minLength ||
                validation.maxLength && value.length > validation.maxLength ||
                (identifier === 'extensionKey' && !getExtensionKeyIsValid(value))
            );
        }
        return true;
    };

    useEffect(() => {
        if(validation){
            setIsValid(validate(value));
        }
    }, [value, validation]);

    useEffect(() => {
        // console.log("initial value in input component changed:" + initialValue)
        setValue(initialValue);
    }, [initialValue]);

    return (
        <div className="mb-2">
            {
                label !== '' && (
                    <label htmlFor={identifier} className="fs-3 form-label mb-1">
                        {label}
                    </label>
                )
            }
            <input
                type="text"
                className={classNames("fs-3 form-control form-control-sm", {
                    'is-valid': isValid === true,
                    'is-invalid': isValid === false,
                })}
                value={value !== '' ? value : ''}
                placeholder={label !== '' ? label : ''}
                aria-label={label !== '' ? label : ''}
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
