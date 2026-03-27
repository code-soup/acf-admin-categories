/**
 * Environment detection utility
 * Centralizes all environment and mode detection logic in one place
 */

// Production detection - either explicitly set NODE_ENV or using CLI args
const isProduction = process.env.NODE_ENV === 'production' || -1 === process.argv.indexOf('development');

// Watch mode detection
const isWatching = -1 !== process.argv.indexOf('serve');

/**
 * Environment information object
 */
module.exports = {
    // Environment modes
    isProduction,
    isDev: !isProduction,

    // Operation modes
    isWatching,

    // Helper for getting env-specific values
    getEnvSpecific: (prodValue, devValue) => isProduction ? prodValue : devValue
};