import { LitElement, html, css } from 'lit';
import { repeat } from 'lit/directives/repeat.js';
import { buttonStyles } from './styles/button-styles.js';

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
            align-items: center;
            gap: 4px;
            margin-bottom: 4px;
        }
        .item-content { flex: 1; }
        .add-btn { margin-top: 4px; }
    `];

    constructor() {
        super();
        this.sortable = true;
        this.addLabel = 'add';
        this._items = [];
    }

    _addItem() {
        const uid = parseInt(Date.now() * Math.random());
        this._items = [...this._items, { uid, value: null }];
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
        return this._items.map(item => item.value);
    }

    setValue(arr) {
        if (!Array.isArray(arr)) return;
        this._items = arr.map((value, i) => ({ uid: i, value }));
    }

    render() {
        return html`
            ${repeat(this._items, item => item.uid, (item, index) => html`
                <div class="item-row">
                    <div class="item-content">
                        <slot name="item-${item.uid}"></slot>
                    </div>
                    ${this.sortable ? html`
                        <button class="btn btn-default btn-sm" @click="${() => this._moveUp(index)}" title="Move up">
                            ${_svgIcon('actions-caret-up')}
                        </button>
                        <button class="btn btn-default btn-sm" @click="${() => this._moveDown(index)}" title="Move down">
                            ${_svgIcon('actions-caret-down')}
                        </button>
                    ` : ''}
                    <button class="btn btn-default btn-sm" @click="${() => this._removeItem(index)}" title="Remove">
                        ${_svgIcon('actions-delete')}
                    </button>
                </div>
            `)}
            <button class="btn btn-default btn-sm add-btn" @click="${this._addItem}">${this.addLabel}</button>
        `;
    }
}

customElements.define('eb-list-field', EbListField);
