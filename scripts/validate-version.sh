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

# WordPress requirements (major.minor)
WP_REQUIRES_INDEX=$(grep -E 'Requires at least:' index.php | sed -E 's/.*Requires at least:[[:space:]]*([0-9.]+).*/\1/')
WP_TESTED_INDEX=$(grep -E 'Tested up to:' index.php | sed -E 's/.*Tested up to:[[:space:]]*([0-9.]+).*/\1/')
WP_REQUIRES_RUN=$(grep -E "'MIN_WP_VERSION'" run.php | head -1 | sed -E "s/.*'MIN_WP_VERSION'[[:space:]]*=>[[:space:]]*'([0-9.]+)'.*/\1/")
WP_TESTED_RUN=$(grep -E "'TESTED_WP_VERSION'" run.php | sed -E "s/.*'TESTED_WP_VERSION'[[:space:]]*=>[[:space:]]*'([0-9.]+)'.*/\1/")

README_STABLE=""
README_WP_REQUIRES=""
README_WP_TESTED=""
README_PHP=""
if [ -f readme.txt ]; then
	README_STABLE=$(grep -E '^Stable tag:' readme.txt | sed -E 's/^Stable tag:[[:space:]]*([0-9.]+).*/\1/')
	README_WP_REQUIRES=$(grep -E '^Requires at least:' readme.txt | sed -E 's/^Requires at least:[[:space:]]*([0-9.]+).*/\1/')
	README_WP_TESTED=$(grep -E '^Tested up to:' readme.txt | sed -E 's/^Tested up to:[[:space:]]*([0-9.]+).*/\1/')
	README_PHP=$(grep -E '^Requires PHP:' readme.txt | sed -E 's/^Requires PHP:[[:space:]]*([0-9.]+).*/\1/')
fi

echo "Plugin Version Check:"
echo "  index.php header:     $PLUGIN_VERSION"
echo "  run.php constant:     $RUN_VERSION"
echo "  CHANGELOG.md:         $CHANGELOG_VERSION"
if [ -n "$README_STABLE" ]; then
	echo "  readme.txt stable:    $README_STABLE"
fi
echo ""
echo "PHP Requirement Check:"
echo "  index.php:            $PHP_INDEX"
echo "  composer.json:        $PHP_COMPOSER"
echo "  run.php constant:     $PHP_RUN"
if [ -n "$README_PHP" ]; then
	echo "  readme.txt:           $README_PHP"
fi
echo ""
echo "WordPress Requirement Check:"
echo "  index.php requires:   $WP_REQUIRES_INDEX"
echo "  index.php tested:     $WP_TESTED_INDEX"
echo "  run.php requires:     $WP_REQUIRES_RUN"
echo "  run.php tested:       $WP_TESTED_RUN"
if [ -n "$README_WP_REQUIRES" ]; then
	echo "  readme.txt requires:  $README_WP_REQUIRES"
	echo "  readme.txt tested:    $README_WP_TESTED"
fi
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

if [ -n "$README_STABLE" ] && [ "$PLUGIN_VERSION" != "$README_STABLE" ]; then
	echo "❌ Plugin version mismatch between index.php and readme.txt Stable tag"
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

if [ -n "$README_PHP" ] && [ "$PHP_INDEX" != "$README_PHP" ]; then
	echo "❌ PHP version mismatch between index.php and readme.txt"
	ERRORS=$((ERRORS + 1))
fi

if [ "$WP_REQUIRES_INDEX" != "$WP_REQUIRES_RUN" ]; then
	echo "❌ WordPress minimum mismatch between index.php and run.php"
	ERRORS=$((ERRORS + 1))
fi

if [ "$WP_TESTED_INDEX" != "$WP_TESTED_RUN" ]; then
	echo "❌ WordPress tested mismatch between index.php and run.php"
	ERRORS=$((ERRORS + 1))
fi

if [ -n "$README_WP_REQUIRES" ] && [ "$WP_REQUIRES_INDEX" != "$README_WP_REQUIRES" ]; then
	echo "❌ WordPress minimum mismatch between index.php and readme.txt"
	ERRORS=$((ERRORS + 1))
fi

if [ -n "$README_WP_TESTED" ] && [ "$WP_TESTED_INDEX" != "$README_WP_TESTED" ]; then
	echo "❌ WordPress tested mismatch between index.php and readme.txt"
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
