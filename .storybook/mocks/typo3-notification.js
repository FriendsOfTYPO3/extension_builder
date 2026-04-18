// Storybook mock for @typo3/backend/notification.js
// In Storybook there is no TYPO3 backend, so we replace the notification API
// with no-ops that log to the console instead.
const Notification = {
    info: (title, message) => console.info('[TYPO3 Notification] Info:', title, message),
    success: (title, message) => console.info('[TYPO3 Notification] Success:', title, message),
    warning: (title, message) => console.warn('[TYPO3 Notification] Warning:', title, message),
    error: (title, message) => console.error('[TYPO3 Notification] Error:', title, message),
};

export default Notification;
