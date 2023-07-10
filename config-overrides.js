module.exports = function override(config, env) {
    config.testMatch = [
        "**/Build/Sources/**/__tests__/**/*.[jt]s?(x)",
        "**/Build/Sources/**/?(*.)+(spec|test).[tj]s?(x)"
    ];

    return config;
}
