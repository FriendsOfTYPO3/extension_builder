import { html } from 'lit';
import { repeat } from 'lit/directives/repeat.js';
import { EbField } from './eb-field.js';
import { formStyles } from './styles/form-styles.js';

export class EbSelectField extends EbField {
    static properties = {
        ...EbField.properties,
        selectValues: { type: Array, attribute: 'select-values' },
        selectOptions: { type: Array, attribute: 'select-options' },
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
        return this.allowedValues ? all.filter(o => this.allowedValues.includes(o.value)) : all;
    }

    updated(changedProps) {
        if (changedProps.has('allowedValues') && this.allowedValues) {
            const visible = this._visibleOptions();
            if (visible.length > 0 && !visible.some(o => o.value === this.value)) {
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
        return html`
            <div class="form-group">
                ${this.label ? html`<label class="form-label">${this.label}</label>` : ''}
                <select class="form-select" @change="${this._onChange}">
                    ${repeat(options, o => o.value, o => html`
                        <option value="${o.value}" ?selected="${this.value === o.value}">${o.label}</option>
                    `)}
                </select>
            </div>
        `;
    }
}

customElements.define('eb-select-field', EbSelectField);
