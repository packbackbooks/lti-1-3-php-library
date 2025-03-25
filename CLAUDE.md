# Claude Coding Guidelines

## Build Commands
- Test all: `composer test`
- Test single file: `./vendor/bin/phpunit tests/path/to/TestFile.php`
- Test specific method: `./vendor/bin/phpunit --filter=methodName tests/path/to/TestFile.php`
- Lint check: `composer lint`
- Fix linting: `composer lint-fix`

## Code Style
- PHP version: 8.1+
- Follow Laravel preset style (managed by Laravel Pint)
- Use PSR-4 autoloading (`Packback\Lti1p3` namespace)
- Type hints for parameters, properties, and return types
- Use interfaces for dependencies (ICache, ICookie, IDatabase)
- Prefer traits for shared functionality (Arrayable, JsonStringable)
- PHPStan level 5 for static analysis
- Error handling: throw LtiException for LTI-specific errors
- Naming: clear, descriptive names following camelCase for methods, PascalCase for classes
- Test coverage: maintain complete coverage for all features