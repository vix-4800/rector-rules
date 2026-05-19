# Core

- PHP library of custom Rector 2 rules under `src/`; tests mirror rule names under `tests/`.
- Package config entrypoint: `config/rules.php`; add new exported rule classes there.
- Test harness: `tests/AbstractRuleTestCase.php` creates temp inline `.php.inc` fixtures and enables one rule per test class.
- See `mem:tech_stack` for runtime/tool pins, `mem:conventions` for rule/test style, `mem:suggested_commands` for local commands, `mem:task_completion` for completion gates.