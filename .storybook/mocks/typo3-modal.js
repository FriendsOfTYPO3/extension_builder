// Storybook mock for @typo3/backend/modal.js
const Modal = {
    confirm: (title, content, severity, buttons) => {
        // In Storybook use the native browser confirm as a stand-in
        const ok = window.confirm(`[TYPO3 Modal] ${title}: ${content}`);
        if (ok && buttons) {
            const confirmBtn = buttons.find((b) => b.trigger);
            confirmBtn?.trigger?.();
        }
    },
    show: () => {},
    dismiss: () => {},
};

export default Modal;
