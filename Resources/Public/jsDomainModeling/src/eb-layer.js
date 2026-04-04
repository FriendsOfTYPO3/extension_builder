import { LitElement, html, css, svg } from 'lit';
import './eb-container.js';
import './eb-wire.js';

/**
 * Canvas layer that hosts draggable containers and SVG wire overlays.
 *
 * Manages the full list of `eb-container` instances and `eb-wire` paths.
 * Listens for `terminal-connect` events to create wires and
 * `container-moved` events to update wire endpoint positions.
 *
 * @element eb-layer
 */
export class EbLayer extends LitElement {
    static properties = {
        _wires: { state: true },
        _containers: { state: true },
        _drawingWire: { state: true },
        _tempWire: { state: true },
    };

    static styles = css`
        :host {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            width: 100%;
            overflow: hidden;
            background: var(--bs-body-bg, #fff);
        }
        #canvas {
            position: relative;
            flex: 1;
            width: 100%;
            overflow: hidden;

        }
        #wire-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        .wire-path {
            stroke: var(--eb-wire-color, #4a90d9);
        }
        .wire-temp-path {
            stroke: var(--eb-wire-temp-color, #aaa);
        }
    `;

    constructor() {
        super();
        this._wires = [];
        this._containers = [];
        this._drawingWire = null;
        this._tempWire = null;
    }

    connectedCallback() {
        super.connectedCallback();
        this.addEventListener('terminal-connect', this._onTerminalConnect.bind(this));
        this.addEventListener('container-moved', this._onContainerMoved.bind(this));
    }

    _onTerminalConnect(e) {
        const { terminalId, uid, sourceEl } = e.detail;

        if (!this._drawingWire) {
            this._drawingWire = { terminalId, uid, sourceEl };
            return;
        }

        const src = this._drawingWire;
        const tgt = { terminalId, uid, sourceEl };

        // Determine source (output/relation) and target (input/SOURCES)
        const isTargetSources = tgt.terminalId === 'SOURCES';
        const srcInfo = isTargetSources ? src : tgt;
        const tgtInfo = isTargetSources ? tgt : src;

        const srcContainer = srcInfo.sourceEl.closest('eb-container') ?? srcInfo.sourceEl.getRootNode()?.host;
        const tgtContainer = tgtInfo.sourceEl.closest('eb-container') ?? tgtInfo.sourceEl.getRootNode()?.host;

        if (!srcContainer || !tgtContainer || srcContainer === tgtContainer) {
            this._drawingWire = null;
            this._tempWire = null;
            return;
        }

        const srcModuleId = parseInt(srcContainer.getAttribute('module-id') ?? '0');
        const tgtModuleId = parseInt(tgtContainer.getAttribute('module-id') ?? '0');

        const pos = this._getWirePositions(srcInfo.sourceEl, tgtInfo.sourceEl);
        this._wires = [...this._wires, {
            id: `wire-${Date.now()}`,
            srcTerminal: srcInfo.terminalId,
            tgtTerminal: tgtInfo.terminalId,
            srcUid: srcInfo.uid,
            tgtUid: tgtInfo.uid,
            srcModuleId,
            tgtModuleId,
            ...pos,
        }];

        this._drawingWire = null;
        this._tempWire = null;
    }

    _onContainerMoved(e) {
        this._updateWirePositions();
    }

    _updateWirePositions() {
        // Re-read terminal positions after container move
        this.updateComplete.then(() => {
            const updatedWires = this._wires.map(wire => {
                const srcEl = this._findTerminalEl(wire.srcTerminal, wire.srcModuleId);
                const tgtEl = this._findTerminalEl(wire.tgtTerminal, wire.tgtModuleId);
                if (!srcEl || !tgtEl) return wire;
                return { ...wire, ...this._getWirePositions(srcEl, tgtEl) };
            });
            this._wires = updatedWires;
        });
    }

