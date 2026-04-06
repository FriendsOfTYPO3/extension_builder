import { html, css } from 'lit';
import { EbField } from './eb-field.js';
import { formStyles } from './styles/form-styles.js';
import { translate } from './translate.js';

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
        description: { type: String },
        helpLink: { type: String, attribute: 'help-link' },
    };

    static styles = [
        formStyles,
        css`
            .help-link {
                font-size: 0.75em;
                color: var(--bs-secondary-color, #6c757d);
                text-decoration: none;
                margin-left: 0.25rem;
                opacity: 0.7;
            }
            .help-link:hover {
                opacity: 1;
                text-decoration: underline;
            }
            .help-text {
                display: block;
                margin-top: 0.2rem;
                font-size: 0.8em;
                color: var(--bs-secondary-color, #6c757d);
            }
        `,
    ];

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
                ${this.helpLink
                    ? html`<a href="${this.helpLink}" target="_blank" class="help-link" title="Documentation">?</a>`
                    : ''}
                <textarea
                    id="${inputId}"
                    class="form-control"
                    rows="${this.rows}"
                    ?aria-required="${this.required}"
                    @input="${this._onInput}"
                >
${this.value ?? ''}</textarea
                >
                ${this.description ? html`<small class="help-text">${translate(this.description)}</small>` : ''}
            </div>
        `;
    }
}

customElements.define('eb-textarea-field', EbTextareaField);
