import { html, css } from 'lit';
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
                ${this.helpLink
                    ? html`<a href="${this.helpLink}" target="_blank" class="help-link" title="Documentation">?</a>`
                    : ''}
            </div>
            ${this.description ? html`<small class="help-text">${this.description}</small>` : ''}
        `;
    }
}

customElements.define('eb-boolean-field', EbBooleanField);
