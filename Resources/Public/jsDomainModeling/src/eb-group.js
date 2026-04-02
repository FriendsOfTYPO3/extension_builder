import { LitElement, html, css } from 'lit';

export class EbGroup extends LitElement {
    static properties = {
        legend: { type: String },
        collapsible: { type: Boolean },
        collapsed: { type: Boolean, reflect: true },
        advancedMode: { type: Boolean, attribute: 'advanced-mode', reflect: true },
    };

    static styles = css`
        :host { display: block; }
        fieldset { border: 1px solid #ccc; padding: 8px; margin: 0; }
        legend {
            padding: 0 4px;
            font-weight: bold;
        }
        :host([collapsible]) legend {
            cursor: pointer;
            user-select: none;
        }
        :host([collapsible]) legend::before {
            content: '▼ ';
        }
        :host([collapsible][collapsed]) legend::before {
            content: '▶ ';
        }
        :host([collapsed]) .content {
            display: none;
        }
        ::slotted([advanced]) {
            display: none;
        }
        :host([advanced-mode]) ::slotted([advanced]) {
            display: block;
        }
    `;

    connectedCallback() {
        super.connectedCallback();
        this.addEventListener('field-updated', this._onFieldUpdated);
    }

    disconnectedCallback() {
        super.disconnectedCallback();
        this.removeEventListener('field-updated', this._onFieldUpdated);
    }

    _onFieldUpdated(e) {
        if (e.detail?.name !== 'relationType') return;
        const renderTypeField = this.querySelector('[name=renderType]');
        if (!renderTypeField) return;
        const optionMap = {
            zeroToOne:  ['selectSingle', 'selectMultipleSideBySide', 'inline'],
            manyToOne:  ['selectSingle', 'selectMultipleSideBySide'],
            zeroToMany: ['inline', 'selectMultipleSideBySide'],
            manyToMany: ['selectMultipleSideBySide', 'selectSingleBox', 'selectCheckBox'],
        };
        renderTypeField.allowedValues = optionMap[e.detail.value] ?? null;
    }

    _initRelationTypes() {
        this.querySelectorAll('[name=relationType]').forEach(field => {
            const value = field.value ?? field.getValue?.();
            if (!value) return;
            const renderTypeField = this.querySelector('[name=renderType]');
            if (!renderTypeField) return;
            const optionMap = {
                zeroToOne:  ['selectSingle', 'selectMultipleSideBySide', 'inline'],
                manyToOne:  ['selectSingle', 'selectMultipleSideBySide'],
                zeroToMany: ['inline', 'selectMultipleSideBySide'],
                manyToMany: ['selectMultipleSideBySide', 'selectSingleBox', 'selectCheckBox'],
            };
            renderTypeField.allowedValues = optionMap[value] ?? null;
        });
    }

    _toggleCollapse() {
        if (this.collapsible) {
            this.collapsed = !this.collapsed;
        }
    }

    _onSlotChange() {
        this.requestUpdate();
        this._initRelationTypes();
    }

    getValue() {
        const result = {};
        this.querySelectorAll('[name]').forEach(field => {
            if (typeof field.getValue === 'function') {
                result[field.name] = field.getValue();
            }
        });
        return result;
    }

    setValue(obj) {
        if (!obj) return;
        this.querySelectorAll('[name]').forEach(field => {
            if (typeof field.setValue === 'function' && obj[field.name] !== undefined) {
                field.setValue(obj[field.name]);
            }
        });
    }

    render() {
        return html`
            <fieldset>
                ${this.legend ? html`
                    <legend @click="${this._toggleCollapse}">${this.legend}</legend>
                ` : ''}
                <div class="content">
                    <slot @slotchange="${this._onSlotChange}"></slot>
                </div>
            </fieldset>
        `;
    }
}

customElements.define('eb-group', EbGroup);
