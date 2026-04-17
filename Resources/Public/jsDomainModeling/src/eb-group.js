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
        /**
         * When true, getValue() returns the flat child-values object directly
         * instead of wrapping it under the group name. The parent container is
         * expected to spread these values into its own result object.
         */
        flatten: { type: Boolean },
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
        this._onAdvancedModeChanged = (e) => {
            this.advancedMode = e.detail.enabled;
        };
        window.addEventListener('eb-advanced-mode-changed', this._onAdvancedModeChanged);
    }

    disconnectedCallback() {
        super.disconnectedCallback();
        this.removeEventListener('field-updated', this._onFieldUpdated);
        window.removeEventListener('eb-advanced-mode-changed', this._onAdvancedModeChanged);
    }

    _onFieldUpdated(e) {
        if (e.detail?.name === 'relationType') {
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
        if (e.detail?.name === 'propertyType') {
            this._applyPropertyTypeVisibility(e.detail.value);
        }
    }

    _applyPropertyTypeVisibility(selectedType) {
        this.querySelectorAll('[data-visible-for]').forEach((el) => {
            const allowedTypes = (el.getAttribute('data-visible-for') || '').split(' ').filter(Boolean);
            if (allowedTypes.length > 0) {
                el.style.display = allowedTypes.includes(selectedType) ? '' : 'none';
            }
        });
        this.querySelectorAll('[data-hidden-for]').forEach((el) => {
            const hiddenTypes = (el.getAttribute('data-hidden-for') || '').split(' ').filter(Boolean);
            if (hiddenTypes.length > 0) {
                el.style.display = hiddenTypes.includes(selectedType) ? 'none' : '';
            }
        });
    }

    _initPropertyTypes() {
        const field = this.querySelector('[name=propertyType]');
        if (!field) {
            return;
        }
        const value = field.getValue?.() ?? field.value;
        if (value) {
            this._applyPropertyTypeVisibility(value);
        }
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
        this._initPropertyTypes();
    }

    /**
     * Collects values from direct slotted children only (not deep descendants)
     * to avoid double-collecting fields that belong to nested eb-group elements.
     *
     * When a direct child eb-group has flatten:true its values are spread into
     * the result object rather than nested under the group name key.
     *
     * @returns {Object} Flat or nested values object depending on `flatten`.
     */
    getValue() {
        const result = {};
        // Only iterate assigned slot nodes (direct light-DOM children rendered
        // into the default slot) to avoid picking up fields from nested groups.
        const slot = this.shadowRoot?.querySelector('slot');
        const directChildren = slot ? slot.assignedElements({ flatten: false }) : Array.from(this.children);

        directChildren.forEach((el) => {
            if (typeof el.getValue !== 'function') {
                return;
            }
            // A flatten group merges its values into the parent result object.
            if (el.tagName?.toLowerCase() === 'eb-group' && el.flatten) {
                Object.assign(result, el.getValue());
                return;
            }
            const name = el.getAttribute('name');
            if (name !== null) {
                result[name] = el.getValue();
            }
        });
        return result;
    }

    /**
     * Distributes values to direct slotted children.
     *
     * For flatten child groups the full values object is forwarded so the
     * child group can populate its own fields from the flat key space.
     *
     * @param {Object} obj - Values object to distribute.
     */
    setValue(obj) {
        if (!obj) {
            return;
        }
        const slot = this.shadowRoot?.querySelector('slot');
        const directChildren = slot ? slot.assignedElements({ flatten: false }) : Array.from(this.children);

        directChildren.forEach((el) => {
            if (typeof el.setValue !== 'function') {
                return;
            }
            // A flatten group receives the full object so it can pick its keys.
            if (el.tagName?.toLowerCase() === 'eb-group' && el.flatten) {
                el.setValue(obj);
                return;
            }
            const name = el.getAttribute('name');
            if (name !== null && obj[name] !== undefined) {
                el.setValue(obj[name]);
            }
        });
        this._initPropertyTypes();
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
