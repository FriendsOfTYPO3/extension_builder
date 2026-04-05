import { html } from 'lit';
import { EbField } from './eb-field.js';
import { formStyles } from './styles/form-styles.js';

/**
 * Checkbox field for boolean values.
 *
 * @element eb-boolean-field
 * @fires field-updated - When the checkbox state changes
 */
export class EbBooleanField extends EbField {
    static properties = {
        ...EbField.properties,
    };

    static styles = [formStyles];

    _onChange(e) {
        this.value = e.target.checked;
        this._fireUpdated();
    }

    getValue() {
        return Boolean(this.value);
    }

    setValue(v) {
        this.value = Boolean(v);
        this.requestUpdate();
    }

    render() {
        return html`
            <div class="form-check form-check-type-toggle">
                <input
                    class="form-check-input"
                    type="checkbox"
                    .checked="${Boolean(this.value)}"
                    aria-checked="${Boolean(this.value)}"
                    @change="${this._onChange}"
                    id="eb-bool-${this.name}"
                />
                <label class="form-check-label" for="eb-bool-${this.name}"> ${this.label || ''} </label>
            </div>
        `;
    }
}

customElements.define('eb-boolean-field', EbBooleanField);
