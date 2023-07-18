import React, { useState, useEffect } from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';

const TextareaComponent = ({label = '', placeholder, identifier = '', onChange = () => {}, validation = null }) => {
    const [value, setValue] = useState('');
    const [isValid, setIsValid] = useState(null);

    useEffect(() => {
        if(validation){
            setIsValid(validate(value));
        }
    }, [value, validation]);

    const handleChange = (event) => {
        setValue(event.target.value);
        onChange(event.target.value);
    };

    const validate = (value) => {
        if (validation?.isRequired && value.trim() === '') {
            return false;
        }
        // Hier können Sie weitere Validierungsregeln hinzufügen
        return true;
    };

    return (
        <div className="mb-2">
            <label htmlFor={identifier} className="form-label">
                {label}
            </label>
            <textarea
                type="text"
                className={classNames("form-control form-control-sm", {
                    'is-valid': isValid === true,
                    'is-invalid': isValid === false,
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
        isRequired: PropTypes.bool
    }),
};

export default TextareaComponent;
