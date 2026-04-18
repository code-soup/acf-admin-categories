#!/bin/bash
# Validate version consistency before git tag
# Usage: ./scripts/validate-version.sh

# Exit on error
set -e

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  Version Consistency Validation"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Extract versions (BSD-compatible grep)
PLUGIN_VERSION=$(grep -E '\* Version:' index.php | sed -E 's/.*Version:[[:space:]]*([0-9.]+).*/\1/')
RUN_VERSION=$(grep -E "'PLUGIN_VERSION'" run.php | sed -E "s/.*'PLUGIN_VERSION'[[:space:]]*=>[[:space:]]*'([0-9.]+)'.*/\1/")
CHANGELOG_VERSION=$(grep -E '\[[0-9.]+\]' CHANGELOG.md | head -1 | sed -E 's/.*\[([0-9.]+)\].*/\1/')

# Extract PHP versions (BSD-compatible grep)
PHP_INDEX=$(grep -E 'Requires PHP:' index.php | sed -E 's/.*Requires PHP:[[:space:]]*([0-9.]+).*/\1/')
PHP_COMPOSER=$(grep -E '"php":' composer.json | sed -E 's/.*"php":[[:space:]]*">=([0-9.]+)".*/\1/')
PHP_RUN=$(grep -E "'MIN_PHP_VERSION'" run.php | sed -E "s/.*'MIN_PHP_VERSION'[[:space:]]*=>[[:space:]]*'([0-9.]+)'.*/\1/")

echo "Plugin Version Check:"
echo "  index.php header:     $PLUGIN_VERSION"
echo "  run.php constant:     $RUN_VERSION"
echo "  CHANGELOG.md:         $CHANGELOG_VERSION"
echo ""
echo "PHP Requirement Check:"
echo "  index.php:            $PHP_INDEX"
echo "  composer.json:        $PHP_COMPOSER"
echo "  run.php constant:     $PHP_RUN"
echo ""

# Validate plugin versions
ERRORS=0

if [ "$PLUGIN_VERSION" != "$RUN_VERSION" ]; then
    echo "❌ Plugin version mismatch between index.php and run.php"
    ERRORS=$((ERRORS + 1))
fi

if [ "$PLUGIN_VERSION" != "$CHANGELOG_VERSION" ]; then
    echo "❌ Plugin version mismatch between index.php and CHANGELOG.md"
    ERRORS=$((ERRORS + 1))
fi

# Validate PHP versions (allow minor version differences like 8.1 vs 8.1.0)
PHP_INDEX_MAJOR=$(echo "$PHP_INDEX" | cut -d. -f1-2)
PHP_COMPOSER_MAJOR=$(echo "$PHP_COMPOSER" | cut -d. -f1-2)

if [ "$PHP_INDEX_MAJOR" != "$PHP_COMPOSER_MAJOR" ]; then
    echo "❌ PHP version mismatch between index.php and composer.json"
    ERRORS=$((ERRORS + 1))
fi

if [ "$PHP_INDEX" != "$PHP_RUN" ]; then
    echo "❌ PHP version mismatch between index.php and run.php"
    ERRORS=$((ERRORS + 1))
fi

# Check for uncommitted changes (warning only, not error)
if ! git diff-index --quiet HEAD --; then
    echo "⚠️  Warning: You have uncommitted changes"
fi

# Final result
echo ""
if [ $ERRORS -eq 0 ]; then
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo "✓ All versions are consistent"
    echo "✓ Ready to tag: v$PLUGIN_VERSION"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo ""
    echo "Next steps:"
    echo "  git commit -m \"Release v$PLUGIN_VERSION\""
    echo "  git tag v$PLUGIN_VERSION"
    echo "  git push && git push --tags"
    echo ""
    exit 0
else
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo "❌ Found $ERRORS error(s)"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo ""
    exit 1
fi
