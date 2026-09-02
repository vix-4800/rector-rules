<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Vix\RectorRules\AddTypedClassConstantRector;
use Vix\RectorRules\CollapseSequentialStrReplaceRector;
use Vix\RectorRules\ExtractAssignmentFromIfConditionRector;
use Vix\RectorRules\LegacyRector\CountArrayToEmptyArrayComparisonRector;
use Vix\RectorRules\LegacyRector\NestedTernaryToMatchRector;
use Vix\RectorRules\LegacyRector\ReplaceTestFunctionPrefixWithAttributeRector;
use Vix\RectorRules\NullableBoolReturnToFalseRector;
use Vix\RectorRules\ReplaceMultipleEqualWithInArrayRector;
use Vix\RectorRules\TernaryNullCheckToNullsafeOperatorRector;
use Vix\RectorRules\Yii2\Yii2AddRelationQueryGenericRector;
use Vix\RectorRules\Yii2\Yii2AddPropertyTagsRector;
use Vix\RectorRules\Yii2\Yii2FindAllIdShortcutRector;
use Vix\RectorRules\Yii2\Yii2FindOneFindAllShortcutRector;
use Vix\RectorRules\Yii2\Yii2FindOneIdShortcutRector;
use Vix\RectorRules\Yii2\Yii2MergeModelRulesRector;
use Vix\RectorRules\Yii2\Yii2PropertyAccessRector;
use Vix\RectorRules\Yii2\Yii2RedundantActiveRecordSelfLookupRector;
use Vix\RectorRules\Yii2\Yii2UseExistsInsteadOfCountRector;
use Vix\RectorRules\Yii2\Yii2UseExistsInsteadOfOneNotNullRector;
use Vix\RectorRules\Yii2\Yii2UserFindOneToIdentityRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rules([
        AddTypedClassConstantRector::class,
        CollapseSequentialStrReplaceRector::class,
        CountArrayToEmptyArrayComparisonRector::class,
        ExtractAssignmentFromIfConditionRector::class,
        NestedTernaryToMatchRector::class,
        NullableBoolReturnToFalseRector::class,
        ReplaceMultipleEqualWithInArrayRector::class,
        ReplaceTestFunctionPrefixWithAttributeRector::class,
        TernaryNullCheckToNullsafeOperatorRector::class,
        Yii2AddRelationQueryGenericRector::class,
        Yii2AddPropertyTagsRector::class,
        Yii2FindAllIdShortcutRector::class,
        Yii2FindOneFindAllShortcutRector::class,
        Yii2FindOneIdShortcutRector::class,
        Yii2MergeModelRulesRector::class,
        Yii2PropertyAccessRector::class,
        Yii2RedundantActiveRecordSelfLookupRector::class,
        Yii2UseExistsInsteadOfCountRector::class,
        Yii2UseExistsInsteadOfOneNotNullRector::class,
        Yii2UserFindOneToIdentityRector::class,
    ]);
};
