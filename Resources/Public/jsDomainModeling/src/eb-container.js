import { LitElement, html, css } from 'lit';
import './eb-terminal.js';
import { renderFields } from './render-fields.js';
import { modelObjectModule } from './config/modelObject.js';
import { formStyles } from './styles/form-styles.js';

/**
 * Draggable domain model object card in the wiring canvas.
 *
 * Renders a card with a colour-coded header (containing an `eb-terminal`
 * and an inline-editable name) and a body of auto-generated form fields.
 * Drag is handled via Pointer Events with capture. Fires `container-moved`
 * on every pointer-move while dragging.
 *
 * @element eb-container
 * @fires container-moved - During drag with `{ moduleId, x, y }` detail
 */
export class EbContainer extends LitElement {
    static properties = {
        moduleId: { type: Number, attribute: 'module-id' },
        posX: { type: Number, attribute: 'pos-x' },
        posY: { type: Number, attribute: 'pos-y' },
        moduleData: { type: Object },
        _name: { state: true },
    };

    static styles = [formStyles, css`
        :host {
            display: block;
            position: absolute;
            min-width: 160px;
            background: var(--bs-body-bg, #fff);
            color: var(--bs-body-color, #000);
            border: 1px solid var(--bs-border-color, #dee2e6);
            border-radius: var(--bs-border-radius, 0.25rem);
            box-shadow: var(--bs-box-shadow-sm, 2px 2px 6px rgba(0,0,0,0.15));
            user-select: none;
            cursor: grab;
        }
        :host(:active) {
            cursor: grabbing;
        }
        .card-header {
            background-color: var(--eb-brand-color, #FF8700);
            color: #fff;
            padding: 0.5rem 0.75rem;
            font-weight: bold;
            font-size: 13px;
            border-radius: calc(var(--bs-border-radius, 0.25rem) - 1px) calc(var(--bs-border-radius, 0.25rem) - 1px) 0 0;
            position: relative;
        }
        .card-body {
            padding: 0.5rem 0.75rem;
            font-size: 12px;
        }
    `];

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
        const interactive = e.composedPath().some(el => {
            if (!(el instanceof Element)) return false;
            const tag = el.tagName.toUpperCase();
            if (['BUTTON', 'INPUT', 'SELECT', 'TEXTAREA', 'A', 'EB-TERMINAL', 'EB-INPLACE-EDIT'].includes(tag)) return true;
            if (el.getAttribute?.('role') === 'button') return true;
            return false;
        });
        if (interactive) return;
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
        const body = this.shadowRoot?.querySelector('.card-body');
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
        const body = this.shadowRoot?.querySelector('.card-body');
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
            <div class="card-header">
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
            <div class="card-body">
                ${renderFields(this._fields.slice(1))}
            </div>
        `;
    }
}

customElements.define('eb-container', EbContainer);
