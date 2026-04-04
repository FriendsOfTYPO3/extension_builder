/**
 * Looks up a translation key from the Extension Builder locallang strings
 * loaded by BuilderModuleController::setLocallangSettings() into
 * window.TYPO3.settings.extensionBuilder._LOCAL_LANG.
 *
 * Dots in keys are replaced with underscores by the PHP loader.
 * Falls back to converting camelCase/snake_case to Title Case when
 * no translation entry exists.
 */
export function translate(key) {
    if (!key) return '';
    const k = key.replace(/\./g, '_');
    const lang = window.TYPO3?.settings?.extensionBuilder?._LOCAL_LANG;
    if (lang?.[k]) return lang[k];
    // Convert camelCase and snake_case to Title Case as readable fallback
    return key
        .replace(/_/g, ' ')
        .replace(/([A-Z])/g, ' $1')
        .trim()
        .replace(/\b\w/g, c => c.toUpperCase());
}
