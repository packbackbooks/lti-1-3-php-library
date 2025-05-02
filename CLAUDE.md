# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Build/Test/Lint Commands
- Run all tests: `composer test`
- Run a single test: `vendor/bin/phpunit tests/path/to/TestFile.php --filter testMethodName`
- Run static analysis: `vendor/bin/phpstan analyse`
- Check code style: `composer lint`
- Fix code style issues: `composer lint-fix`

## Code Style Guidelines
- Follow PSR-1/PSR-2 coding standards (Laravel preset)
- Use PHP 8.1+ features and type hints
- Classes organized in the namespace `Packback\Lti1p3`
- Tests in the namespace `Tests`
- Document public methods with PHPDoc blocks
- Constants for error messages (use const ERR_* pattern)
- Use interfaces for dependency injection
- Error handling: Throw exceptions with descriptive messages
- Import all classes with use statements
- Use strict type checking (avoid loose comparisons)
- Use camelCase for method names and variables
- Line length: 80-120 characters