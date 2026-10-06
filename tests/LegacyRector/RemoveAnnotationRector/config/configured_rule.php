<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Vix\RectorRules\LegacyRector\RemoveAnnotationRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig
        ->ruleWithConfiguration(RemoveAnnotationRector::class, ['method', 'JMS\DiExtraBundle\Annotation\InjectParams']);
};
