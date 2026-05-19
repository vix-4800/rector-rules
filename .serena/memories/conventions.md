# Conventions

- Every PHP file uses `declare(strict_types=1);`.
- Rule classes are final, namespace `Vix\RectorRules`, extend `Rector\Rector\AbstractRector`.
- Tests are final PHPUnit classes under `Vix\RectorRules\Tests`, extend `AbstractRuleTestCase`, use inline heredoc fixtures via `doTestCode()`.
- New rules exported by adding `use` + class entry to `config/rules.php`.
- Existing rules often keep helper methods private and return `null` for no-change cases.