import { LitElement } from 'lit';

/**
 * Abstract base class for all Extension Builder form field components.
 *
 * Provides the common interface (getValue, setValue, validate) and fires
 * a `field-updated` event on value changes. All concrete field types extend
 * this class and inherit its properties.
 *
 * @abstract
 */
export class EbField extends LitElement {
    static properties = {
        /** Field identifier, used as the key when collecting form values. */
        name: { type: String },
        /** Human-readable label rendered above the input. */
        label: { type: String },
        /** Current field value. */
        value: {},
        /** Whether the field must have a non-empty value before saving. */
        required: { type: Boolean },
        /** When true, the field is only shown in advanced mode. */
        advanced: { type: Boolean },
    };

    /**
     * Dispatches a `field-updated` CustomEvent that bubbles through Shadow DOM
     * boundaries so parent components can collect updated values.
     */
    _fireUpdated() {
        this.dispatchEvent(new CustomEvent('field-updated', {
            bubbles: true,
            composed: true,
            detail: { name: this.name, value: this.getValue() },
        }));
    }

    /**
     * Returns the current field value.
     * @returns {*} Current value
     */
    getValue() {
        return this.value;
    }

    /**
     * Programmatically sets the field value and requests a re-render.
     * @param {*} v - New value
     */
    setValue(v) {
        this.value = v;
    }

    /**
     * Validates the current value against field constraints.
     * @returns {boolean} true if valid
     */
    validate() {
        return true;
    }
}
