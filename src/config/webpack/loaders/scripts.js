/**
 * JavaScript processing configuration
 * Note: Currently no JS files in project, but keeping loader for future use
 */

module.exports = (config, env) => ({
    test: /\.js$/,
    exclude: [/node_modules/],
    // No loaders needed - just pass through JS as-is
    type: 'javascript/auto',
});