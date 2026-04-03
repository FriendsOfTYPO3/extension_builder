import Notification from '@typo3/backend/notification.js';
import Modal from '@typo3/backend/modal.js';
import Severity from '@typo3/backend/severity.js';
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
            openBtn.className = 'btn btn-primary';
            openBtn.textContent = 'Open';
            const cancelBtn = form.appendChild(document.createElement('button'));
            cancelBtn.type = 'button';
            cancelBtn.className = 'btn btn-default';
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


    // Backups → nativer Dialog → Backup auswählen → Restore
    document.getElementById('WiringEditor-backupsButton-button')
        ?.addEventListener('click', async e => {
            e.preventDefault();
            const smdUrl = editor.getAttribute('smd-url');

            if (!editor.extensionName) { Notification.info('Restore backup', 'Please load an extension first.'); return; }
            const working = editor._serializeWorking();
            if (!working) { Notification.info('Restore backup', 'No extension loaded.'); return; }

            const resp = await fetch(smdUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ method: 'listBackups', params: { name: editor.extensionName, working } }),
            });
            const data = await resp.json();
            const backups = data.result ?? [];
            if (backups.length === 0) {
                Notification.info('Restore backup', 'No backups found for this extension.');
                return;
            }

            const dialog = document.createElement('dialog');
            const heading = dialog.appendChild(document.createElement('h3'));
            heading.textContent = 'Restore backup';
            const note = dialog.appendChild(document.createElement('p'));
            note.textContent = 'Restoring a backup will overwrite all current extension files. The current state will be backed up first.';
            note.style.color = '#c00';

            const select = dialog.appendChild(document.createElement('select'));
            select.size = Math.min(backups.length, 8);
            select.style.cssText = 'min-width:320px;display:block;margin-bottom:8px;';
            backups.forEach(b => {
                const option = document.createElement('option');
                option.value = b.directory;
                const label = document.createTextNode(b.label + '  (' + b.fileCount + ' files)');
                option.appendChild(label);
                select.appendChild(option);
            });

            const form = dialog.appendChild(document.createElement('form'));
            form.method = 'dialog';
            const restoreBtn = form.appendChild(document.createElement('button'));
            restoreBtn.type = 'submit';
            restoreBtn.className = 'btn btn-danger';
            restoreBtn.style.marginRight = '8px';
            restoreBtn.textContent = 'Restore';
            const cancelBtn = form.appendChild(document.createElement('button'));
            cancelBtn.type = 'button';
            cancelBtn.className = 'btn btn-default';
            cancelBtn.textContent = 'Cancel';
            cancelBtn.addEventListener('click', () => dialog.close('cancel'));

            document.body.appendChild(dialog);
            dialog.showModal();
            dialog.addEventListener('close', async () => {
                dialog.remove();
                if (dialog.returnValue === 'cancel') return;
                const backupDirectory = select.value;
                Modal.confirm(
                    'Confirm restore',
                    'Restore backup from ' + backupDirectory + '? The current extension will be overwritten.',
                    Severity.warning,
                    [
                        { text: 'Cancel', btnClass: 'btn-default', trigger: () => Modal.dismiss() },
                        {
                            text: 'Restore',
                            btnClass: 'btn-danger',
                            trigger: async () => {
                                Modal.dismiss();
                                const restoreResp = await fetch(smdUrl, {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify({
                                        method: 'restoreBackup',
                                        params: { name: editor.extensionName, working, backupDirectory },
                                    }),
                                });
                                const result = await restoreResp.json();
                                if (result.error) {
                                    Notification.error('Restore failed', result.error);
                                } else {
                                    Notification.success('Backup restored', result.success ?? 'Extension restored.');
                                }
                            },
                        },
                    ]
                );
            });
        });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initEditor);
} else {
    initEditor();
}
