# Rector Rules

Custom Rector rules package.

## Install

```bash
composer require --dev vix/rector-rules
```

## Usage

```php
<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withImportNames()
    ->withPaths([__DIR__ . '/src'])
    ->withSets([__DIR__ . '/vendor/vix/rector-rules/config/rules.php']);
```

## Rules

- `Vix\RectorRules\AddTypedClassConstantRector`
- `Vix\RectorRules\CollapseSequentialStrReplaceRector`
- `Vix\RectorRules\ExtractAssignmentFromIfConditionRector`
- `Vix\RectorRules\NullableBoolReturnToFalseRector`
- `Vix\RectorRules\ReplaceMultipleEqualWithInArrayRector`
- `Vix\RectorRules\Yii2FindAllIdShortcutRector`
- `Vix\RectorRules\Yii2FindOneFindAllShortcutRector`
- `Vix\RectorRules\Yii2FindOneIdShortcutRector`
- `Vix\RectorRules\Yii2PropertyAccessRector`
- `Vix\RectorRules\Yii2UseExistsInsteadOfCountRector`
- `Vix\RectorRules\Yii2UseExistsInsteadOfOneNotNullRector`
- `Vix\RectorRules\Yii2UserFindOneToIdentityRector`
