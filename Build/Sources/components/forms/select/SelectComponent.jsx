import React, { useState, useEffect, useContext } from 'react';
import PropTypes from 'prop-types';
import classNames from "classnames";
import {ValidationErrorsContext} from "../../../App";

const SelectComponent = ({ label, options, defaultValue, showEmptyValue = true, identifier, initialValue = "", onChange, validation }) => {
    const [value, setValue] = useState(initialValue);
    const [isValid, setIsValid] = useState(null);
    const { setValidationErrors } = useContext(ValidationErrorsContext);

    const handleChange = (event) => {
        setValue(event.target.value);
        onChange(event.target.value);
    };

    useEffect(() => {
        setValue(initialValue);
    }, [initialValue]);

    const validate = (value) => {
        if (validation?.isRequired && value?.trim() === '') {
            setValidationErrors(prevState => ({...prevState, [identifier]: true}));
            return false;
        }

        // Hier können Sie weitere Validierungsregeln hinzufügen
        setValidationErrors(prevState => ({...prevState, [identifier]: false}));
        return true;
    };

    useEffect(() => {
        if(validation){
            setIsValid(validate(value));
        }
    }, [value]);

    return (
        <div className="mb-2">
            <label
                htmlFor={identifier}
                className="fs-3 form-label"
            >
                {label}
            </label>
            <select
                className={classNames("fs-3 form-select", {
                    'is-valid': isValid === true,
                    'is-invalid': isValid === false,
                })}
                aria-label={label}
                onChange={handleChange}
                value={value}
            >
                {showEmptyValue && <option value="">Please choose ...</option>}
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
    validation: PropTypes.shape({
        isRequired: PropTypes.bool,
    }),
};

SelectComponent.defaultProps = {
    label: '',
    identifier: '',
    options: [],
    onChange: () => {},
    validation: null,
};

export default SelectComponent;
