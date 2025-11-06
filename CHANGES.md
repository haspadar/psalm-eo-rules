# Changelog

## [0.3.0] – 2025-11-06
### Changed
- Bumped PHP requirement to **8.2+**
- Switched Dockerfile base image to `php:8.2-cli`
- Updated Rector and other dev tools for PHP 8.2 compatibility
- `NoMutablePropertyChecker` now respects `readonly` classes

### Removed
- `NoNewInsideMethodChecker` rule and its tests
- README entry and documentation references to the removed rule

### Fixed
- False positives on immutable (`readonly`) classes in property mutability checks

### Internal
- Modernized CI configuration
- Aligned Psalm, PHPStan, Rector, and PHPUnit versions

## [0.2.0] – 2025-11-03
### Added
- Support for **Psalm 6.x** API (updated interfaces and event handlers)
- Inline `@psalm-suppress` detection restored for `NoStaticPropertyChecker`
- CI workflow modernized: PHPStan, Psalm, PHPUnit, Rector only
- Documentation cleanup and badge updates in README

### Removed
- Codecov integration and coverage reports
- Xdebug from Dockerfile and CI pipeline

### Fixed
- Composer `platform` conflicts with Psalm version constraints
- CI stability on PHP 8.1 runner

---

## [0.1.0] – 2025-10-30
Initial release with full set of EO-rules:
`NoStaticMethodDeclaration`, `NoStaticProperty`, `NoMutableProperty`,
`NoNullableType`, `NoNullLiteral`, `NoIsset`, `NoEmpty`, `NoProtected`,
`NoTraitUsage`, `NoInterfaceImplementation`, `NonFinalOrAbstractClass`,
`NoConstructorException`, `NoNewInsideMethod`.