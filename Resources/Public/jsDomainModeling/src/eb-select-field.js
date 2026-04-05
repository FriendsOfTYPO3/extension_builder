import { html } from 'lit';
import { repeat } from 'lit/directives/repeat.js';
import { EbField } from './eb-field.js';
import { formStyles } from './styles/form-styles.js';

/**
 * Dropdown select field with optional value filtering.
 *
 * When `allowedValues` is set, only matching options are rendered and the
 * value is auto-reset to the first allowed option if the current value is
 * no longer in the allowed set.
 *
 * @element eb-select-field
 * @fires field-updated - When the selected option changes
 */
export class EbSelectField extends EbField {
    static properties = {
        ...EbField.properties,
        /** Array of option values (e.g. ['inline', 'selectSingle']). */
        selectValues: { type: Array, attribute: 'select-values' },
        /** Array of option labels, parallel to selectValues. Falls back to selectValues if omitted. */
        selectOptions: { type: Array, attribute: 'select-options' },
        /** When set, only options whose value is in this array are shown. */
        allowedValues: { type: Array },
    };

    static styles = [formStyles];

    _getOptions() {
        const values = this.selectValues ?? [];
        const labels = this.selectOptions ?? values;
        return values.map((v, i) => ({ value: v, label: labels[i] ?? v }));
    }

    _visibleOptions() {
        const all = this._getOptions();
        return this.allowedValues ? all.filter((o) => this.allowedValues.includes(o.value)) : all;
    }

    updated(changedProps) {
        if (changedProps.has('allowedValues') && this.allowedValues) {
            const visible = this._visibleOptions();
            if (visible.length > 0 && !visible.some((o) => o.value === this.value)) {
                this.value = visible[0].value;
                this._fireUpdated();
            }
        }
    }

    _onChange(e) {
        this.value = e.target.value;
        this._fireUpdated();
    }

    getValue() {
        return this.value ?? '';
    }

    setValue(v) {
        this.value = v;
        this.requestUpdate();
    }

    render() {
        const options = this._visibleOptions();
        const inputId = `eb-sel-${this.name}`;
        return html`
            <div class="form-group">
                ${this.label ? html`<label class="form-label" for="${inputId}">${this.label}</label>` : ''}
                <select
                    id="${inputId}"
                    class="form-select"
                    aria-label="${this.label || this.name}"
                    @change="${this._onChange}"
                >
                    ${repeat(
                        options,
                        (o) => o.value,
                        (o) => html`
                            <option value="${o.value}" ?selected="${this.value === o.value}">${o.label}</option>
                        `
                    )}
                </select>
            </div>
        `;
    }
}

customElements.define('eb-select-field', EbSelectField);
