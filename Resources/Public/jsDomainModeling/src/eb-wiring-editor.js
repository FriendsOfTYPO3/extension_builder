import { LitElement, html, css } from 'lit';
import './eb-layer.js';
import './eb-string-field.js';
import './eb-textarea-field.js';
import './eb-select-field.js';
import './eb-boolean-field.js';
import './eb-hidden-field.js';
import './eb-group.js';
import './eb-list-field.js';
import { extensionPropertiesFields } from './config/extensionProperties.js';
import { modelObjectModule } from './config/modelObject.js';

export class EbWiringEditor extends LitElement {
    static properties = {
        smdUrl: { type: String, attribute: 'smd-url' },
        extensionName: { type: String, attribute: 'extension-name' },
        initialWarnings: { type: Array, attribute: 'initial-warnings' },
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
            min-height: 0;
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
            min-height: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
        }
        eb-layer {
            flex: 1;
            min-height: 0;
            width: 100%;
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
        this.initialWarnings = [];
        this._loading = false;
        this._error = null;
        this._extensionData = null;
        this._advancedMode = false;
        this._leftCollapsed = false;
    }

    async connectedCallback() {
        super.connectedCallback();
        if (this.initialWarnings?.length > 0) {
            this._error = this.initialWarnings.join('<br>');
        }
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
                body: JSON.stringify({ method: 'listWirings', params: {} }),
            });
            const data = await response.json();
            if (data.error) throw new Error(data.error);
            const allExtensions = data.result ?? [];
            const found = allExtensions.find(e => e.name === this.extensionName);
            if (!found) throw new Error(`Extension "${this.extensionName}" not found`);
            this._extensionData = JSON.parse(found.working);
            await this.updateComplete;
            this._populateLayer();
            this._populateProperties();
        } catch (e) {
            this._error = e.message;
        } finally {
            this._loading = false;
        }
    }

    _populateProperties() {
        const props = this._extensionData?.properties ?? {};
        this.shadowRoot.querySelectorAll('[name]').forEach(field => {
            if (props[field.name] !== undefined && typeof field.setValue === 'function') {
                field.setValue(props[field.name]);
            }
        });
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

    _collectProperties() {
        const props = {};
        this.shadowRoot.querySelectorAll('[name]').forEach(field => {
            if (typeof field.getValue === 'function') {
                props[field.name] = field.getValue();
            }
        });
        return props;
    }

    async save() {
        const layer = this.shadowRoot.querySelector('eb-layer');
        if (!layer) return;

        const { modules, wires } = layer.serialize();
        const working = JSON.stringify({
            modules,
            wires,
            properties: this._collectProperties(),
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

    reset() {
        this.extensionName = '';
        this._extensionData = null;
        this._error = null;
        const layer = this.shadowRoot.querySelector('eb-layer');
        if (layer) {
            layer._containers = [];
            layer._wires = [];
        }
        this.shadowRoot.querySelectorAll('[name]').forEach(field => {
            field.setValue?.('');
        });
    }

    addModelObject() {
        const layer = this.shadowRoot.querySelector('eb-layer');
        if (layer) layer.addContainer(modelObjectModule.container);
    }

    _renderFieldDef(fieldDef) {
        const p = fieldDef.inputParams ?? {};
        const type = fieldDef.type;

        if (!type || p.className?.includes('hiddenField')) {
            return html`<eb-hidden-field name="${p.name}"></eb-hidden-field>`;
        }

        switch (type) {
            case 'string':
                return html`<eb-string-field
                    name="${p.name}"
                    label="${p.label ?? ''}"
                    ?required="${p.required}"
                    type-invite="${p.typeInvite ?? ''}"
                    .value="${p.value ?? ''}"
                ></eb-string-field>`;

            case 'text':
                return html`<eb-textarea-field
                    name="${p.name}"
                    label="${p.label ?? ''}"
                    .value="${p.value ?? ''}"
                ></eb-textarea-field>`;

            case 'select':
                return html`<eb-select-field
                    name="${p.name}"
                    label="${p.label ?? ''}"
                    .selectValues="${p.selectValues ?? []}"
                    .selectOptions="${p.selectOptions ?? []}"
                    .value="${p.value ?? (p.selectValues?.[0] ?? '')}"
                ></eb-select-field>`;

            case 'boolean':
                return html`<eb-boolean-field
                    name="${p.name}"
                    label="${p.label ?? ''}"
                    .value="${p.value ?? false}"
                ></eb-boolean-field>`;

            case 'group':
                return html`<eb-group
                    name="${p.name ?? ''}"
                    legend="${p.legend ?? ''}"
                    ?collapsible="${p.collapsible}"
                    ?collapsed="${p.collapsed}"
                >${this._renderFields(p.fields ?? [])}</eb-group>`;

            case 'list':
                return html`<eb-list-field
                    name="${p.name}"
                    ?sortable="${p.sortable}"
                    element-type="${JSON.stringify(p.elementType ?? {})}"
                ></eb-list-field>`;

            default:
                return html`<eb-string-field
                    name="${p.name}"
                    label="${p.label ?? ''}"
                ></eb-string-field>`;
        }
    }

    _renderFields(fields) {
        return fields.map(f => this._renderFieldDef(f));
    }

    render() {
        return html`
            <div class="toolbar">
                <button @click="${this.save}">Save</button>
                <button @click="${this.load}">Reload</button>
                <button @click="${this._toggleAdvancedMode}">Advanced</button>
                <button @click="${this.addModelObject}">+ Model Object</button>
                ${this._error ? html`<span class="error">${this._error}</span>` : ''}
            </div>
            <div class="content ${this._advancedMode ? 'advanced-mode' : ''}">
                <div class="left-panel ${this._leftCollapsed ? 'collapsed' : ''}">
                    <div class="left-panel-header">
                        <button @click="${this._toggleLeftPanel}">☰</button>
                    </div>
                    ${this._renderFields(extensionPropertiesFields)}
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
