import React, { useState, useEffect, useContext } from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import {ValidationErrorsContext} from "../../../App";

const TextareaComponent = ({label = '', placeholder, identifier = '', initialValue = "", onChange = () => {}, validation = null }) => {
    const [value, setValue] = useState(initialValue);
    const [isValid, setIsValid] = useState(false);
    const { setValidationErrors } = useContext(ValidationErrorsContext);

    useEffect(() => {
        if(validation){
            setIsValid(validate(value));
        }
    }, [value]);

    const handleChange = (event) => {
        setValue(event.target.value);
        onChange(event.target.value);
    };

    const validate = (value) => {
        if(!validation) {
            return null; // Rückgabewert null, wenn keine Validierung vorhanden ist
        }

        if(!validation?.isRequired) {
            return true;
        }

        if (validation?.isRequired && value?.trim() === '') {
            setValidationErrors(prevState => ({...prevState, [identifier]: true}));
            return false;
        }

        if(validation?.minLength && value?.length < validation.minLength) {
            setValidationErrors(prevState => ({...prevState, [identifier]: true}));
            return false;
        }

        // Hier können Sie weitere Validierungsregeln hinzufügen
        setValidationErrors(prevState => ({...prevState, [identifier]: false}));
        return true;
    };

    useEffect(() => {
        setValue(initialValue);
    }, [initialValue]);

    return (
        <div className="mb-2">
            <label htmlFor={identifier} className="fs-3 form-label">
                {label}
            </label>
            <textarea
                type="text"
                className={classNames("fs-3 form-control form-control-sm", {
                    'is-valid': validation && isValid === true,
                    'is-invalid': validation && isValid === false,
                })}
                id={identifier}
                name={identifier}
                placeholder={placeholder}
                value={value}
                onChange={handleChange}
                rows="5"
            />
        </div>
    );
};

TextareaComponent.propTypes = {
    label: PropTypes.string,
    placeholder: PropTypes.string.isRequired,
    identifier: PropTypes.string,
    onChange: PropTypes.func,
    validation: PropTypes.shape({
        isRequired: PropTypes.bool,
        minLength: PropTypes.number,
        maxLength: PropTypes.number
    }),
};

export default TextareaComponent;
