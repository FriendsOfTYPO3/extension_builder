import { getExtensionKeyIsValid } from './getExtensionKeyIsValid'; // replace with actual file name

describe('getExtensionKeyIsValid', () => {
    it('returns false for key length less than 3 (without underscores)', () => {
        expect(getExtensionKeyIsValid('a_')).toBe(false);
    });

    it('returns false for key length more than 30 (without underscores)', () => {
        expect(getExtensionKeyIsValid('a'.repeat(31) + '_')).toBe(false);
    });

    it('returns false for keys beginning with forbidden prefixes', () => {
        const forbiddenPrefixes = ['tx', 'user_', 'pages', 'tt_', 'sys_', 'ts_language', 'csh_'];
        forbiddenPrefixes.forEach(prefix => {
            expect(getExtensionKeyIsValid(prefix + 'validKey')).toBe(false);
        });
    });

    it('returns false for keys beginning or ending with a digit or underscore', () => {
        expect(getExtensionKeyIsValid('_invalidKey')).toBe(false);
        expect(getExtensionKeyIsValid('invalidKey_')).toBe(false);
        expect(getExtensionKeyIsValid('1invalidKey')).toBe(false);
    });

    it('returns false for keys containing characters other than lowercase letters, digits, and underscore', () => {
        expect(getExtensionKeyIsValid('invalidKey$')).toBe(false);
        expect(getExtensionKeyIsValid('InvalidKey')).toBe(false);
    });

    it('returns true for valid keys', () => {
        expect(getExtensionKeyIsValid('valid_key')).toBe(true);
        expect(getExtensionKeyIsValid('validkey')).toBe(true);
        expect(getExtensionKeyIsValid('valid_key123')).toBe(true);
        expect(getExtensionKeyIsValid('a'.repeat(29) + '_a')).toBe(true);
    });

    it('returns false for keys containing uppercase letters', () => {
        expect(getExtensionKeyIsValid('InvalidKey')).toBe(false);
        expect(getExtensionKeyIsValid('inValid')).toBe(false);
        expect(getExtensionKeyIsValid('INVALid_key')).toBe(false);
        expect(getExtensionKeyIsValid('valid_key')).toBe(true);
    });
});
