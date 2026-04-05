import Notification from '@typo3/backend/notification.js';
import Modal from '@typo3/backend/modal.js';
import Severity from '@typo3/backend/severity.js';
import { html } from 'lit';
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
    if (!editor) {
        return;
    }

    // Save
    document.getElementById('WiringEditor-saveButton-button')?.addEventListener('click', (e) => {
        e.preventDefault();
        editor.save();
    });

    // New
    document.getElementById('WiringEditor-newButton-button')?.addEventListener('click', (e) => {
        e.preventDefault();
        editor.reset();
    });

    // Advanced toggle
    document.getElementById('toggleAdvancedOptions')?.addEventListener('click', (e) => {
        e.preventDefault();
        editor._toggleAdvancedMode();
    });

    // Open → Liste laden → TYPO3 Modal → Extension auswählen
    document.getElementById('WiringEditor-loadButton-button')?.addEventListener('click', async (e) => {
        e.preventDefault();
        const smdUrl = editor.getAttribute('smd-url');
        const resp = await fetch(smdUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ method: 'listWirings', params: {} }),
        });
        const data = await resp.json();
        const extensions = data.result ?? [];
        if (extensions.length === 0) {
            Notification.info('Open Extension', 'No extensions found.');
            return;
        }

        const select = document.createElement('select');
        select.size = 8;
        select.style.minWidth = '240px';
        extensions.forEach((ext) => {
            const option = document.createElement('option');
            option.value = ext.name;
            option.textContent = ext.name;
            select.appendChild(option);
        });

        const modal = Modal.advanced({
            title: 'Open Extension',
            content: html``,
            severity: Severity.info,
            size: 'small',
            staticBackdrop: false,
            buttons: [
                {
                    text: 'Cancel',
                    btnClass: 'btn-default',
                    trigger: () => Modal.dismiss(),
                },
                {
                    text: 'Open',
                    btnClass: 'btn-primary',
                    active: true,
                    trigger: () => {
                        const name = modal.querySelector('.t3js-modal-body select')?.value;
                        Modal.dismiss();
                        if (name) {
                            editor.extensionName = name;
                            editor.load();
                        }
                    },
                },
            ],
            callback: (modalEl) => {
                const body = modalEl.querySelector('.t3js-modal-body');
                if (body) {
                    body.replaceChildren(select);
                    select.focus();
                }
            },
        });
    });

    // Backups → TYPO3 Modal → Backup auswählen → Restore
    document.getElementById('WiringEditor-backupsButton-button')?.addEventListener('click', async (e) => {
        e.preventDefault();
        const smdUrl = editor.getAttribute('smd-url');

        if (!editor.extensionName) {
            Notification.info('Restore backup', 'Please load an extension first.');
            return;
        }
        const working = editor._serializeWorking();
        if (!working) {
            Notification.info('Restore backup', 'No extension loaded.');
            return;
        }

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

        const select = document.createElement('select');
        select.size = Math.min(backups.length, 8);
        select.style.cssText = 'min-width:320px;display:block;margin-bottom:8px;';
        backups.forEach((b) => {
            const option = document.createElement('option');
            option.value = b.directory;
            option.textContent = b.label + '  (' + b.fileCount + ' files)';
            select.appendChild(option);
        });

        const backupModal = Modal.advanced({
            title: 'Restore backup',
            content: html``,
            severity: Severity.warning,
            staticBackdrop: false,
            buttons: [
                {
                    text: 'Cancel',
                    btnClass: 'btn-default',
                    trigger: () => Modal.dismiss(),
                },
                {
                    text: 'Restore',
                    btnClass: 'btn-danger',
                    trigger: async () => {
                        const backupDirectory = backupModal.querySelector('.t3js-modal-body select')?.value;
                        Modal.dismiss();
                        if (!backupDirectory) {
                            return;
                        }
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
                                            Notification.success(
                                                'Backup restored',
                                                result.success ?? 'Extension restored.'
                                            );
                                        }
                                    },
                                },
                            ]
                        );
                    },
                },
            ],
            callback: (modalEl) => {
                const body = modalEl.querySelector('.t3js-modal-body');
                if (body) {
                    const note = document.createElement('p');
                    note.textContent =
                        'Restoring a backup will overwrite all current extension files. The current state will be backed up first.';
                    note.className = 'text-danger';
                    body.replaceChildren(note, select);
                }
            },
        });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initEditor);
} else {
    initEditor();
}
