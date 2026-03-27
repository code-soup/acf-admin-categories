/**
 * Webpack optimization configuration
 */

module.exports = (config, env) => ({
    // Enable tree shaking
    usedExports: true,

    // Enable module concatenation for more efficient bundling (production only)
    concatenateModules: env.isProduction,

    // Ensure side effects are properly handled
    sideEffects: true,

    // Minification - use webpack's built-in minimizer
    minimize: env.isProduction,
});
