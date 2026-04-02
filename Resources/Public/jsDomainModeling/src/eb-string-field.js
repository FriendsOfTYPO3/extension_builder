import { html, css } from 'lit';
import { EbField } from './eb-field.js';

export class EbStringField extends EbField {
    static properties = {
        ...EbField.properties,
        placeholder: { type: String },
        typeInvite: { type: String, attribute: 'type-invite' },
        forceAlphaNumeric: { type: Boolean, attribute: 'force-alpha-numeric' },
        lcFirst: { type: Boolean, attribute: 'lc-first' },
        ucFirst: { type: Boolean, attribute: 'uc-first' },
        firstCharNonNumeric: { type: Boolean, attribute: 'first-char-non-numeric' },
        minLength: { type: Number, attribute: 'min-length' },
        maxLength: { type: Number, attribute: 'max-length' },
    };

    static styles = css`
        :host { display: block; }
        label { display: block; margin-bottom: 2px; }
        input { width: 100%; box-sizing: border-box; }
    `;

    _onInput(e) {
        let v = e.target.value;
        if (this.forceAlphaNumeric) {
            v = v.replace(/[^a-zA-Z0-9]/g, '');
        }
        if (this.lcFirst && v.length > 0) {
            v = v.charAt(0).toLowerCase() + v.slice(1);
        }
        if (this.ucFirst && v.length > 0) {
            v = v.charAt(0).toUpperCase() + v.slice(1);
        }
        if (v !== e.target.value) {
            e.target.value = v;
        }
        this.value = v;
        this._fireUpdated();
    }

    getValue() {
        return this.value ?? '';
    }

    setValue(v) {
        this.value = v;
        this.requestUpdate();
    }

    validate() {
        const v = this.getValue();
        if (this.required && !v) return false;
        if (this.minLength && v.length < this.minLength) return false;
        if (this.maxLength && v.length > this.maxLength) return false;
        if (this.firstCharNonNumeric && v.length > 0 && /^[0-9]/.test(v)) return false;
        return true;
    }

    render() {
        const placeholder = this.placeholder || this.typeInvite || '';
        return html`
            ${this.label ? html`<label>${this.label}</label>` : ''}
            <input
                type="text"
                .value="${this.value ?? ''}"
                placeholder="${placeholder}"
                @input="${this._onInput}"
            >
        `;
    }
}

customElements.define('eb-string-field', EbStringField);
