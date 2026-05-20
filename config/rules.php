<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Vix\RectorRules\AddTypedClassConstantRector;
use Vix\RectorRules\CollapseSequentialStrReplaceRector;
use Vix\RectorRules\ExtractAssignmentFromIfConditionRector;
use Vix\RectorRules\NullableBoolReturnToFalseRector;
use Vix\RectorRules\NullCoalescingToIssetRector;
use Vix\RectorRules\ReplaceMultipleEqualWithInArrayRector;
use Vix\RectorRules\Yii2FindAllIdShortcutRector;
use Vix\RectorRules\Yii2FindOneFindAllShortcutRector;
use Vix\RectorRules\Yii2FindOneIdShortcutRector;
use Vix\RectorRules\Yii2PropertyAccessRector;
use Vix\RectorRules\Yii2RedundantActiveRecordSelfLookupRector;
use Vix\RectorRules\Yii2UseExistsInsteadOfCountRector;
use Vix\RectorRules\Yii2UseExistsInsteadOfOneNotNullRector;
use Vix\RectorRules\Yii2UserFindOneToIdentityRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rules([
        AddTypedClassConstantRector::class,
        CollapseSequentialStrReplaceRector::class,
        ExtractAssignmentFromIfConditionRector::class,
        NullableBoolReturnToFalseRector::class,
        NullCoalescingToIssetRector::class,
        ReplaceMultipleEqualWithInArrayRector::class,
        Yii2FindAllIdShortcutRector::class,
        Yii2FindOneFindAllShortcutRector::class,
        Yii2FindOneIdShortcutRector::class,
        Yii2PropertyAccessRector::class,
        Yii2RedundantActiveRecordSelfLookupRector::class,
        Yii2UseExistsInsteadOfCountRector::class,
        Yii2UseExistsInsteadOfOneNotNullRector::class,
        Yii2UserFindOneToIdentityRector::class,
    ]);
};
