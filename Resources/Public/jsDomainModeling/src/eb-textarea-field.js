import { html } from 'lit';
import { EbField } from './eb-field.js';
import { formStyles } from './styles/form-styles.js';

/**
 * Multi-line textarea field.
 *
 * @element eb-textarea-field
 * @fires field-updated - When the value changes
 */
export class EbTextareaField extends EbField {
    static properties = {
        ...EbField.properties,
        /** Number of visible text rows. Defaults to 4. */
        rows: { type: Number },
    };

    static styles = [formStyles];

    constructor() {
        super();
        this.rows = 4;
    }

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
        const inputId = `eb-ta-${this.name}`;
        return html`
            <div class="form-group">
                ${this.label ? html`<label class="form-label" for="${inputId}">${this.label}</label>` : ''}
                <textarea
                    id="${inputId}"
                    class="form-control"
                    rows="${this.rows}"
                    ?aria-required="${this.required}"
                    @input="${this._onInput}"
                >${this.value ?? ''}</textarea>
            </div>
        `;
    }
}

customElements.define('eb-textarea-field', EbTextareaField);
