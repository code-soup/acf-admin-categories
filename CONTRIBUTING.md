# Contributing to ACF Admin Categories

Thank you for your interest in contributing to ACF Admin Categories! We welcome contributions from the community and are pleased to have them. This document outlines the guidelines and workflow for contributing to this project.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Setup](#development-setup)
- [Making Changes](#making-changes)
- [Submitting Changes](#submitting-changes)
- [Coding Standards](#coding-standards)
- [Release Process](#release-process)

## Code of Conduct

This project and everyone participating in it is governed by our Code of Conduct. By participating, you are expected to uphold this code. Please report unacceptable behavior to [abuse@codesoup.co](mailto:abuse@codesoup.co).

### Our Standards

- **Be respectful**: Treat everyone with respect and kindness
- **Be inclusive**: Welcome developers of all experience levels
- **Be constructive**: Provide helpful feedback and suggestions
- **Be collaborative**: Work together towards common goals

## Getting Started

### Prerequisites

- PHP 8.0 or higher
- Composer
- Node.js 18+ and Yarn
- WordPress 6.0+ development environment
- Advanced Custom Fields plugin (Free or Pro)

### Issues and Feature Requests

1. **Search existing issues** before creating new ones
2. **Use issue templates** when available
3. **Provide detailed information** including:
   - WordPress version
   - PHP version
   - ACF version
   - Plugin version
   - Steps to reproduce (for bugs)
   - Expected vs actual behavior

## Development Setup

### 1. Fork and Clone

```bash
# Fork the repository on GitHub, then clone your fork
git clone https://github.com/YOUR-USERNAME/acf-admin-categories.git
cd acf-admin-categories

# Add the original repository as upstream
git remote add upstream https://github.com/code-soup/acf-admin-categories.git
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
yarn install
```

### 3. Development Environment

Set up a local WordPress development environment with:
- WordPress 6.0+
- PHP 8.0+
- ACF plugin installed and activated

### 4. Build Assets

```bash
# Development build with watch
yarn dev

# Production build
yarn build
```

## Making Changes

### 1. Create a Branch

```bash
# Update your fork
git fetch upstream
git checkout main
git merge upstream/main

# Create a feature branch
git checkout -b feature/your-feature-name
# or for bug fixes
git checkout -b fix/issue-description
```

### 2. Development Guidelines

#### File Structure

- **PHP Classes**: Use PSR-4 autoloading in `includes/` directory
- **Assets**: Source files in `src/`, compiled files in `dist/`
- **Templates**: PHP templates in `templates/` directory

#### Naming Conventions

- **Classes**: PascalCase (`ACF_Setup`, `CategoryManager`)
- **Files**: kebab-case with appropriate prefixes
  - Classes: `class-acf-setup.php`
  - Interfaces: `category-interface.php`
  - Traits: `helpers-trait.php`
- **Methods**: camelCase (`getCategoryCount`)
- **Variables**: snake_case (`$category_id`)
- **Constants**: SCREAMING_SNAKE_CASE (`PLUGIN_VERSION`)

#### WordPress Standards

- Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- Use WordPress hooks and filters appropriately
- Sanitize, validate, and escape all user input
- Use proper nonces for security
- Follow WordPress database best practices

## Submitting Changes

### 1. Code Quality Checks

Before submitting, ensure your code passes all checks:

```bash
# Lint PHP files
composer lint

# Check coding standards
composer phpcs

# Fix coding standards issues
composer phpcbf

# Run all CI checks
composer ci
```

### 2. Commit Guidelines

Use conventional commit messages:

```bash
# Format: type(scope): description
git commit -m "feat(admin): add category filtering interface"
git commit -m "fix(autoloader): resolve underscore class naming"
git commit -m "docs(readme): update installation instructions"
```

**Types:**
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes
- `refactor`: Code refactoring
- `chore`: Maintenance tasks

### 3. Create Pull Request

1. **Push your branch**: `git push origin feature/your-feature-name`
2. **Create PR** on GitHub
3. **Fill out the PR template** completely
4. **Link related issues** using keywords (fixes #123)

### PR Requirements

- [ ] Code follows WordPress coding standards
- [ ] Documentation is updated
- [ ] Backward compatibility is maintained
- [ ] Security considerations are addressed

## Coding Standards

### PHP Standards

- **PSR-12** extended coding standard
- **WordPress Coding Standards** (WPCS)
- **PHPCompatibility** for PHP version compatibility

### JavaScript/CSS Standards

- **ESLint** for JavaScript
- **Stylelint** for CSS/SCSS
- **Prettier** for code formatting

### Documentation Standards

- **PHPDoc** for all PHP classes and methods
- **JSDoc** for JavaScript functions
- **Inline comments** for complex logic
- **README updates** for new features

## Security

### Reporting Security Issues

**DO NOT** report security vulnerabilities through public GitHub issues.

Instead, email security@codesoup.co with:
- Description of the vulnerability
- Steps to reproduce
- Potential impact
- Suggested fix (if any)

### Security Best Practices

- Always sanitize user input
- Validate data before processing
- Use WordPress nonces for form submissions
- Escape output appropriately
- Follow principle of least privilege
- Keep dependencies updated

## Release Process

### Version Numbering

We follow [Semantic Versioning](https://semver.org/):
- **MAJOR**: Breaking changes
- **MINOR**: New features (backward compatible)
- **PATCH**: Bug fixes (backward compatible)

### Release Steps

1. Update version in all relevant files
2. Update CHANGELOG.md
3. Create release branch
4. Final testing
5. Merge to main
6. Create GitHub release
7. Publish to WordPress.org (if applicable)

## Recognition

Contributors will be:
- Listed in the CHANGELOG.md
- Credited in release notes
- Added to the contributors list (with permission)

## Questions?

- **Documentation**: Check the [README](README.md) first
- **Discussions**: Use GitHub Discussions for questions

Thank you for contributing to ACF Admin Categories! 🎉 