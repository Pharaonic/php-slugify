# Contributing

Contributions are welcome. Please follow the workflow below before opening a pull request.

## Supported PHP versions

Each release line targets exactly one PHP version, and the branch name tells you which:

```text
8.0.x → PHP 8.0
8.1.x → PHP 8.1
8.2.x → PHP 8.2
...
```

Code on a branch must not use syntax or functions introduced after that branch's PHP version.

## Setup

Fork the repository, then clone your fork:

```bash
git clone https://github.com/YOUR_USERNAME/php-slugify.git
cd php-slugify
composer install
```

Add the original repository as `upstream`:

```bash
git remote add upstream https://github.com/Pharaonic/php-slugify.git
```

## Choose the target branch

Check out the branch that matches the PHP version you are targeting:

```bash
git checkout 8.2.x
git pull upstream 8.2.x
```

## Create a working branch

```bash
git checkout -b fix/acronym-splitting
```

Recommended prefixes:

```text
feature/
fix/
refactor/
test/
docs/
```

## Make your changes

- Keep each change focused.
- Add or update tests for behavior changes. Slug output changes need a regression entry in `tests/Fixtures/regression.php`.
- Preserve backward compatibility whenever possible. Document any output change in `UPGRADE.md`.
- Update `/docs` when the public API or usage changes.
- Add an entry to `CHANGELOG.md` under `Unreleased`.
- Do not introduce breaking changes without discussing them in an issue first.

## Run checks

```bash
composer test      # PHPUnit
composer analyse   # PHPStan (level 9)
composer lint      # PHP_CodeSniffer (PSR-12); `composer format` fixes most issues
composer check     # all of the above
composer validate --strict
```

All checks must pass. CI also runs the tests with the lowest allowed dependency versions and without `ext-intl`.

## Push and open a pull request

```bash
git push origin fix/acronym-splitting
```

Open the pull request against the version branch you started from, and fill in the pull request template. Do not submit the same change to multiple version branches unless asked to.

## Code of Conduct

This project follows the [Code of Conduct](CODE_OF_CONDUCT.md).

## Security

Do not report security vulnerabilities through public issues. See [SECURITY.md](SECURITY.md).
