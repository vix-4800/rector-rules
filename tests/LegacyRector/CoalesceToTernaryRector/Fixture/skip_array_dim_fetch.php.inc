<?php

namespace Vix\RectorRules\Tests\LegacyRector\CoalesceToTernaryRector\Fixture;

final class SkipArrayDimFetch
{
    public function run(array $data)
    {
        return $data['key'] ?? 'test';
    }
}
