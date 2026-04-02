import Notification from '@typo3/backend/notification.js';
import './styles/editor.css';
import './eb-terminal.js';
import './eb-wire.js';
import './eb-container.js';
import './eb-layer.js';
import './eb-wiring-editor.js';
import './eb-field.js';
import './eb-string-field.js';
import './eb-select-field.js';
import './eb-boolean-field.js';
import './eb-hidden-field.js';
import './eb-textarea-field.js';
import './eb-inplace-edit.js';
import './eb-group.js';
import './eb-list-field.js';

function initEditor() {
    const editor = document.querySelector('eb-wiring-editor');
    if (!editor) return;

    // Save
    document.getElementById('WiringEditor-saveButton-button')
        ?.addEventListener('click', e => { e.preventDefault(); editor.save(); });

    // New
    document.getElementById('WiringEditor-newButton-button')
        ?.addEventListener('click', e => { e.preventDefault(); editor.reset(); });

    // Advanced toggle
    document.getElementById('toggleAdvancedOptions')
        ?.addEventListener('click', e => { e.preventDefault(); editor._toggleAdvancedMode(); });

    // Open → Liste laden → nativer Dialog → Extension auswählen
    document.getElementById('WiringEditor-loadButton-button')
        ?.addEventListener('click', async e => {
            e.preventDefault();
            const smdUrl = editor.getAttribute('smd-url');
            const resp = await fetch(smdUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ method: 'listWirings', params: {} }),
            });
            const data = await resp.json();
            const extensions = data.result ?? [];
            if (extensions.length === 0) { Notification.info('Open Extension', 'No extensions found.'); return; }

            // Dialog mit sicheren DOM-Methoden (kein innerHTML mit Nutzerdaten)
            const dialog = document.createElement('dialog');
            const heading = dialog.appendChild(document.createElement('h3'));
            heading.textContent = 'Open Extension';

            const select = dialog.appendChild(document.createElement('select'));
            select.size = 8;
            select.style.minWidth = '240px';
            extensions.forEach(ext => {
                const option = document.createElement('option');
                option.value = ext.name;
                option.textContent = ext.name;
                select.appendChild(option);
            });

            const form = dialog.appendChild(document.createElement('form'));
            form.method = 'dialog';
            const openBtn = form.appendChild(document.createElement('button'));
            openBtn.type = 'submit';
            openBtn.textContent = 'Open';
            const cancelBtn = form.appendChild(document.createElement('button'));
            cancelBtn.type = 'button';
            cancelBtn.textContent = 'Cancel';
            cancelBtn.addEventListener('click', () => dialog.close('cancel'));

            document.body.appendChild(dialog);
            dialog.showModal();
            dialog.addEventListener('close', () => {
                if (dialog.returnValue !== 'cancel') {
                    editor.extensionName = select.value;
                    editor.load();
                }
                dialog.remove();
            });
        });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initEditor);
} else {
    initEditor();
}
