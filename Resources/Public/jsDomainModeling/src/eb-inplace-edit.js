import { LitElement, html } from 'lit';

export class EbInplaceEdit extends LitElement {
    static properties = {
        value: { type: String },
        _editing: { type: Boolean, state: true },
    };

    constructor() {
        super();
        this._editing = false;
    }

    createRenderRoot() { return this; }

    _startEdit() {
        this._editing = true;
        this.updateComplete.then(() => {
            this.querySelector('input')?.focus();
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
                <input
                    class="form-control form-control-sm"
                    .value="${this.value ?? ''}"
                    @blur="${this._confirm}"
                    @keydown="${this._onKey}"
                >
            `;
        }
        return html`
            <span
                style="cursor:pointer;border-bottom:1px dashed currentColor;min-width:1em;display:inline-block;"
                @click="${this._startEdit}"
            >${this.value ?? ''}</span>
        `;
    }
}

customElements.define('eb-inplace-edit', EbInplaceEdit);
