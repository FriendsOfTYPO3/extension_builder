import { LitElement, html, css, nothing } from 'lit';

/**
 * Collapsible card-style group container for related fields.
 *
 * Renders child elements in a slotted Bootstrap-style card. When
 * `collapsible` is true the header acts as a toggle button with proper
 * ARIA attributes. Also intercepts `relationType` field changes to
 * constrain the `renderType` select options automatically.
 *
 * @element eb-group
 */
export class EbGroup extends LitElement {
    static properties = {
        name: { type: String },
        legend: { type: String },
        collapsible: { type: Boolean },
        collapsed: { type: Boolean, reflect: true },
        advancedMode: { type: Boolean, attribute: 'advanced-mode', reflect: true },
    };

    static styles = css`
        :host {
            display: block;
        }
        .card {
            border: 1px solid var(--bs-border-color, #dee2e6);
            border-radius: var(--bs-border-radius, 0.25rem);
            background-color: var(--bs-body-bg, #fff);
            color: var(--bs-body-color, #000);
            margin-bottom: 0.5rem;
        }
        .card-header {
            padding: 0.4rem 0.6rem;
            background-color: var(--bs-secondary-bg, transparent);
            border-bottom: 1px solid var(--bs-border-color, #dee2e6);
            font-weight: bold;
        }
        :host([collapsible]) .card-header {
            cursor: pointer;
            user-select: none;
        }
        .card-header::before {
            content: '▼ ';
        }
        :host([collapsed]) .card-header::before {
            content: '▶ ';
        }
        .card-body {
            padding: 0.4rem 0.6rem;
        }
        :host([collapsed]) .card-body {
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
        if (e.detail?.name !== 'relationType') {
            return;
        }
        const renderTypeField = this.querySelector('[name=renderType]');
        if (!renderTypeField) {
            return;
        }
        const optionMap = {
            zeroToOne: ['selectSingle', 'selectMultipleSideBySide', 'inline'],
            manyToOne: ['selectSingle', 'selectMultipleSideBySide'],
            zeroToMany: ['inline', 'selectMultipleSideBySide'],
            manyToMany: ['selectMultipleSideBySide', 'selectSingleBox', 'selectCheckBox'],
        };
        renderTypeField.allowedValues = optionMap[e.detail.value] ?? null;
    }

    _initRelationTypes() {
        this.querySelectorAll('[name=relationType]').forEach((field) => {
            const value = field.value ?? field.getValue?.();
            if (!value) {
                return;
            }
            const renderTypeField = this.querySelector('[name=renderType]');
            if (!renderTypeField) {
                return;
            }
            const optionMap = {
                zeroToOne: ['selectSingle', 'selectMultipleSideBySide', 'inline'],
                manyToOne: ['selectSingle', 'selectMultipleSideBySide'],
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

    _onHeaderKeyDown(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            this._toggleCollapse();
        }
    }

    _onSlotChange() {
        this.requestUpdate();
        this._initRelationTypes();
    }

    getValue() {
        const result = {};
        this.querySelectorAll('[name]').forEach((field) => {
            if (typeof field.getValue === 'function') {
                result[field.name] = field.getValue();
            }
        });
        return result;
    }

    setValue(obj) {
        if (!obj) {
            return;
        }
        this.querySelectorAll('[name]').forEach((field) => {
            if (typeof field.setValue === 'function' && obj[field.name] !== undefined) {
                field.setValue(obj[field.name]);
            }
        });
    }

    render() {
        return html`
            <div class="card" role="group" aria-label="${this.legend || this.name || 'Group'}">
                ${this.legend
                    ? html`
                          <div
                              class="card-header"
                              @click="${this._toggleCollapse}"
                              @keydown="${this._onHeaderKeyDown}"
                              role="${this.collapsible ? 'button' : nothing}"
                              tabindex="${this.collapsible ? '0' : nothing}"
                              aria-expanded="${this.collapsible ? String(!this.collapsed) : nothing}"
                          >
                              ${this.legend}
                          </div>
                      `
                    : ''}
                <div class="card-body">
                    <slot @slotchange="${this._onSlotChange}"></slot>
                </div>
            </div>
        `;
    }
}

customElements.define('eb-group', EbGroup);
