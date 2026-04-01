import { LitElement } from 'lit';

export class EbField extends LitElement {
    static properties = {
        name: { type: String },
        label: { type: String },
        value: {},
        required: { type: Boolean },
        advanced: { type: Boolean },
    };

    _fireUpdated() {
        this.dispatchEvent(new CustomEvent('field-updated', {
            bubbles: true,
            composed: true,
            detail: { name: this.name, value: this.getValue() },
        }));
    }

    getValue() {
        return this.value;
    }

    setValue(v) {
        this.value = v;
    }

    validate() {
        return true;
    }
}
