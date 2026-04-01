import { LitElement, html, svg, css } from 'lit';

export class EbWire extends LitElement {
    static properties = {
        x1: { type: Number },
        y1: { type: Number },
        x2: { type: Number },
        y2: { type: Number },
        srcUid: { type: String, attribute: 'src-uid' },
        tgtUid: { type: String, attribute: 'tgt-uid' },
        srcTerminal: { type: String, attribute: 'src-terminal' },
        tgtTerminal: { type: String, attribute: 'tgt-terminal' },
        srcModuleId: { type: Number, attribute: 'src-module-id' },
        tgtModuleId: { type: Number, attribute: 'tgt-module-id' },
    };

    static styles = css`
        :host {
            display: contents;
        }
    `;

    getPath() {
        const { x1, y1, x2, y2 } = this;
        const cy1 = y1 + 80;
        const cy2 = y2 - 80;
        return `M ${x1} ${y1} C ${x1} ${cy1}, ${x2} ${cy2}, ${x2} ${y2}`;
    }

    serialize() {
        return {
            src: { moduleId: this.srcModuleId, terminal: this.srcTerminal, uid: this.srcUid },
            tgt: { moduleId: this.tgtModuleId, terminal: this.tgtTerminal, uid: this.tgtUid },
        };
    }

    render() {
        return svg`
            <path
                d="${this.getPath()}"
                stroke="#4a90d9"
                stroke-width="2"
                fill="none"
                stroke-linecap="round"
            />
        `;
    }
}

customElements.define('eb-wire', EbWire);