    _findTerminalEl(terminalId, moduleId) {
        const container = this.shadowRoot.querySelector(`eb-container[module-id="${moduleId}"]`);
        if (!container) return null;
        return container.shadowRoot?.querySelector(`eb-terminal[terminal-id="${terminalId}"]`) ?? null;
    }

    _getWirePositions(srcEl, tgtEl) {
        const layerRect = this.getBoundingClientRect();
        const srcRect = srcEl.getBoundingClientRect();
        const tgtRect = tgtEl.getBoundingClientRect();
        return {
            x1: srcRect.left - layerRect.left + srcRect.width / 2,
            y1: srcRect.top - layerRect.top + srcRect.height / 2,
            x2: tgtRect.left - layerRect.left + tgtRect.width / 2,
            y2: tgtRect.top - layerRect.top + tgtRect.height / 2,
        };
    }

    addContainer(moduleData) {
        const nextId = this._containers.length;
        this._containers = [...this._containers, {
            moduleId: nextId,
            posX: 20 + nextId * 20,
            posY: 20 + nextId * 20,
            moduleData,
        }];
    }

    addContainers(modules) {
        this._containers = modules.map((mod, index) => ({
            moduleId: index,
            posX: mod.config?.position?.[0] ?? 10 + index * 180,
            posY: mod.config?.position?.[1] ?? 10,
            moduleData: mod,
        }));
    }

    addWires(wires, modules) {
        this.updateComplete.then(() => {
            this._wires = wires.map(wire => {
                const srcEl = this._findTerminalEl(wire.src.terminal, wire.src.moduleId);
                const tgtEl = this._findTerminalEl(wire.tgt.terminal, wire.tgt.moduleId);
                const pos = srcEl && tgtEl ? this._getWirePositions(srcEl, tgtEl) : { x1: 0, y1: 0, x2: 0, y2: 0 };
                return {
                    id: `wire-${wire.src.moduleId}-${wire.src.terminal}`,
                    srcTerminal: wire.src.terminal,
                    tgtTerminal: wire.tgt.terminal,
                    srcUid: wire.src.uid,
                    tgtUid: wire.tgt.uid,
                    srcModuleId: wire.src.moduleId,
                    tgtModuleId: wire.tgt.moduleId,
                    ...pos,
                };
            });
        });
    }

    serialize() {
        const containers = Array.from(
            this.shadowRoot.querySelectorAll('eb-container')
        );
        const modules = containers.map(c => c.serialize());
        const wires = this._wires.map(w => ({
            src: { moduleId: w.srcModuleId, terminal: w.srcTerminal, uid: w.srcUid },
            tgt: { moduleId: w.tgtModuleId, terminal: w.tgtTerminal, uid: w.tgtUid },
        }));
        return { modules, wires };
    }

    render() {
        return html`
            <div id="canvas">
                ${this._containers.map(c => html`
                    <eb-container
                        module-id="${c.moduleId}"
                        pos-x="${c.posX}"
                        pos-y="${c.posY}"
                        .moduleData="${c.moduleData}">
                    </eb-container>
                `)}
                <svg id="wire-overlay">
                    ${this._wires.map(w => svg`
                        <path
                            class="wire-path"
                            d="M ${w.x1} ${w.y1} C ${w.x1} ${w.y1 + 80}, ${w.x2} ${w.y2 - 80}, ${w.x2} ${w.y2}"
                            stroke-width="2"
                            fill="none"
                            stroke-linecap="round"
                        />
                    `)}
                    ${this._tempWire ? svg`
                        <path
                            class="wire-temp-path"
                            d="M ${this._tempWire.x1} ${this._tempWire.y1} L ${this._tempWire.x2} ${this._tempWire.y2}"
                            stroke-width="1.5"
                            stroke-dasharray="4 4"
                            fill="none"
                        />
                    ` : ''}
                </svg>
            </div>
        `;
    }
}

customElements.define('eb-layer', EbLayer);
