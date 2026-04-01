import { html, css } from 'lit';
import { repeat } from 'lit/directives/repeat.js';
import { EbField } from './eb-field.js';

export class EbSelectField extends EbField {
    static properties = {
        ...EbField.properties,
        selectValues: { type: Array, attribute: 'select-values' },
        selectOptions: { type: Array, attribute: 'select-options' },
    };

    static styles = css`
        :host { display: block; }
        label { display: block; margin-bottom: 2px; }
        select { width: 100%; box-sizing: border-box; }
    `;

    _getOptions() {
        const opts = this.selectValues || this.selectOptions || [];
        return opts.map(o => typeof o === 'string' ? { value: o, label: o } : o);
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
        const options = this._getOptions();
        return html`
            ${this.label ? html`<label>${this.label}</label>` : ''}
            <select @change="${this._onChange}">
                ${repeat(options, o => o.value, o => html`
                    <option value="${o.value}" ?selected="${this.value === o.value}">${o.label}</option>
                `)}
            </select>
        `;
    }
}

customElements.define('eb-select-field', EbSelectField);
