# Suggested Commands

- `composer test` / `npm test` not used; Composer script is canonical for this PHP package.
- `composer test -- --filter RuleNameTest` runs focused PHPUnit tests through project script.
- `composer static-analysis` runs PHPStan.
- `vendor/bin/phpunit --filter RuleNameTest` useful when bypassing Composer script output/colors.