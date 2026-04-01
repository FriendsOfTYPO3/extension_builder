import { LitElement, html, css } from 'lit';
import './eb-layer.js';

export class EbWiringEditor extends LitElement {
    static properties = {
        smdUrl: { type: String, attribute: 'smd-url' },
        extensionName: { type: String, attribute: 'extension-name' },
        _loading: { state: true },
        _error: { state: true },
        _extensionData: { state: true },
    };

    static styles = css`
        :host {
            display: flex;
            flex-direction: column;
            width: 100%;
            height: 100%;
            font-family: sans-serif;
        }
        .toolbar {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            background: #f0f0f0;
            border-bottom: 1px solid #ccc;
        }
        button {
            padding: 4px 12px;
            cursor: pointer;
        }
        eb-layer {
            flex: 1;
        }
        .error {
            color: red;
            padding: 8px;
        }
        .loading {
            padding: 20px;
            color: #666;
        }
    `;

    constructor() {
        super();
        this.smdUrl = '/typo3/ajax/extensionBuilder/wireEditor';
        this.extensionName = '';
        this._loading = false;
        this._error = null;
        this._extensionData = null;
    }

    async connectedCallback() {
        super.connectedCallback();
        if (this.extensionName) {
            await this.load();
        }
    }

    async load() {
        this._loading = true;
        this._error = null;
        try {
            const response = await fetch(this.smdUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ method: 'listWirings', params: { name: this.extensionName } }),
            });
            const data = await response.json();
            if (data.error) throw new Error(data.error);
            this._extensionData = data.result ?? data;
            await this.updateComplete;
            this._populateLayer();
        } catch (e) {
            this._error = e.message;
        } finally {
            this._loading = false;
        }
    }

    _populateLayer() {
        const layer = this.shadowRoot.querySelector('eb-layer');
        if (!layer || !this._extensionData) return;
        const modules = this._extensionData.modules ?? [];
        const wires = this._extensionData.wires ?? [];
        layer.addContainers(modules);
        if (wires.length > 0) {
            layer.addWires(wires, modules);
        }
    }

    async save() {
        const layer = this.shadowRoot.querySelector('eb-layer');
        if (!layer) return;

        const { modules, wires } = layer.serialize();
        const working = JSON.stringify({
            modules,
            wires,
            properties: this._extensionData?.properties ?? {},
        });

        try {
            const response = await fetch(this.smdUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    method: 'saveWiring',
                    params: { name: this.extensionName, working },
                }),
            });
            const data = await response.json();
            if (data.error) throw new Error(data.error);
        } catch (e) {
            this._error = e.message;
        }
    }

    render() {
        return html`
            <div class="toolbar">
                <button @click="${this.save}">Save</button>
                <button @click="${this.load}">Reload</button>
                ${this._error ? html`<span class="error">${this._error}</span>` : ''}
            </div>
            ${this._loading
                ? html`<div class="loading">Loading...</div>`
                : html`<eb-layer></eb-layer>`
            }
        `;
    }
}

customElements.define('eb-wiring-editor', EbWiringEditor);
