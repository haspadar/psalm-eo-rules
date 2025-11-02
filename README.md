# Psalm EO Rules

[![PHP Version](https://img.shields.io/badge/PHP-8.1-blue)](https://www.php.net/releases/8.1/)
[![CI](https://github.com/haspadar/psalm-eo-rules/actions/workflows/ci.yml/badge.svg)](https://github.com/haspadar/psalm-eo-rules/actions/workflows/ci.yml)
[![Coverage](https://codecov.io/gh/haspadar/psalm-eo-rules/branch/main/graph/badge.svg)](https://codecov.io/gh/haspadar/psalm-eo-rules)

---

## 📦 About

**Psalm EO Rules** is a [Psalm](https://psalm.dev) plugin that codifies the principles from
[Elegant Objects](https://www.yegor256.com/elegant-objects.html). Every rule enforces immutability, explicit composition, and clear ownership.

The plugin focuses on eliminating hidden shared state, encouraging immutable objects, and making composition and
contracts explicit.

---

## ⚙️ Installation

```bash
composer require --dev haspadar/psalm-eo-rules
```

Enable the plugin in `psalm.xml`:

```xml

<psalm>
    <plugins>
        <pluginClass class="Haspadar\PsalmEoRules\Plugin"/>
    </plugins>
</psalm>
```

Requirements: PHP 8.1+ and Psalm 5.25 or newer.

> Suppressions are supported for every rule listed below. Use `@psalm-suppress IssueName` sparingly when a deviation is
> intentional.

---

## 🧭 Rules

| Issue code                  | Trigger                                                                                                 | EO rationale                                                                           |
|-----------------------------|---------------------------------------------------------------------------------------------------------|----------------------------------------------------------------------------------------|
| `NoStaticMethodDeclaration` | Declaring a `static function`                                                                           | Static helpers break encapsulation and make behaviour context dependent                |
| `NoStaticProperty`          | Declaring or reading a static property                                                                  | Shared state hides dependencies and produces hidden coupling                           |
| `NoMutableProperty`         | Property declared without the `readonly` flag                                                           | Objects should be immutable after construction                                         |
| `NoNullableType`            | Parameter typed as `?Type`                                                                              | Optional behaviour should be modelled explicitly (Optional, Null Object, Either, etc.) |
| `NoNullLiteral`             | Using the `null` literal in expressions                                                                 | `null` signals absence and erodes the object contract                                  |
| `NoIsset`                   | Calling `isset()`                                                                                       | Weakens type guarantees; be explicit about the expected shape                          |
| `NoEmpty`                   | Calling `empty()`                                                                                       | Masks intent and hides the data contract of an object                                  |
| `NonFinalOrAbstractClass`   | Class that is neither `final` nor `abstract`                                                            | Every class should either be closed for inheritance or clearly designed for extension  |
| `NoInterfaceImplementation` | Concrete class that does not implement any interface                                                    | Keeps polymorphism explicit and substitution possible                                  |
| `NoTraitUsage`              | Using traits in a class                                                                                 | Traits blur object boundaries; prefer composition or delegation                        |
| `NoConstructorException`    | `throw` statements inside a constructor                                                                 | Constructors must not fail; delegate validation to factories                           |
| `NoNewInsideMethod`         | `new` inside methods/constructors (allowed: exceptions, parent constructor calls, local temps, returns) | Object creation belongs to dedicated factories                                         |
| `NoProtected`               | Protected methods or properties                                                                         | Without subclassing there is no need for protected members                             |

---

## 🚀 Usage Tips

- Add selected suppressions near the expression or class you need to relax: `/** @psalm-suppress NoEmpty */`.
- Combine with Psalm baselines to track legacy violations separately from new code.
- Rules rely on Psalm’s standard autoloader; ensure Composer’s autoload file is available when Psalm runs.

---

## 🧪 Tests

Each rule has a dedicated PHPUnit suite that executes Psalm against curated fixtures and asserts on the reported
issues. Run everything locally with:

```bash
composer test
```

Additional helpers:

- `composer psalm` – run Psalm on the plugin itself
- `composer analyze` – run PHPStan
- `composer fix` – apply coding standards

The CI workflow mirrors these steps.

## 🧱 Local Development

To build the container with Xdebug enabled for local debugging:

```bash
docker compose build --build-arg INSTALL_XDEBUG=true
```

or, without Compose:

```bash
docker build --build-arg INSTALL_XDEBUG=true -t psalm-eo-rules-dev .
```

Then run:

```bash
docker run -it --rm -v $(pwd):/app psalm-eo-rules-dev
```

---

## 🤝 Contributing

1. Fork and branch from `main`.
2. Keep fixtures under `tests/Fixtures/<RuleName>`; positive and negative cases should be explicit.
3. Add a TestDox description for every scenario so failures read naturally in CI output.
4. Run `composer test` before submitting a PR.

---

## 📄 License

[MIT](LICENSE)
