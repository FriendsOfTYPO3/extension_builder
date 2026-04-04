import { LitElement, html } from 'lit';
import { formStyles } from './styles/form-styles.js';

/**
 * Click-to-edit inline text field.
 *
 * Renders as a styled span in display mode. On click it switches to a text
 * input; on blur or Enter the new value is confirmed. Escape cancels.
 *
 * @element eb-inplace-edit
 * @fires inplace-change - When the value is confirmed with `{ value: string }` detail
 */
export class EbInplaceEdit extends LitElement {
    static properties = {
        value: { type: String },
        _editing: { type: Boolean, state: true },
    };

    static styles = [formStyles];

    constructor() {
        super();
        this._editing = false;
    }

    _startEdit() {
        this._editing = true;
        this.updateComplete.then(() => {
            this.shadowRoot.querySelector('input')?.focus();
        });
    }

    _confirm(e) {
        const val = e.target.value;
        this._editing = false;
        if (val !== this.value) {
            this.value = val;
            this.dispatchEvent(new CustomEvent('inplace-change', {
                bubbles: true,
                composed: true,
                detail: { value: val },
            }));
        }
    }

    _cancel() {
        this._editing = false;
    }

    _onKey(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            this._confirm(e);
        } else if (e.key === 'Escape') {
            this._cancel();
        }
    }

    getValue() {
        return this.value ?? '';
    }

    setValue(v) {
        this.value = v;
    }

    render() {
        if (this._editing) {
            return html`
                <div aria-live="polite">
                    <input
                        class="form-control form-control-sm"
                        .value="${this.value ?? ''}"
                        @blur="${this._confirm}"
                        @keydown="${this._onKey}"
                    >
                </div>
            `;
        }
        return html`
            <div aria-live="polite">
                <span
                    style="cursor:pointer;border-bottom:1px dashed currentColor;min-width:1em;display:inline-block;"
                    role="button"
                    tabindex="0"
                    aria-label="Edit: ${this.value ?? ''}"
                    @click="${this._startEdit}"
                    @keydown="${(e) => e.key === 'Enter' && this._startEdit()}"
                >${this.value ?? ''}</span>
            </div>
        `;
    }
}

customElements.define('eb-inplace-edit', EbInplaceEdit);
