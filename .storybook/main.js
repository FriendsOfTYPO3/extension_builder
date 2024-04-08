/** @type { import('@storybook/react-webpack5').StorybookConfig } */
const config = {
  stories: [
    "../Build/Sources/**/*.mdx",
    "../Build/Sources/**/*.stories.@(js|jsx|mjs|ts|tsx)",
  ],
  addons: [
    "@storybook/preset-create-react-app",
    "@storybook/addon-onboarding",
    "@storybook/addon-links",
    "@storybook/addon-essentials",
    "@chromatic-com/storybook",
    "@storybook/addon-interactions",
  ],
  framework: {
    name: "@storybook/react-webpack5",
    options: {},
  },
  docs: {
    autodocs: "tag",
  },
  staticDirs: ["../public"],
    webpackFinal: async (config, { configType }) => {
        // `configType` hat den Wert 'DEVELOPMENT' oder 'PRODUCTION'
        // Du kannst verschiedene Konfigurationen für production und development Mode haben.

        // JSX- und Babel-Konfiguration:
        config.module.rules.push({
            test: /\.(js|jsx)$/,
            exclude: /node_modules/,
            use: [{
                loader: 'babel-loader',
                options: {
                    presets: ['@babel/preset-env', '@babel/preset-react']
                }
            }]
        });

        // Rückgabe der finalen Konfiguration
        return config;
    },
};
export default config;
