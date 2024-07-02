console.log("Jest config loaded");

module.exports = {
    testEnvironment: 'jsdom',
    roots: [
        "<rootDir>/Build/Sources"
    ],
    testMatch: [
        "**/__tests__/**/*.[jt]s?(x)",
        "**/?(*.)+(spec|test).[tj]s?(x)"
    ],
};
