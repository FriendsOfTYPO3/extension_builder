import { html, css } from 'lit';
import { EbField } from './eb-field.js';

/**
 * Hidden field — stores a value without rendering any visible UI.
 * Used for internal state that needs to participate in form serialization.
 *
 * @element eb-hidden-field
 */
export class EbHiddenField extends EbField {
    static properties = {
        ...EbField.properties,
    };

    static styles = css`
        :host { display: none; }
    `;

    getValue() {
        return this.value ?? '';
    }

    setValue(v) {
        this.value = v;
    }

    render() {
        return html`<input type="hidden" .value="${this.value ?? ''}">`;
    }
}

customElements.define('eb-hidden-field', EbHiddenField);
