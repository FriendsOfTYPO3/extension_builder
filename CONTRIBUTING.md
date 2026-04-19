## Contributing

Feel free to create new pull requests on GitHub.

### Commit Messages

We follow the [TYPO3 Commit Message Guidelines](https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/Appendix/CommitMessage.html). Every commit message must start with a type prefix:

| Prefix | Usage |
|--------|-------|
| `[BUGFIX]` | Bug fixes |
| `[FEATURE]` | New features |
| `[TASK]` | Refactoring, improvements, maintenance |
| `[DOCS]` | Documentation changes |
| `[SECURITY]` | Security fixes |
| `[RELEASE]` | Release-related changes |
| `[DEVBOX]` | Changes to the ddev devbox setup |

For breaking changes, prepend `[!!!]` before the type: `[!!!][FEATURE] Remove deprecated API`

**Rules:**
- Maximum line length: 72 characters
- Use imperative mood: "Fix bug" not "Fixed bug"
- Start with a capital letter after the prefix

**Examples:**
```
[BUGFIX] Fix extension key validation for underscores
[FEATURE] Add support for TYPO3 v14
[TASK] Update dependencies to latest versions
[DOCS] Add section about roundtrip mode
```

### Code Quality

All checks must pass before a PR can be merged. The CI pipeline runs automatically:

- **PHP CS Fixer** — code formatting
- **Rector** — automated refactoring rules
- **PHPStan** — static analysis
- **ESLint** — JavaScript linting
- **Prettier** — JavaScript/CSS formatting
- **Stylelint** — SCSS linting

Run all checks locally before submitting a PR:

```bash
# PHP linters (requires composer install first)
composer install
.Build/bin/php-cs-fixer fix --config=.php-cs-fixer.php -v --dry-run --stop-on-violation --using-cache=no
.Build/bin/rector process --dry-run
.Build/bin/phpstan analyse --no-progress

# JavaScript linters
pnpm install
pnpm lint

# Tests
.Build/bin/phpunit --colors -c .Build/vendor/typo3/testing-framework/Resources/Core/Build/UnitTests.xml Tests/Unit
```

### Pull Request Labels

Labels are assigned automatically based on the commit message prefix. A semantic label (bug, enhancement, maintenance, documentation, security) is required for merge.

### Devbox

If you don't have a setup already, we have included a [ddev](https://www.ddev.com) devbox to help with development.

#### Prerequisites

* [DDEV](https://www.ddev.com)
* Docker

#### How to use the devbox?

```bash
git clone git@github.com:FriendsOfTYPO3/extension_builder.git
cd .devbox
ddev start
```

Username/password: `admin`/`password`

**Note:** xdebug is disabled by default to improve performance. Enable it with `ddev xdebug on`.
