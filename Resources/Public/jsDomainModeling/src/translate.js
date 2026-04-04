/**
 * Looks up a translation key from the Extension Builder locallang strings
 * loaded by BuilderModuleController::setLocallangSettings() into
 * window.TYPO3.settings.extensionBuilder._LOCAL_LANG.
 *
 * Dots in keys are replaced with underscores by the PHP loader.
 * Falls back to the raw key if no translation is found.
 */
export function translate(key) {
    if (!key) return '';
    const k = key.replace(/\./g, '_');
    return window.TYPO3?.settings?.extensionBuilder?._LOCAL_LANG?.[k] ?? key;
}
