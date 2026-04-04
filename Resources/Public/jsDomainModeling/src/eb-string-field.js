import { html } from 'lit';
import { EbField } from './eb-field.js';

export class EbStringField extends EbField {
    static properties = {
        ...EbField.properties,
        placeholder: { type: String },
        typeInvite: { type: String, attribute: 'type-invite' },
        forceAlphaNumeric: { type: Boolean, attribute: 'force-alpha-numeric' },
        forceAlphaNumericUnderscore: { type: Boolean, attribute: 'force-alpha-numeric-underscore' },
        forceLowerCase: { type: Boolean, attribute: 'force-lower-case' },
        noSpaces: { type: Boolean, attribute: 'no-spaces' },
        lcFirst: { type: Boolean, attribute: 'lc-first' },
        ucFirst: { type: Boolean, attribute: 'uc-first' },
        firstCharNonNumeric: { type: Boolean, attribute: 'first-char-non-numeric' },
        noLeadingUnderscore: { type: Boolean, attribute: 'no-leading-underscore' },
        noTrailingUnderscore: { type: Boolean, attribute: 'no-trailing-underscore' },
        forbiddenPrefixes: { type: String, attribute: 'forbidden-prefixes' },
        minLength: { type: Number, attribute: 'min-length' },
        maxLength: { type: Number, attribute: 'max-length' },
        _error: { state: true },
    };

    // PoC: Light DOM so TYPO3 Bootstrap CSS classes (form-control, form-label, etc.) apply directly.
    // Pattern from TYPO3 core: typo3-backend-alert, typo3-backend-pagination use the same approach.
    createRenderRoot() { return this; }

    _getValidationError(v) {
        if (this.required && !v) return 'Required';
        if (!v) return null;
        if (this.minLength && v.length < this.minLength) return `Minimum ${this.minLength} characters`;
        if (this.maxLength && v.length > this.maxLength) return `Maximum ${this.maxLength} characters`;
        if (this.firstCharNonNumeric && /^[0-9]/.test(v)) return 'Must not start with a number';
        if (this.noLeadingUnderscore && v.startsWith('_')) return 'Must not start with an underscore';
        if (this.noTrailingUnderscore && v.endsWith('_')) return 'Must not end with an underscore';
        if (this.forbiddenPrefixes) {
            const match = this.forbiddenPrefixes.split(' ').filter(Boolean).find(p => v.startsWith(p));
            if (match) return `Must not start with "${match}"`;
        }
        return null;
    }

    _onInput(e) {
        let v = e.target.value;
        if (this.forceAlphaNumericUnderscore) {
            v = v.replace(/\s/g, '_').replace(/[^a-zA-Z0-9_]/g, '');
        } else if (this.forceAlphaNumeric) {
            v = v.replace(/[^a-zA-Z0-9]/g, '');
        } else if (this.noSpaces) {
            v = v.replace(/\s/g, '');
        }
        if (this.forceLowerCase) {
            v = v.toLowerCase();
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
        this._error = this._getValidationError(v);
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
        return this._getValidationError(this.getValue()) === null;
    }

    render() {
        const placeholder = this.placeholder || this.typeInvite || '';
        return html`
            ${this.label ? html`<label class="form-label">${this.label}</label>` : ''}
            <input
                class="form-control${this._error ? ' is-invalid' : ''}"
                type="text"
                .value="${this.value ?? ''}"
                placeholder="${placeholder}"
                @input="${this._onInput}"
            >
            ${this._error ? html`<div class="invalid-feedback">${this._error}</div>` : ''}
        `;
    }
}

customElements.define('eb-string-field', EbStringField);
