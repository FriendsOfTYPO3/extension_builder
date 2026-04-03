import { LitElement, html, css } from 'lit';
import { buttonStyles } from './styles/button-styles.js';
import Notification from '@typo3/backend/notification.js';
import Modal from '@typo3/backend/modal.js';
import Severity from '@typo3/backend/severity.js';
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
import { renderFieldDef, renderFields } from './render-fields.js';

export class EbWiringEditor extends LitElement {
    static properties = {
        smdUrl: { type: String, attribute: 'smd-url' },
        extensionName: { type: String, attribute: 'extension-name' },
        initialWarnings: { type: Array, attribute: 'initial-warnings' },
        _loading: { state: true },
        _extensionData: { state: true },
        _advancedMode: { state: true },
        _leftCollapsed: { state: true },
    };

    static styles = [buttonStyles, css`
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
            background: var(--eb-toolbar-bg, #f0f0f0);
            border-bottom: 1px solid var(--eb-border-color, #ccc);
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
            border-right: 1px solid var(--eb-border-color, #ccc);
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
        .loading {
            padding: 20px;
            color: var(--eb-text-muted, #666);
        }
        .advanced-mode ::slotted([advanced]),
        .advanced-mode [advanced] {
            display: block;
        }
    `];

    constructor() {
        super();
        this.smdUrl = '';
        this.extensionName = '';
        this.initialWarnings = [];
        this._loading = false;
        this._extensionData = null;
        this._advancedMode = false;
        this._leftCollapsed = false;
    }

    async connectedCallback() {
        super.connectedCallback();
        if (this.initialWarnings?.length > 0) {
            this.initialWarnings.forEach(msg => Notification.warning('Configuration', msg));
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
        if (!this.extensionName) return;
        this._loading = true;
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
        } catch (e) {
            Notification.error('Load failed', e.message);
        } finally {
            this._loading = false;
        }
        // _loading is now false, so eb-layer is re-rendered into the DOM.
        // Wait for that render to complete before populating it.
        if (this._extensionData) {
            await this.updateComplete;
            this._populateLayer();
            this._populateProperties();
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

    _serializeWorking() {
        const layer = this.shadowRoot.querySelector('eb-layer');
        if (!layer) return null;
        const { modules, wires } = layer.serialize();
        return JSON.stringify({ modules, wires, properties: this._collectProperties() });
    }

    async _fetchPreviewChanges(working) {
        try {
            const response = await fetch(this.smdUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ method: 'previewChanges', params: { name: this.extensionName, working } }),
            });
            return await response.json();
        } catch {
            return null;
        }
    }

    _buildPreviewContent(preview) {
        const lines = [];
        if (preview.modifiedFiles?.length) {
            lines.push('Files that will be modified:\n');
            for (const file of preview.modifiedFiles) {
                let fileLine = '  • ' + file.path;
                if (file.renamedTo) fileLine += '  →  ' + file.renamedTo;
                lines.push(fileLine + '\n');
                for (const c of (file.changes ?? [])) {
                    if (c.type === 'renamed') lines.push('      ↻ ' + c.from + ' → ' + c.to + '\n');
                    else if (c.type === 'removed') lines.push('      − ' + c.method + ' (removed)\n');
                    else if (c.type === 'added') lines.push('      + ' + c.method + ' (added)\n');
                }
            }
        }
        if (preview.deletedFiles?.length) {
            lines.push('\nFiles that will be deleted:\n');
            for (const f of preview.deletedFiles) lines.push('  • ' + f + '\n');
        }
        const pre = document.createElement('pre');
        pre.style.cssText = 'font-size:0.9em;max-height:60vh;overflow:auto;white-space:pre-wrap;';
        pre.textContent = lines.join('');
        return pre;
    }

    async save(extraParams = {}) {
        const working = this._serializeWorking();
        if (!working) return;

        if (!extraParams._previewDone) {
            const preview = await this._fetchPreviewChanges(working);
            if (preview?.hasChanges) {
                Modal.confirm(
                    'Review changes before generating',
                    this._buildPreviewContent(preview),
                    Severity.warning,
                    [
                        { text: 'Cancel', btnClass: 'btn-default', trigger: () => Modal.dismiss() },
                        {
                            text: 'Generate',
                            btnClass: 'btn-warning',
                            trigger: () => { Modal.dismiss(); this.save({ ...extraParams, _previewDone: true }); },
                        },
                    ]
                );
                return;
            }
        }

        const response = await fetch(this.smdUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                method: 'saveWiring',
                params: { name: this.extensionName, working, ...extraParams },
            }),
        });
        const data = await response.json();

        if (data.errors?.length) {
            data.errors.forEach(msg => Notification.error('Validation error', msg));
            return;
        }
        if (data.error) {
            Notification.error('Error', data.error);
            return;
        }
        if (data.confirm) {
            Modal.confirm(
                'Warning',
                data.confirm,
                Severity.warning,
                [
                    { text: 'Cancel', btnClass: 'btn-default', trigger: () => Modal.dismiss() },
                    {
                        text: 'Save anyway',
                        btnClass: 'btn-warning',
                        trigger: () => {
                            Modal.dismiss();
                            this._saveWithConfirmation(data.confirmFieldName);
                        },
                    },
                ]
            );
            return;
        }
        if (data.warning) {
            Notification.warning('Warning', data.warning);
        }
        (data.warnings ?? []).forEach(msg => Notification.warning('Roundtrip warning', msg));
        if (data.success) {
            Notification.success('Saved', data.success);
            (data.installationHints ?? []).forEach(hint => Notification.info('Next steps', hint));
        }
    }

    _saveWithConfirmation(fieldName) {
        this.save({ [fieldName]: true });
    }

    reset() {
        this.extensionName = '';
        this._extensionData = null;
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

    render() {
        return html`
            <div class="toolbar">
                <button class="btn btn-primary btn-sm" @click="${this.addModelObject}">+ Model Object</button>
            </div>
            <div class="content ${this._advancedMode ? 'advanced-mode' : ''}">
                <div class="left-panel ${this._leftCollapsed ? 'collapsed' : ''}">
                    <div class="left-panel-header">
                        <button class="btn btn-default btn-sm" @click="${this._toggleLeftPanel}" title="Toggle panel">☰</button>
                    </div>
                    ${renderFields(extensionPropertiesFields)}
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
