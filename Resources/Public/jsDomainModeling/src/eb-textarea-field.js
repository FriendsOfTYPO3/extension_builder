import { html } from 'lit';
import { EbField } from './eb-field.js';

export class EbTextareaField extends EbField {
    static properties = {
        ...EbField.properties,
        rows: { type: Number },
    };

    constructor() {
        super();
        this.rows = 4;
    }

    createRenderRoot() { return this; }

    _onInput(e) {
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
        return html`
            <div class="form-group">
                ${this.label ? html`<label class="form-label">${this.label}</label>` : ''}
                <textarea
                    class="form-control"
                    rows="${this.rows}"
                    @input="${this._onInput}"
                >${this.value ?? ''}</textarea>
            </div>
        `;
    }
}

customElements.define('eb-textarea-field', EbTextareaField);
