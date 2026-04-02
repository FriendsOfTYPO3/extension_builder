import { html, css } from 'lit';
import { EbField } from './eb-field.js';

export class EbTextareaField extends EbField {
    static properties = {
        ...EbField.properties,
        cols: { type: Number },
        rows: { type: Number },
    };

    static styles = css`
        :host { display: block; }
        label { display: block; margin-bottom: 2px; }
        textarea { width: 100%; box-sizing: border-box; resize: vertical; }
    `;

    constructor() {
        super();
        this.cols = 30;
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
        return html`
            ${this.label ? html`<label>${this.label}</label>` : ''}
            <textarea
                cols="${this.cols}"
                rows="${this.rows}"
                @input="${this._onInput}"
            >${this.value ?? ''}</textarea>
        `;
    }
}

customElements.define('eb-textarea-field', EbTextareaField);
