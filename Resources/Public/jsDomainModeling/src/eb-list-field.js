import { LitElement, html, css, nothing } from 'lit';
import { repeat } from 'lit/directives/repeat.js';
import { buttonStyles } from './styles/button-styles.js';
import { renderFieldDef } from './render-fields.js';

function _iconUrl(name) {
    const base = window.TYPO3?.settings?.extensionBuilder?.publicResourceWebPath?.core ?? '';
    return base ? `${base}Icons/T3Icons/sprites/actions.svg#${name}` : '';
}

function _svgIcon(name) {
    const url = _iconUrl(name);
    if (!url) return html`<span>${name}</span>`;
    return html`
        <svg width="16" height="16" aria-hidden="true">
            <use href="${url}"></use>
        </svg>
    `;
}

export class EbListField extends LitElement {
    static properties = {
        name: { type: String },
        sortable: { type: Boolean },
        addLabel: { type: String, attribute: 'add-label' },
        elementType: { type: String, attribute: 'element-type' },
        _items: { state: true },
    };

    static styles = [buttonStyles, css`
        :host { display: block; }
        .item-row {
            display: flex;
            align-items: flex-start;
            gap: 4px;
            margin-bottom: 4px;
        }
        .item-content { flex: 1; }
        .item-actions {
            display: flex;
            flex-direction: column;
            gap: 2px;
            padding-top: 2px;
        }
        .add-btn { margin-top: 4px; }
    `];

    constructor() {
        super();
        this.sortable = true;
        this.addLabel = 'add';
        this._items = [];
    }

    get _elementTypeDef() {
        try { return JSON.parse(this.elementType || 'null'); }
        catch { return null; }
    }

    _addItem() {
        const uid = Date.now() + Math.floor(Math.random() * 1000);
        this._items = [...this._items, { uid }];
        this._fireUpdated();
    }

    _removeItem(index) {
        this._items = this._items.filter((_, i) => i !== index);
        this._fireUpdated();
    }

    _moveUp(index) {
        if (index === 0) return;
        const items = [...this._items];
        [items[index - 1], items[index]] = [items[index], items[index - 1]];
        this._items = items;
        this._fireUpdated();
    }

    _moveDown(index) {
        if (index >= this._items.length - 1) return;
        const items = [...this._items];
        [items[index], items[index + 1]] = [items[index + 1], items[index]];
        this._items = items;
        this._fireUpdated();
    }

    _fireUpdated() {
        this.dispatchEvent(new CustomEvent('list-updated', {
            bubbles: true,
            composed: true,
            detail: { value: this.getValue() },
        }));
    }

    getValue() {
        const containers = this.shadowRoot?.querySelectorAll('.item-content') ?? [];
        return Array.from(containers).map(container => {
            const group = container.querySelector('eb-group');
            if (group?.getValue) return group.getValue();
            const field = container.querySelector('[name]');
            return field?.getValue?.() ?? null;
        });
    }

    setValue(arr) {
        if (!Array.isArray(arr)) return;
        this._items = arr.map((_, i) => ({ uid: i }));
        this.updateComplete.then(() => {
            const containers = this.shadowRoot?.querySelectorAll('.item-content') ?? [];
            arr.forEach((value, index) => {
                if (!value) return;
                const container = containers[index];
                if (!container) return;
                const group = container.querySelector('eb-group');
                if (group?.setValue) { group.setValue(value); return; }
                const field = container.querySelector('[name]');
                field?.setValue?.(value);
            });
        });
    }

    render() {
        const def = this._elementTypeDef;
        return html`
            ${repeat(this._items, item => item.uid, (item, index) => html`
                <div class="item-row">
                    <div class="item-content">
                        ${def ? renderFieldDef(def) : nothing}
                    </div>
                    <div class="item-actions">
                        ${this.sortable ? html`
                            <button class="btn btn-default btn-sm" @click="${() => this._moveUp(index)}" title="Move up">
                                ${_svgIcon('actions-caret-up')}
                            </button>
                            <button class="btn btn-default btn-sm" @click="${() => this._moveDown(index)}" title="Move down">
                                ${_svgIcon('actions-caret-down')}
                            </button>
                        ` : nothing}
                        <button class="btn btn-default btn-sm" @click="${() => this._removeItem(index)}" title="Remove">
                            ${_svgIcon('actions-delete')}
                        </button>
                    </div>
                </div>
            `)}
            <button class="btn btn-default btn-sm add-btn" @click="${this._addItem}">${this.addLabel}</button>
        `;
    }
}

customElements.define('eb-list-field', EbListField);
