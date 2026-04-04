import { LitElement, html, css } from 'lit';

/**
 * Visual connection point (port) on a domain model container.
 *
 * Renders as a small coloured circle. When clicked, fires `terminal-connect`
 * so `EbLayer` can draw a wire between two terminals. The `type` attribute
 * controls colour and position: `input` (green, top) or `output` (red).
 *
 * @element eb-terminal
 * @fires terminal-connect - On pointerdown with `{ terminalId, uid, sourceEl }` detail
 */
export class EbTerminal extends LitElement {
    static properties = {
        type: { type: String },
        terminalId: { type: String, attribute: 'terminal-id' },
        uid: { type: String },
    };

    static styles = css`
        :host {
            display: block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--eb-terminal-default, #4a90d9);
            border: 2px solid var(--eb-terminal-default-border, #2c5f8a);
            cursor: crosshair;
            position: absolute;
        }
        :host([type="input"]) {
            background: var(--eb-terminal-input, #5cb85c);
            border-color: var(--eb-terminal-input-border, #3d7a3d);
            top: -8px;
            left: 50%;
            transform: translateX(-50%);
        }
        :host([type="output"]) {
            background: var(--eb-terminal-output, #d9534f);
            border-color: var(--eb-terminal-output-border, #8a2c2c);
        }
        :host(:hover) {
            transform: translateX(-50%) scale(1.3);
        }
    `;

    connectedCallback() {
        super.connectedCallback();
        this.addEventListener('pointerdown', this._onPointerDown.bind(this));
    }

    updated(changed) {
        if (changed.has('type')) {
            const labels = { input: 'Input terminal', output: 'Output terminal' };
            this.setAttribute('aria-label', labels[this.type] ?? 'Terminal');
            this.setAttribute('role', 'img');
        }
    }

    _onPointerDown(e) {
        e.stopPropagation();
        this.dispatchEvent(new CustomEvent('terminal-connect', {
            bubbles: true,
            composed: true,
            detail: {
                terminalId: this.terminalId,
                uid: this.uid,
                sourceEl: this,
            },
        }));
    }

    getCenter() {
        const rect = this.getBoundingClientRect();
        const layerRect = this._getLayerRect();
        return {
            x: rect.left - layerRect.left + rect.width / 2,
            y: rect.top - layerRect.top + rect.height / 2,
        };
    }

    _getLayerRect() {
        let el = this.parentElement;
        while (el && el.tagName !== 'EB-LAYER') {
            el = el.parentElement;
        }
        return el ? el.getBoundingClientRect() : { left: 0, top: 0 };
    }

    render() {
        return html``;
    }
}

customElements.define('eb-terminal', EbTerminal);
