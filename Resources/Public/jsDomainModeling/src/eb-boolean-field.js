import { html, css } from 'lit';
import { EbField } from './eb-field.js';

export class EbBooleanField extends EbField {
    static properties = {
        ...EbField.properties,
    };

    static styles = css`
        :host { display: block; }
        label { display: inline-flex; align-items: center; gap: 4px; cursor: pointer; }
    `;

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
            <label>
                <input type="checkbox" .checked="${Boolean(this.value)}" @change="${this._onChange}">
                ${this.label || ''}
            </label>
        `;
    }
}

customElements.define('eb-boolean-field', EbBooleanField);
