import { LitElement, html, css } from 'lit';
import './eb-terminal.js';
import { renderFields } from './render-fields.js';
import { modelObjectModule } from './config/modelObject.js';

export class EbContainer extends LitElement {
    static properties = {
        moduleId: { type: Number, attribute: 'module-id' },
        posX: { type: Number, attribute: 'pos-x' },
        posY: { type: Number, attribute: 'pos-y' },
        moduleData: { type: Object },
        _name: { state: true },
    };

    static styles = css`
        :host {
            display: block;
            position: absolute;
            min-width: 160px;
            background: var(--eb-container-bg, #fff);
            border: 1px solid var(--eb-border-color-dark, #aaa);
            border-radius: 4px;
            box-shadow: 2px 2px 6px rgba(0,0,0,0.2);
            user-select: none;
            cursor: grab;
        }
        :host(:active) {
            cursor: grabbing;
        }
        .header {
            background: var(--eb-container-header-bg, #3a6ea5);
            color: var(--eb-container-header-text, #fff);
            padding: 6px 10px;
            font-weight: bold;
            font-size: 13px;
            border-radius: 3px 3px 0 0;
            position: relative;
        }
        .body {
            padding: 8px 10px;
            font-size: 12px;
        }
    `;

    constructor() {
        super();
        this.posX = 10;
        this.posY = 10;
        this.moduleData = {};
        this._name = '';
        this._dragging = false;
        this._dragOffsetX = 0;
        this._dragOffsetY = 0;
    }

    updated(changed) {
        if (changed.has('posX') || changed.has('posY')) {
            this.style.transform = `translate(${this.posX}px, ${this.posY}px)`;
        }
        if (changed.has('moduleData')) {
            this._name = this.moduleData?.value?.name ?? '';
            this._populateFromValue();
        }
    }

    connectedCallback() {
        super.connectedCallback();
        this.style.transform = `translate(${this.posX}px, ${this.posY}px)`;
        this.addEventListener('pointerdown', this._onPointerDown.bind(this));
        this.addEventListener('pointermove', this._onPointerMove.bind(this));
        this.addEventListener('pointerup', this._onPointerUp.bind(this));
    }

    _onPointerDown(e) {
        if (e.target.tagName === 'EB-TERMINAL') return;
        if (e.target.tagName === 'EB-INPLACE-EDIT') return;
        e.preventDefault();
        this._dragging = true;
        this._dragOffsetX = e.clientX - this.posX;
        this._dragOffsetY = e.clientY - this.posY;
        this.setPointerCapture(e.pointerId);
    }

    _onPointerMove(e) {
        if (!this._dragging) return;
        this.posX = e.clientX - this._dragOffsetX;
        this.posY = e.clientY - this._dragOffsetY;
        this.style.transform = `translate(${this.posX}px, ${this.posY}px)`;
        this.dispatchEvent(new CustomEvent('container-moved', {
            bubbles: true,
            composed: true,
            detail: { moduleId: this.moduleId, x: this.posX, y: this.posY },
        }));
    }

    _onPointerUp(e) {
        if (!this._dragging) return;
        this._dragging = false;
        this.releasePointerCapture(e.pointerId);
    }

    _onNameChange(e) {
        this._name = e.detail.value;
    }

    get _fields() {
        return modelObjectModule.container.fields;
    }

    _populateFromValue() {
        const value = this.moduleData?.value ?? {};
        const body = this.shadowRoot?.querySelector('.body');
        if (!body) return;
        Array.from(body.children).forEach(el => {
            const name = el.getAttribute('name');
            if (name !== null && value[name] !== undefined && typeof el.setValue === 'function') {
                el.setValue(value[name]);
            }
        });
    }

    _collectValues() {
        const value = { name: this._name ?? '' };
        const body = this.shadowRoot?.querySelector('.body');
        if (body) {
            Array.from(body.children).forEach(el => {
                const name = el.getAttribute('name');
                if (name !== null && typeof el.getValue === 'function') {
                    value[name] = el.getValue();
                }
            });
        }
        return value;
    }

    serialize() {
        return {
            config: { position: [this.posX, this.posY] },
            value: this._collectValues(),
        };
    }

    render() {
        return html`
            <div class="header">
                <eb-terminal
                    type="input"
                    terminal-id="SOURCES"
                    uid="${this.moduleData?.value?.objectsettings?.uid ?? ''}">
                </eb-terminal>
                <eb-inplace-edit
                    name="name"
                    .value="${this._name || 'New Model Object'}"
                    @inplace-change="${this._onNameChange}">
                </eb-inplace-edit>
            </div>
            <div class="body">
                ${renderFields(this._fields.slice(1))}
            </div>
        `;
    }
}

customElements.define('eb-container', EbContainer);
