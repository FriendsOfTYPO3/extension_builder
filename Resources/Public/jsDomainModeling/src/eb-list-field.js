import { LitElement, html, css, nothing } from 'lit';
import { repeat } from 'lit/directives/repeat.js';
import { buttonStyles } from './styles/button-styles.js';
import { formStyles } from './styles/form-styles.js';
import { renderFieldDef } from './render-fields.js';
import './eb-terminal.js';

function _iconUrl(name) {
    const base = window.TYPO3?.settings?.extensionBuilder?.publicResourceWebPath?.core ?? '';
    return base ? `${base}Icons/T3Icons/sprites/actions.svg#${name}` : '';
}

const _iconFallbacks = {
    'actions-caret-up': '↑',
    'actions-caret-down': '↓',
    'actions-delete': '✕',
    'actions-view-list-collapse': '▼',
    'actions-view-list-expand': '▶',
};

function _svgIcon(name) {
    const url = _iconUrl(name);
    if (!url) {
        return html`<span aria-hidden="true">${_iconFallbacks[name] ?? name}</span>`;
    }
    return html`
        <svg width="16" height="16" aria-hidden="true">
            <use href="${url}"></use>
        </svg>
    `;
}

// Field names treated as the item's display label when collapsed.
const _LABEL_FIELDS = new Set(['propertyName', 'relationName', 'customAction', 'name']);

export class EbListField extends LitElement {
    static properties = {
        name: { type: String },
        sortable: { type: Boolean },
        addLabel: { type: String, attribute: 'add-label' },
        elementType: { type: String, attribute: 'element-type' },
        _items: { state: true },
    };

    static styles = [
        buttonStyles,
        formStyles,
        css`
            :host {
                display: block;
            }
            .item-row {
                display: flex;
                align-items: flex-start;
                gap: 4px;
                margin-bottom: 4px;
            }
            .item-content {
                flex: 1;
            }
            .item-content.is-collapsed {
                display: none;
            }
            .item-collapsed-label {
                flex: 1;
                font-size: 12px;
                color: var(--bs-secondary-color, #6c757d);
                padding: 2px 0;
                font-style: italic;
            }
            .item-actions {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 2px;
                padding-top: 2px;
            }
            .item-actions .btn-delete {
                grid-column: 1 / -1;
            }
            .item-actions .btn-collapse {
                grid-column: 1 / -1;
                margin-bottom: 2px;
            }
            .add-btn {
                margin-top: 4px;
            }
            .item-terminal {
                display: flex;
                align-items: center;
                padding-top: 4px;
            }
        `,
    ];

    constructor() {
        super();
        this.sortable = true;
        this.addLabel = 'add';
        this._items = [];
        this._boundOnFieldUpdated = this._onFieldUpdated.bind(this);
    }

    connectedCallback() {
        super.connectedCallback();
        this.addEventListener('field-updated', this._boundOnFieldUpdated);
    }

    disconnectedCallback() {
        super.disconnectedCallback();
        this.removeEventListener('field-updated', this._boundOnFieldUpdated);
    }

    _onFieldUpdated(e) {
        if (!_LABEL_FIELDS.has(e.detail?.name)) {
            return;
        }
        // Find the .item-content ancestor in the composed path
        const itemContent = e
            .composedPath()
            .find((el) => el instanceof Element && el.classList?.contains('item-content'));
        if (!itemContent) {
            return;
        }
        const containers = Array.from(this.shadowRoot?.querySelectorAll('.item-content') ?? []);
        const index = containers.indexOf(itemContent);
        if (index < 0) {
            return;
        }
        const items = [...this._items];
        items[index] = { ...items[index], label: e.detail.value };
        this._items = items;
    }

    get _elementTypeDef() {
        try {
            return JSON.parse(this.elementType || 'null');
        } catch {
            return null;
        }
    }

    get _isWirable() {
        const fields = this._elementTypeDef?.inputParams?.fields ?? [];
        return fields.some((f) => f.inputParams?.wirable);
    }

    _addItem() {
        const uid = parseInt(Date.now() * Math.random()) || Date.now();
        const newIndex = this._items.length;
        this._items = [...this._items, { uid, collapsed: false, label: '' }];
        this.updateComplete.then(() => {
            const containers = Array.from(this.shadowRoot?.querySelectorAll('.item-content') ?? []);
            const container = containers[newIndex];
            if (!container) {
                return;
            }
            const uidField = container.querySelector('[name="uid"]');
            uidField?.setValue?.(String(uid));
        });
        this._fireUpdated();
    }

