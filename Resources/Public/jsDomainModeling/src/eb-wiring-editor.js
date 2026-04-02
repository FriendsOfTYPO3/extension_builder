import { LitElement, html, css } from 'lit';
import './eb-layer.js';
import { extensionPropertiesFields } from './config/extensionProperties.js';
import { modelObjectModule } from './config/modelObject.js';

export class EbWiringEditor extends LitElement {
    static properties = {
        smdUrl: { type: String, attribute: 'smd-url' },
        extensionName: { type: String, attribute: 'extension-name' },
        _loading: { state: true },
        _error: { state: true },
        _extensionData: { state: true },
        _advancedMode: { state: true },
        _leftCollapsed: { state: true },
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
        .content {
            display: flex;
            flex-direction: row;
            flex: 1;
            overflow: hidden;
        }
        .left-panel {
            width: 280px;
            min-width: 120px;
            max-width: 600px;
            overflow-y: auto;
            border-right: 1px solid #ccc;
            padding: 8px;
            resize: horizontal;
        }
        .left-panel.collapsed {
            width: 20px;
            min-width: 20px;
            overflow: hidden;
            padding: 4px;
            resize: none;
        }
        .left-panel-header {
            display: flex;
            justify-content: flex-end;
        }
        .left-panel-header button {
            padding: 2px 6px;
        }
        .center-panel {
            flex: 1;
            overflow: hidden;
            position: relative;
        }
        eb-layer {
            width: 100%;
            height: 100%;
        }
        .error {
            color: red;
            padding: 8px;
        }
        .loading {
            padding: 20px;
            color: #666;
        }
        .advanced-mode ::slotted([advanced]),
        .advanced-mode [advanced] {
            display: block;
        }
    `;

    constructor() {
        super();
        this.smdUrl = '/typo3/ajax/extensionBuilder/wireEditor';
        this.extensionName = '';
        this._loading = false;
        this._error = null;
        this._extensionData = null;
        this._advancedMode = false;
        this._leftCollapsed = false;
    }

    async connectedCallback() {
        super.connectedCallback();
        this.addEventListener('field-updated', this._onFieldUpdated);
        if (this.extensionName) {
            await this.load();
        }
    }

    disconnectedCallback() {
        super.disconnectedCallback();
        this.removeEventListener('field-updated', this._onFieldUpdated);
    }

    _onFieldUpdated(e) {
        if (e.detail?.name !== 'targetVersion') return;
        const dependsOnField = this.querySelector('[name=dependsOn]');
        if (!dependsOnField) return;
        const current = dependsOnField.getValue?.() ?? dependsOnField.value ?? '';
        const updated = current
            .split('\n')
            .map(line => line.includes('typo3') ? `typo3 => ${e.detail.value}` : line)
            .join('\n');
        dependsOnField.setValue?.(updated);
    }

    _toggleAdvancedMode() {
        this._advancedMode = !this._advancedMode;
    }

    _toggleLeftPanel() {
        this._leftCollapsed = !this._leftCollapsed;
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
                <button @click="${this._toggleAdvancedMode}">Advanced</button>
                ${this._error ? html`<span class="error">${this._error}</span>` : ''}
            </div>
            <div class="content ${this._advancedMode ? 'advanced-mode' : ''}">
                <div class="left-panel ${this._leftCollapsed ? 'collapsed' : ''}">
                    <div class="left-panel-header">
                        <button @click="${this._toggleLeftPanel}">☰</button>
                    </div>
                    <slot name="properties"></slot>
                </div>
                <div class="center-panel">
                    ${this._loading
                        ? html`<div class="loading">Loading...</div>`
                        : html`<eb-layer></eb-layer>`
                    }
                </div>
            </div>
        `;
    }
}

customElements.define('eb-wiring-editor', EbWiringEditor);
