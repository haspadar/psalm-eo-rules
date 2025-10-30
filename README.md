# Psalm EO Rules

[![CI](https://github.com/haspadar/psalm-eo-rules/actions/workflows/ci.yml/badge.svg)](https://github.com/haspadar/psalm-eo-rules/actions/workflows/ci.yml)
[![Code Style](https://img.shields.io/badge/Code%20Style-PSR--12-blue)](https://github.com/FriendsOfPHP/PHP-CS-Fixer)

---

## 📦 About

**Psalm EO Rules** is a custom [Psalm](https://psalm.dev) plugin that encodes the coding principles
from [Elegant Objects](https://www.yegor256.com/elegant-objects.html). Each rule is implemented as a Psalm issue so that
violations are visible directly in static analysis reports.

The plugin focuses on:

- eliminating static state and global helpers
- keeping objects immutable and free of `null`
- preventing ad-hoc inheritance hierarchies
- reinforcing interface-driven design and clear responsibilities

---

## ⚙️ Installation

```bash
composer require --dev haspadar/psalm-eo-rules
```

Then register the plugin in `psalm.xml`:

```xml

<psalm>
    <plugins>
        <pluginClass class="Haspadar\PsalmEoRules\Plugin" />
    </plugins>
</psalm>
```

Requires PHP 8.1.

> Tip: Rules that support inline suppression are marked in the table below. Use `@psalm-suppress IssueName` sparingly
> when you need an exception.

---

## 🧭 Rules

| Issue code                  | What triggers it                                                                                                      | EO rationale                                                                         | Suppressible                                      |
|-----------------------------|-----------------------------------------------------------------------------------------------------------------------|--------------------------------------------------------------------------------------|---------------------------------------------------|
| `NoStaticMethodDeclaration` | Declaring a `static function`                                                                                         | Static helpers break encapsulation and state consistency                             | ✅ via `@psalm-suppress NoStaticMethodDeclaration` |
| `NoStaticProperty`          | Declaring or reading a static property                                                                                | Shared state hides dependencies and makes objects unpredictable                      | ✅ via `@psalm-suppress NoStaticProperty`          |
| `NoMutableProperty`         | Property declared without the `readonly` flag                                                                         | Objects must be immutable after construction                                         | ✅ via `@psalm-suppress NoMutableProperty`         |
| `NoNullableType`            | Method parameter typed as `?Type`                                                                                     | Optional behaviour should be modelled with dedicated objects (Optional, Null Object) | ✅ via `@psalm-suppress NoNullableType`            |
| `NoNullLiteral`             | Using the `null` literal in expressions                                                                               | `null` represents absence and breaks object integrity                                | ✅ via `@psalm-suppress NoNullLiteral`             |
| `NoIsset`                   | Calling `isset()`                                                                                                     | Weakens type guarantees; prefer explicit comparisons                                 | ✅ via `@psalm-suppress NoIsset`                   |
| `NoEmpty`                   | Calling `empty()`                                                                                                     | Masks intent and hides the data contract of an object                                | ✅ via `@psalm-suppress NoEmpty`                   |
| `NonFinalOrAbstractClass`   | Class that is neither `final` nor `abstract`                                                                          | Every class must either be closed for inheritance or explicitly extensible           | ✅ via `@psalm-suppress NonFinalOrAbstractClass`   |
| `NoInterfaceImplementation` | Concrete class without any implemented interface                                                                      | Keeps polymorphism explicit and substitution possible                                | ✅ via `@psalm-suppress NoInterfaceImplementation` |
| `NoClassExtends`            | Inheriting from a class outside the allowed list (only exception hierarchies or `final`→`abstract`)                   | Prevents ad-hoc inheritance; use composition instead                                 | ✅ via `@psalm-suppress NoClassExtends`            |
| `NoTraitUsage`              | Using a trait in a class                                                                                              | Traits blur object boundaries; prefer composition or delegation                      | ✅ via `@psalm-suppress NoTraitUsage`              |
| `NoConstructorException`    | `throw` statements inside a constructor                                                                               | Constructors must not fail; delegate validation to factories or helpers              | ✅ via `@psalm-suppress NoConstructorException`    |
| `NoNewInsideMethod`         | `new` inside methods/constructors (except for exceptions, `return new`, parent constructor calls, or local variables) | Object creation belongs to dedicated factories                                       | ✅ via `@psalm-suppress NoNewInsideMethod`         |
| `NoProtected`               | Protected methods or properties                                                                                       | Without inheritance there is no legitimate protected state                           | ✅ via `@psalm-suppress NoProtected`               |

---

## 📄 License

[MIT](LICENSE)