    _removeItem(index) {
        this._items = this._items.filter((_, i) => i !== index);
        this._fireUpdated();
    }

    _toggleCollapse(index) {
        const items = [...this._items];
        items[index] = { ...items[index], collapsed: !items[index].collapsed };
        this._items = items;
    }

    _moveUp(index) {
        if (index === 0) {
            return;
        }
        const items = [...this._items];
        [items[index - 1], items[index]] = [items[index], items[index - 1]];
        this._items = items;
        this._fireUpdated();
    }

    _moveDown(index) {
        if (index >= this._items.length - 1) {
            return;
        }
        const items = [...this._items];
        [items[index], items[index + 1]] = [items[index + 1], items[index]];
        this._items = items;
        this._fireUpdated();
    }

    _fireUpdated() {
        this.dispatchEvent(
            new CustomEvent('list-updated', {
                bubbles: true,
                composed: true,
                detail: { value: this.getValue() },
            })
        );
    }

    getValue() {
        const containers = this.shadowRoot?.querySelectorAll('.item-content') ?? [];
        return Array.from(containers).map((container) => {
            const group = container.querySelector('eb-group');
            if (group?.getValue) {
                return group.getValue();
            }
            const field = container.querySelector('[name]');
            return field?.getValue?.() ?? null;
        });
    }

    setValue(arr) {
        if (!Array.isArray(arr)) {
            return;
        }
        this._items = arr.map((_, i) => ({ uid: i, collapsed: false, label: '' }));
        this.updateComplete.then(() => {
            const containers = this.shadowRoot?.querySelectorAll('.item-content') ?? [];
            arr.forEach((value, index) => {
                if (!value) {
                    return;
                }
                const container = containers[index];
                if (!container) {
                    return;
                }
                const group = container.querySelector('eb-group');
                if (group?.setValue) {
                    group.setValue(value);
                    return;
                }
                const field = container.querySelector('[name]');
                field?.setValue?.(value);
            });
        });
    }

    render() {
        const def = this._elementTypeDef;
        const wirable = this._isWirable;
        return html`
            ${repeat(
                this._items,
                (item) => item.uid,
                (item, index) => html`
                    <div class="item-row">
                        ${wirable
                            ? html`
                                  <div class="item-terminal">
                                      <eb-terminal droppable terminal-id="REL_${index}"></eb-terminal>
                                  </div>
                              `
                            : nothing}
                        <div class="item-content ${item.collapsed ? 'is-collapsed' : ''}">
                            ${def ? renderFieldDef(def) : nothing}
                        </div>
                        ${item.collapsed
                            ? html`<span class="item-collapsed-label">${item.label || `Item ${index + 1}`}</span>`
                            : nothing}
                        <div class="item-actions">
                            <button
                                class="btn btn-default btn-sm btn-collapse"
                                @click="${() => this._toggleCollapse(index)}"
                                title="${item.collapsed ? 'Expand' : 'Collapse'}"
                            >
                                ${item.collapsed
                                    ? _svgIcon('actions-view-list-expand')
                                    : _svgIcon('actions-view-list-collapse')}
                            </button>
                            ${this.sortable
                                ? html`
                                      <button
                                          class="btn btn-default btn-sm"
                                          @click="${() => this._moveUp(index)}"
                                          title="Move up"
                                      >
                                          ${_svgIcon('actions-caret-up')}
                                      </button>
                                      <button
                                          class="btn btn-default btn-sm"
                                          @click="${() => this._moveDown(index)}"
                                          title="Move down"
                                      >
                                          ${_svgIcon('actions-caret-down')}
                                      </button>
                                  `
                                : nothing}
                            <button
                                class="btn btn-default btn-sm btn-delete"
                                @click="${() => this._removeItem(index)}"
                                title="Remove"
                            >
                                ${_svgIcon('actions-delete')}
                            </button>
                        </div>
                    </div>
                `
            )}
            <button class="btn btn-default btn-sm add-btn" @click="${this._addItem}">${this.addLabel}</button>
        `;
    }
}

customElements.define('eb-list-field', EbListField);
