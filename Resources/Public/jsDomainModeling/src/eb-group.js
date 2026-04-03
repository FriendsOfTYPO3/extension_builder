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
        .card {
            border: var(--bs-card-border-width, 1px) solid var(--bs-card-border-color, #dee2e6);
            border-radius: var(--bs-card-border-radius, 0.375rem);
            margin-bottom: 0.5rem;
        }
        .card-header {
            padding: var(--bs-card-cap-padding-y, 0.5rem) var(--bs-card-cap-padding-x, 1rem);
            background-color: var(--bs-card-cap-bg, transparent);
            border-bottom: var(--bs-card-border-width, 1px) solid var(--bs-card-border-color, #dee2e6);
            font-weight: bold;
        }
        :host([collapsible]) .card-header {
            cursor: pointer;
            user-select: none;
        }
        .card-header::before { content: '▼ '; }
        :host([collapsed]) .card-header::before { content: '▶ '; }
        .card-body { padding: var(--bs-card-spacer-y, 1rem) var(--bs-card-spacer-x, 1rem); }
        :host([collapsed]) .card-body { display: none; }
        ::slotted([advanced]) { display: none; }
        :host([advanced-mode]) ::slotted([advanced]) { display: block; }
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
            <div class="card">
                ${this.legend ? html`
                    <div class="card-header" @click="${this._toggleCollapse}">${this.legend}</div>
                ` : ''}
                <div class="card-body">
                    <slot @slotchange="${this._onSlotChange}"></slot>
                </div>
            </div>
        `;
    }
}

customElements.define('eb-group', EbGroup);
