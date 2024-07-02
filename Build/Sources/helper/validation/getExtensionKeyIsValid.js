export function getExtensionKeyIsValid(extensionKey) {
    // Überprüft die Länge des Schlüssels (ohne Unterstriche)
    var keyLength = extensionKey.replace(/_/g, '').length;
    if (keyLength < 3 || keyLength > 30) {
        return false;
    }

    // Überprüft, ob der Schlüssel mit einem unerlaubten Präfix beginnt
    var forbiddenPrefixes = ['tx', 'user_', 'pages', 'tt_', 'sys_', 'ts_language', 'csh_'];
    var hasForbiddenPrefix = forbiddenPrefixes.some(prefix => extensionKey.startsWith(prefix));
    if (hasForbiddenPrefix) {
        return false;
    }

    // Überprüft, ob der Schlüssel mit einer Ziffer oder einem Unterstrich beginnt oder endet
    if (/^[0-9_]|_$/.test(extensionKey)) {
        return false;
    }

    // Überprüft, ob der Schlüssel nur die erlaubten Zeichen enthält (a-z, 0-9 und Unterstrich)
    if (!/^[a-z0-9_]*$/.test(extensionKey)) {
        return false;
    }

    // Überprüft, ob der Schlüssel Großbuchstaben enthält
    if (/[A-Z]/.test(extensionKey)) {
        return false;
    }

    // Wenn alle Überprüfungen bestanden sind, ist der Schlüssel gültig
    return true;
}
